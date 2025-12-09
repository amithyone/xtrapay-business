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
            // This ensures we get the exact transaction for this reference
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

            // Verify transaction has required relationships
            if (!$transaction->site_id || !$transaction->business_profile_id) {
                Log::error('PayVibe Webhook: Transaction missing site or business profile', [
                    'transaction_id' => $transaction->id,
                    'reference' => $reference,
                    'site_id' => $transaction->site_id,
                    'business_profile_id' => $transaction->business_profile_id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Transaction configuration error'
                ], 500);
            }

            // Load site and business profile with validation
            $site = $transaction->site;
            $businessProfile = $transaction->businessProfile;

            if (!$site) {
                Log::error('PayVibe Webhook: Site not found for transaction', [
                    'transaction_id' => $transaction->id,
                    'site_id' => $transaction->site_id,
                    'reference' => $reference
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Site not found'
                ], 500);
            }

            if (!$businessProfile) {
                Log::error('PayVibe Webhook: Business profile not found for transaction', [
                    'transaction_id' => $transaction->id,
                    'business_profile_id' => $transaction->business_profile_id,
                    'reference' => $reference
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found'
                ], 500);
            }

            // Log transaction isolation details for verification
            Log::info('PayVibe Webhook: Transaction isolation verified', [
                'transaction_id' => $transaction->id,
                'reference' => $reference,
                'site_id' => $site->id,
                'site_name' => $site->name,
                'site_api_code' => $site->api_code,
                'business_profile_id' => $businessProfile->id,
                'business_name' => $businessProfile->business_name,
                'webhook_url' => $site->webhook_url ?? 'Not configured'
            ]);

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

                    // Credit business profile (already loaded and validated above)
                    $businessProfile->increment('balance', $transaction->amount);

                    Log::info('PayVibe Webhook: Business profile credited', [
                        'transaction_id' => $transaction->id,
                        'business_profile_id' => $businessProfile->id,
                        'business_name' => $businessProfile->business_name,
                        'amount' => $transaction->amount,
                        'new_balance' => $businessProfile->balance
                    ]);

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

                    // Send notifications to business
                    $this->notifyBusiness($transaction);
                    
                    // Send webhook notification to business's website if configured
                    $this->sendWebhookToBusiness($transaction);

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

    /**
     * Send webhook notification to business's website
     * This sends a POST request to the business's webhook_url when payment is received
     * 
     * IMPORTANT: This ensures each business/site only receives webhooks for their own transactions
     * - Transaction is linked to specific site_id and business_profile_id
     * - Site has unique webhook_url
     * - No cross-contamination between businesses
     */
    private function sendWebhookToBusiness(Transaction $transaction)
    {
        try {
            // Reload site to ensure we have latest data
            $site = Site::find($transaction->site_id);
            
            if (!$site) {
                Log::error('PayVibe Webhook: Cannot send webhook - site not found', [
                    'transaction_id' => $transaction->id,
                    'site_id' => $transaction->site_id,
                    'business_profile_id' => $transaction->business_profile_id
                ]);
                return;
            }

            // Verify site belongs to the transaction's business
            if ($site->business_profile_id !== $transaction->business_profile_id) {
                Log::error('PayVibe Webhook: Site-Business mismatch detected!', [
                    'transaction_id' => $transaction->id,
                    'transaction_business_id' => $transaction->business_profile_id,
                    'site_id' => $site->id,
                    'site_business_id' => $site->business_profile_id,
                    'reference' => $transaction->reference
                ]);
                // Don't send webhook if mismatch - security measure
                return;
            }

            // Check if site has webhook URL configured
            if (!$site->webhook_url) {
                Log::info('PayVibe Webhook: Webhook URL not configured for site', [
                    'transaction_id' => $transaction->id,
                    'site_id' => $site->id,
                    'site_name' => $site->name,
                    'business_profile_id' => $transaction->business_profile_id
                ]);
                return;
            }

            // Verify site is active
            if (!$site->is_active) {
                Log::warning('PayVibe Webhook: Site is not active, skipping webhook', [
                    'transaction_id' => $transaction->id,
                    'site_id' => $site->id,
                    'site_name' => $site->name
                ]);
                return;
            }

            // Prepare webhook payload
            $payload = [
                'event' => 'payment.received',
                'transaction' => [
                    'id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'external_id' => $transaction->external_id,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'payment_method' => $transaction->payment_method,
                    'customer_email' => $transaction->customer_email,
                    'customer_name' => $transaction->customer_name,
                    'description' => $transaction->description,
                    'created_at' => $transaction->created_at->toIso8601String(),
                    'updated_at' => $transaction->updated_at->toIso8601String(),
                ],
                'site' => [
                    'id' => $site->id,
                    'name' => $site->name,
                    'api_code' => $site->api_code,
                ],
                'metadata' => $transaction->metadata ?? [],
                'timestamp' => now()->toIso8601String(),
            ];

            // Log detailed isolation information
            Log::info('PayVibe Webhook: Sending webhook to business - Isolation verified', [
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'site_id' => $site->id,
                'site_name' => $site->name,
                'site_api_code' => $site->api_code,
                'business_profile_id' => $transaction->business_profile_id,
                'webhook_url' => $site->webhook_url,
                'amount' => $transaction->amount,
                'isolation_check' => 'PASSED - Site matches transaction business'
            ]);

            // Send webhook POST request
            $response = Http::timeout(10)->post($site->webhook_url, $payload);

            if ($response->successful()) {
                Log::info('PayVibe Webhook: Webhook sent successfully to business', [
                    'transaction_id' => $transaction->id,
                    'webhook_url' => $site->webhook_url,
                    'response_status' => $response->status()
                ]);
            } else {
                Log::warning('PayVibe Webhook: Webhook sent but received error response', [
                    'transaction_id' => $transaction->id,
                    'webhook_url' => $site->webhook_url,
                    'response_status' => $response->status(),
                    'response_body' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            // Don't fail the webhook processing if business webhook fails
            Log::error('PayVibe Webhook: Error sending webhook to business', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}

