<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Handle incoming transaction notifications from external sites
     * This endpoint receives notifications from sites like faddedsmm.com
     */
    public function receiveTransaction(Request $request)
    {
        try {
            Log::info('Webhook received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            // 1. Validate API Key from header
            $apiKey = $request->header('X-API-Key');
            if (!$apiKey) {
                Log::warning('Webhook rejected: No API key provided', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'API key required'
                ], 401);
            }

            // 2. Validate the incoming webhook data
            $validator = Validator::make($request->all(), [
                'site_api_code' => 'required|string',
                'reference' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'status' => 'required|in:pending,success,failed,abandoned',
                'payment_method' => 'nullable|string',
                'customer_email' => 'nullable|email',
                'customer_name' => 'nullable|string',
                'description' => 'nullable|string',
                'external_id' => 'nullable|string',
                'metadata' => 'nullable|array',
                'timestamp' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                Log::error('Webhook validation failed', [
                    'errors' => $validator->errors(),
                    'payload' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // 3. Find and validate the site by API code and API key
            $site = Site::where('api_code', $data['site_api_code'])
                       ->where('api_key', $apiKey)
                       ->where('is_active', true)
                       ->first();

            if (!$site) {
                Log::error('Site validation failed', [
                    'api_code' => $data['site_api_code'],
                    'api_key_provided' => $apiKey,
                    'ip' => $request->ip(),
                    'payload' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key or site not found'
                ], 403);
            }

            // 4. Optional: Validate IP address if site has allowed IPs configured
            if ($site->allowed_ips) {
                $allowedIPs = explode(',', $site->allowed_ips);
                $clientIP = $request->ip();
                
                if (!in_array($clientIP, $allowedIPs)) {
                    Log::warning('Webhook rejected: IP not allowed', [
                        'client_ip' => $clientIP,
                        'allowed_ips' => $allowedIPs,
                        'site' => $site->name
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'IP address not authorized'
                    ], 403);
                }
            }

            // Check if transaction already exists by reference and site
            $existingTransaction = Transaction::where('reference', $data['reference'])
                                            ->where('site_id', $site->id)
                                            ->first();

            if ($existingTransaction) {
                Log::info('Found existing transaction', [
                    'transaction_id' => $existingTransaction->id,
                    'reference' => $data['reference'],
                    'current_status' => $existingTransaction->status,
                    'new_status' => $data['status'],
                    'site' => $site->name
                ]);

                // Always update the transaction with new data, regardless of status change
                $oldStatus = $existingTransaction->status;
                $oldAmount = $existingTransaction->amount;
                
                $updateData = [
                    'status' => $data['status'],
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'payment_method' => $data['payment_method'] ?? $existingTransaction->payment_method,
                    'customer_email' => $data['customer_email'] ?? $existingTransaction->customer_email,
                    'customer_name' => $data['customer_name'] ?? $existingTransaction->customer_name,
                    'description' => $data['description'] ?? $existingTransaction->description,
                    'external_id' => $data['external_id'] ?? $existingTransaction->external_id,
                    'metadata' => array_merge($existingTransaction->metadata ?? [], $data['metadata'] ?? []),
                    'updated_at' => $data['timestamp'] ?? now(),
                ];

                $existingTransaction->update($updateData);

                Log::info('Transaction updated', [
                    'transaction_id' => $existingTransaction->id,
                    'reference' => $data['reference'],
                    'old_status' => $oldStatus,
                    'new_status' => $data['status'],
                    'old_amount' => $oldAmount,
                    'new_amount' => $data['amount'],
                    'site' => $site->name
                ]);

                // Credit business profile if status changed to success
                if ($data['status'] === 'success' && $oldStatus !== 'success') {
                    $this->creditBusinessProfile($site, $data['amount']);
                    
                    Log::info('Business profile credited for successful transaction', [
                        'transaction_id' => $existingTransaction->id,
                        'reference' => $data['reference'],
                        'amount' => $data['amount'],
                        'site' => $site->name
                    ]);
                    
                    // Process savings collection for successful transactions
                    try {
                        $savingsService = app(\App\Services\SavingsCollectionService::class);
                        $savingsService->processTransaction($existingTransaction);
                    } catch (\Exception $e) {
                        Log::warning('Failed to process savings collection', [
                            'transaction_id' => $existingTransaction->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    // Send Telegram notification for successful transaction
                    $this->sendTelegramNotification($existingTransaction);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction updated successfully',
                    'transaction_id' => $existingTransaction->id,
                    'action' => 'updated',
                    'old_status' => $oldStatus,
                    'new_status' => $data['status']
                ]);
            }

            // Create new transaction (no existing transaction found)
            Log::info('Creating new transaction - no existing transaction found', [
                'reference' => $data['reference'],
                'site_id' => $site->id,
                'site_name' => $site->name,
                'status' => $data['status'],
                'amount' => $data['amount']
            ]);

            $transaction = Transaction::create([
                'site_id' => $site->id,
                'business_profile_id' => $site->business_profile_id,
                'reference' => $data['reference'],
                'external_id' => $data['external_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'status' => $data['status'],
                'payment_method' => $data['payment_method'] ?? 'unknown',
                'customer_email' => $data['customer_email'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'description' => $data['description'] ?? 'Transaction from ' . $site->name,
                'metadata' => $data['metadata'] ?? [],
                'created_at' => $data['timestamp'] ?? now(),
                'updated_at' => $data['timestamp'] ?? now(),
            ]);

            Log::info('New transaction created successfully', [
                'transaction_id' => $transaction->id,
                'reference' => $data['reference'],
                'amount' => $data['amount'],
                'status' => $data['status'],
                'site' => $site->name
            ]);

            // Credit business profile if transaction is successful
            if ($data['status'] === 'success') {
                $this->creditBusinessProfile($site, $data['amount']);
                
                Log::info('Business profile credited for new successful transaction', [
                    'transaction_id' => $transaction->id,
                    'reference' => $data['reference'],
                    'amount' => $data['amount'],
                    'site' => $site->name
                ]);
                
                // Process savings collection for successful transactions
                try {
                    $savingsService = app(\App\Services\SavingsCollectionService::class);
                    $savingsService->processTransaction($transaction);
                } catch (\Exception $e) {
                    Log::warning('Failed to process savings collection', [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                // Send Telegram notification for successful transaction
                $this->sendTelegramNotification($transaction);
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'transaction_id' => $transaction->id,
                'action' => 'created'
            ]);

        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
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
     * Credit the business profile balance
     */
    private function creditBusinessProfile(Site $site, $amount)
    {
        try {
            $businessProfile = $site->businessProfile;
            if ($businessProfile) {
                $businessProfile->increment('balance', $amount);
                
                Log::info('Business profile credited', [
                    'business_profile_id' => $businessProfile->id,
                    'amount' => $amount,
                    'new_balance' => $businessProfile->balance,
                    'site' => $site->name
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to credit business profile', [
                'error' => $e->getMessage(),
                'site_id' => $site->id,
                'amount' => $amount
            ]);
        }
    }

    /**
     * Handle webhook verification (for payment gateways that require verification)
     */
    public function verifyWebhook(Request $request)
    {
        // This method can be used for webhook verification if needed
        return response()->json([
            'success' => true,
            'message' => 'Webhook verified'
        ]);
    }

    /**
     * Test webhook endpoint
     */
    public function testWebhook(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint is working',
            'timestamp' => now(),
            'received_data' => $request->all()
        ]);
    }

    /**
 * Send Telegram notification for successful transaction
 */
private function sendTelegramNotification(Transaction $transaction)
{
    try {
        $businessProfile = $transaction->businessProfile;

        // Check if bot token and chat ID are set
        if (!$businessProfile || !$businessProfile->telegram_chat_id || !$businessProfile->telegram_bot_token) {
            Log::info('⚠️ TELEGRAM NOTIFICATION SKIPPED - No chat ID or bot token configured', [
                'transaction_id' => $transaction->id,
                'business_profile_id' => $businessProfile ? $businessProfile->id : null,
                'has_chat_id' => $businessProfile ? !empty($businessProfile->telegram_chat_id) : false,
                'has_bot_token' => $businessProfile ? !empty($businessProfile->telegram_bot_token) : false,
                'amount' => $transaction->amount,
                'reference' => $transaction->reference
            ]);
            return;
        }

        $message = "🎉 Transaction Successful!\n\n" .
                  "💰 Amount: ₦" . number_format($transaction->amount, 2) . "\n" .
                  "📝 Reference: {$transaction->reference}\n" .
                  "🏢 Site: {$transaction->site->name}\n" .
                  "📅 Date: " . $transaction->created_at->format('M d, Y H:i') . "\n" .
                  "💳 Payment Method: {$transaction->payment_method}";

        // Log that we're about to send the notification
        Log::info('📤 SENDING TELEGRAM NOTIFICATION', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'reference' => $transaction->reference,
            'site' => $transaction->site->name,
            'chat_id' => $businessProfile->telegram_chat_id
        ]);

        // Send POST request to Telegram API
        $response = Http::post("https://api.telegram.org/bot{$businessProfile->telegram_bot_token}/sendMessage", [
            'chat_id' => $businessProfile->telegram_chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);

        // Decode response
        $result = $response->json();

        // Check for success
        if ($response->ok() && isset($result['ok']) && $result['ok'] === true && isset($result['result']['message_id'])) {
            Log::info('✅ TELEGRAM NOTIFICATION SENT SUCCESSFULLY!', [
                'transaction_id' => $transaction->id,
                'message_id' => $result['result']['message_id'],
                'business_profile_id' => $businessProfile->id,
                'amount' => $transaction->amount,
                'reference' => $transaction->reference,
                'site' => $transaction->site->name,
                'payment_method' => $transaction->payment_method,
                'chat_id' => $businessProfile->telegram_chat_id
            ]);
            
            // Also log a simple message for easy visibility
            Log::info("📱 Telegram notification sent for transaction {$transaction->reference} - Amount: ₦" . number_format($transaction->amount, 2));
        } else {
            // Defensive logging if Telegram API response fails or is malformed
            Log::error('❌ FAILED TO SEND TELEGRAM NOTIFICATION', [
                'transaction_id' => $transaction->id,
                'response' => $result,
                'http_status' => $response->status(),
                'business_profile_id' => $businessProfile->id,
                'amount' => $transaction->amount,
                'reference' => $transaction->reference
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error sending Telegram notification', [
            'transaction_id' => $transaction->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
}
