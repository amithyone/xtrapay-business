<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\BusinessProfile;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PayVibeWebhookController extends Controller
{
    /**
     * Handle PayVibe webhook notifications
     * This endpoint receives payment notifications from PayVibe when a payment is completed
     */
    public function handleWebhook(Request $request)
    {
        try {
            Log::info('PayVibe Webhook: Received webhook', [
                'payload' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            // Validate webhook payload
            $reference = $request->input('reference') ?? $request->input('data.reference');
            $status = $request->input('status') ?? $request->input('data.status');
            $amount = $request->input('amount') ?? $request->input('data.amount');

            if (!$reference) {
                Log::error('PayVibe Webhook: Missing reference', [
                    'payload' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Missing reference'
                ], 400);
            }

            // Find transaction by reference
            $transaction = Transaction::where('reference', $reference)
                ->orWhere('metadata->payvibe_reference', $reference)
                ->first();

            if (!$transaction) {
                Log::warning('PayVibe Webhook: Transaction not found', [
                    'reference' => $reference,
                    'payload' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Check if payment is successful
            $isSuccessful = ($status === 'success' || $status === 'completed' || $status === 'paid');
            $wasPending = $transaction->status === 'pending';

            // Update transaction status if payment is successful
            if ($isSuccessful && $wasPending) {
                DB::beginTransaction();

                try {
                    // Update transaction status
                    $transaction->update([
                        'status' => 'success',
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'payvibe_webhook_received_at' => now()->toIso8601String(),
                            'payvibe_status' => $status,
                            'payvibe_amount' => $amount
                        ])
                    ]);

                    // Credit business profile
                    $businessProfile = $transaction->businessProfile;
                    if ($businessProfile) {
                        $businessProfile->increment('balance', $transaction->amount);

                        Log::info('PayVibe Webhook: Business profile credited', [
                            'transaction_id' => $transaction->id,
                            'business_profile_id' => $businessProfile->id,
                            'amount' => $transaction->amount,
                            'new_balance' => $businessProfile->balance
                        ]);
                    }

                    // Process savings collection if applicable
                    try {
                        $savingsService = app(\App\Services\SavingsCollectionService::class);
                        $savingsService->processTransaction($transaction);
                    } catch (\Exception $e) {
                        Log::warning('PayVibe Webhook: Failed to process savings collection', [
                            'transaction_id' => $transaction->id,
                            'error' => $e->getMessage()
                        ]);
                    }

                    DB::commit();

                    // Send notification to business
                    $this->notifyBusiness($transaction);

                    Log::info('PayVibe Webhook: Payment processed successfully', [
                        'transaction_id' => $transaction->id,
                        'reference' => $reference,
                        'amount' => $transaction->amount
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('PayVibe Webhook: Error processing payment', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Error processing payment: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                // Update status even if not successful (for failed payments)
                if ($status && $status !== $transaction->status) {
                    $transaction->update([
                        'status' => $status === 'failed' ? 'failed' : $transaction->status,
                        'metadata' => array_merge($transaction->metadata ?? [], [
                            'payvibe_webhook_received_at' => now()->toIso8601String(),
                            'payvibe_status' => $status
                        ])
                    ]);
                }

                Log::info('PayVibe Webhook: Transaction status updated (not successful or already processed)', [
                    'transaction_id' => $transaction->id,
                    'reference' => $reference,
                    'status' => $status,
                    'was_pending' => $wasPending
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'transaction_id' => $transaction->id
            ]);

        } catch (\Exception $e) {
            Log::error('PayVibe Webhook: Exception processing webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Send notification to business when payment is received
     */
    private function notifyBusiness(Transaction $transaction)
    {
        try {
            $businessProfile = $transaction->businessProfile;

            if (!$businessProfile) {
                Log::warning('PayVibe Webhook: Cannot notify - business profile not found', [
                    'transaction_id' => $transaction->id
                ]);
                return;
            }

            // Check if Telegram notification is configured
            if ($businessProfile->telegram_chat_id && $businessProfile->telegram_bot_token) {
                $siteName = $transaction->site ? $transaction->site->name : 'Direct Payment';
                
                $message = "🎉 PayVibe Payment Received!\n\n" .
                          "💰 Amount: ₦" . number_format($transaction->amount, 2) . "\n" .
                          "📝 Reference: {$transaction->reference}\n" .
                          "🏢 Site: {$siteName}\n" .
                          "📅 Date: " . $transaction->created_at->format('M d, Y H:i') . "\n" .
                          "💳 Payment Method: PayVibe\n" .
                          "✅ Status: Successful";

                Log::info('PayVibe Webhook: Sending Telegram notification', [
                    'transaction_id' => $transaction->id,
                    'business_profile_id' => $businessProfile->id,
                    'chat_id' => $businessProfile->telegram_chat_id
                ]);

                // Send Telegram notification
                $response = Http::post("https://api.telegram.org/bot{$businessProfile->telegram_bot_token}/sendMessage", [
                    'chat_id' => $businessProfile->telegram_chat_id,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);

                if ($response->ok() && $response->json('ok')) {
                    Log::info('PayVibe Webhook: Telegram notification sent successfully', [
                        'transaction_id' => $transaction->id,
                        'message_id' => $response->json('result.message_id')
                    ]);
                } else {
                    Log::error('PayVibe Webhook: Failed to send Telegram notification', [
                        'transaction_id' => $transaction->id,
                        'response' => $response->json()
                    ]);
                }
            } else {
                Log::info('PayVibe Webhook: Telegram notification skipped - not configured', [
                    'transaction_id' => $transaction->id,
                    'business_profile_id' => $businessProfile->id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('PayVibe Webhook: Error sending notification', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

