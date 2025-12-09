<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PayVibeService;
use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayVibeApiController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Generate a PayVibe virtual account via API
     * Businesses call this endpoint to request virtual account numbers
     * 
     * Authentication: X-API-Key header + site_api_code in request body
     */
    public function requestVirtualAccount(Request $request)
    {
        try {
            Log::info('PayVibe API: Virtual account request received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all(),
                'ip' => $request->ip()
            ]);

            // 1. Validate API Key from header
            $apiKey = $request->header('X-API-Key');
            if (!$apiKey) {
                Log::warning('PayVibe API: No API key provided', [
                    'ip' => $request->ip()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'API key required. Please provide X-API-Key header.'
                ], 401);
            }

            // 2. Validate request data
            $validator = Validator::make($request->all(), [
                'site_api_code' => 'required|string',
                'amount' => 'required|numeric|min:100', // Minimum ₦1.00 (100 kobo)
                'reference' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email',
                'customer_name' => 'nullable|string|max:255',
                'metadata' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                Log::error('PayVibe API: Validation failed', [
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
                Log::error('PayVibe API: Site validation failed', [
                    'api_code' => $data['site_api_code'],
                    'api_key_provided' => substr($apiKey, 0, 10) . '...',
                    'ip' => $request->ip()
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
                    Log::warning('PayVibe API: IP not allowed', [
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

            // 5. Convert amount to kobo
            $amountInKobo = (int)($data['amount'] * 100);

            // 6. Generate reference (use provided or generate new)
            $reference = $data['reference'] ?? $this->payVibeService->generateReference(
                $site->business_profile_id,
                $site->id
            );

            // 7. Call PayVibe API to initiate virtual account
            Log::info('PayVibe API: Calling PayVibe service', [
                'reference' => $reference,
                'amount' => $amountInKobo,
                'site_id' => $site->id
            ]);

            $result = $this->payVibeService->initiateVirtualAccount($reference, $amountInKobo);

            if (!$result['success']) {
                Log::error('PayVibe API: Failed to generate virtual account', [
                    'reference' => $reference,
                    'error' => $result['message'] ?? 'Unknown error',
                    'site_id' => $site->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to generate virtual account',
                    'error' => $result['data'] ?? null
                ], 500);
            }

            $payVibeData = $result['data'];

            // 8. Create pending transaction record
            $transaction = Transaction::create([
                'reference' => $reference,
                'external_id' => $payVibeData['reference'] ?? $reference,
                'site_id' => $site->id,
                'business_profile_id' => $site->business_profile_id,
                'amount' => $data['amount'],
                'currency' => 'NGN',
                'status' => 'pending',
                'payment_method' => 'payvibe',
                'customer_email' => $data['customer_email'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'description' => $data['description'] ?? 'PayVibe virtual account payment',
                'metadata' => array_merge([
                    'payvibe_reference' => $payVibeData['reference'] ?? $reference,
                    'account_number' => $payVibeData['account_number'] ?? $payVibeData['virtual_account_number'] ?? null,
                    'bank_name' => $payVibeData['bank_name'] ?? null,
                    'account_name' => $payVibeData['account_name'] ?? null,
                    'initiated_at' => now()->toIso8601String(),
                    'requested_via_api' => true
                ], $data['metadata'] ?? [])
            ]);

            Log::info('PayVibe API: Virtual account generated and transaction created', [
                'transaction_id' => $transaction->id,
                'reference' => $reference,
                'site_id' => $site->id,
                'account_number' => $payVibeData['account_number'] ?? $payVibeData['virtual_account_number'] ?? null
            ]);

            // 9. Return success response with account details
            return response()->json([
                'success' => true,
                'message' => 'Virtual account generated successfully',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $reference,
                    'account_number' => $payVibeData['account_number'] ?? $payVibeData['virtual_account_number'] ?? null,
                    'bank_name' => $payVibeData['bank_name'] ?? null,
                    'account_name' => $payVibeData['account_name'] ?? null,
                    'amount' => $data['amount'],
                    'currency' => 'NGN',
                    'status' => 'pending',
                    'expires_at' => now()->addHours(24)->toIso8601String(), // Virtual accounts typically expire after 24 hours
                    'created_at' => $transaction->created_at->toIso8601String()
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('PayVibe API: Exception processing request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for a transaction
     * Businesses can check if a payment has been completed
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            // 1. Validate API Key from header
            $apiKey = $request->header('X-API-Key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key required. Please provide X-API-Key header.'
                ], 401);
            }

            // 2. Validate request
            $validator = Validator::make($request->all(), [
                'site_api_code' => 'required|string',
                'reference' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // 3. Validate site
            $site = Site::where('api_code', $data['site_api_code'])
                       ->where('api_key', $apiKey)
                       ->where('is_active', true)
                       ->first();

            if (!$site) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key or site not found'
                ], 403);
            }

            // 4. Find transaction
            $transaction = Transaction::where('reference', $data['reference'])
                ->where('site_id', $site->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // 5. Check payment status from PayVibe
            $result = $this->payVibeService->checkPaymentStatus($transaction->reference);

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'payvibe_status' => $result['data'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayVibe API: Error checking payment status', [
                'error' => $e->getMessage(),
                'reference' => $request->input('reference')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}

