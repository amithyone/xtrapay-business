<?php

namespace App\Http\Controllers;

use App\Services\PayVibeService;
use App\Models\BusinessProfile;
use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PayVibeController extends Controller
{
    protected $payVibeService;

    public function __construct(PayVibeService $payVibeService)
    {
        $this->payVibeService = $payVibeService;
    }

    /**
     * Generate a PayVibe virtual account for payment
     * This endpoint is called when a business/user requests a virtual account number
     */
    public function generateVirtualAccount(Request $request)
    {
        try {
            $user = Auth::user();
            $businessProfile = $user->businessProfile;

            if (!$businessProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found. Please create a business profile first.'
                ], 422);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:100', // Minimum ₦1.00 (100 kobo)
                'site_id' => 'nullable|exists:sites,id',
                'description' => 'nullable|string|max:255',
                'customer_email' => 'nullable|email',
                'customer_name' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $amountInKobo = (int)($data['amount'] * 100); // Convert to kobo

            // Validate site belongs to business if provided
            $site = null;
            if (isset($data['site_id'])) {
                $site = Site::where('id', $data['site_id'])
                    ->where('business_profile_id', $businessProfile->id)
                    ->first();

                if (!$site) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Site not found or does not belong to your business.'
                    ], 403);
                }
            }

            // Generate unique reference
            $reference = $this->payVibeService->generateReference(
                $businessProfile->id,
                $site ? $site->id : null
            );

            // Call PayVibe API to initiate virtual account
            $result = $this->payVibeService->initiateVirtualAccount($reference, $amountInKobo);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to generate virtual account'
                ], 500);
            }

            $payVibeData = $result['data'];

            // Create pending transaction record
            $transaction = Transaction::create([
                'reference' => $reference,
                'external_id' => $payVibeData['reference'] ?? $reference,
                'site_id' => $site ? $site->id : null,
                'business_profile_id' => $businessProfile->id,
                'amount' => $data['amount'],
                'currency' => 'NGN',
                'status' => 'pending',
                'payment_method' => 'payvibe',
                'customer_email' => $data['customer_email'] ?? null,
                'customer_name' => $data['customer_name'] ?? null,
                'description' => $data['description'] ?? 'PayVibe virtual account payment',
                'metadata' => [
                    'payvibe_reference' => $payVibeData['reference'] ?? $reference,
                    'account_number' => $payVibeData['account_number'] ?? null,
                    'bank_name' => $payVibeData['bank_name'] ?? null,
                    'account_name' => $payVibeData['account_name'] ?? null,
                    'initiated_at' => now()->toIso8601String()
                ]
            ]);

            Log::info('PayVibe: Virtual account generated and transaction created', [
                'transaction_id' => $transaction->id,
                'reference' => $reference,
                'business_profile_id' => $businessProfile->id,
                'amount' => $data['amount']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Virtual account generated successfully',
                'data' => [
                    'transaction_id' => $transaction->id,
                    'reference' => $reference,
                    'account_number' => $payVibeData['account_number'] ?? null,
                    'bank_name' => $payVibeData['bank_name'] ?? null,
                    'account_name' => $payVibeData['account_name'] ?? null,
                    'amount' => $data['amount'],
                    'currency' => 'NGN',
                    'status' => 'pending',
                    'expires_at' => now()->addHours(24)->toIso8601String() // Virtual accounts typically expire after 24 hours
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('PayVibe: Error generating virtual account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating virtual account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for a transaction
     */
    public function checkPaymentStatus(Request $request, Transaction $transaction)
    {
        try {
            $user = Auth::user();
            $businessProfile = $user->businessProfile;

            if (!$businessProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business profile not found.'
                ], 422);
            }

            // Verify transaction belongs to business
            if ($transaction->business_profile_id !== $businessProfile->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found.'
                ], 403);
            }

            // Check payment status from PayVibe
            $result = $this->payVibeService->checkPaymentStatus($transaction->reference);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to check payment status'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            Log::error('PayVibe: Error checking payment status', [
                'error' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while checking payment status: ' . $e->getMessage()
            ], 500);
        }
    }
}

