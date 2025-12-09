<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayVibeService
{
    protected $baseUrl;
    protected $productIdentifier;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.payvibe.base_url', 'https://payvibeapi.six3tech.com/api');
        $this->productIdentifier = config('services.payvibe.product_identifier', 'fadded_sms');
        $this->apiKey = config('services.payvibe.api_key');
    }

    /**
     * Initiate a virtual account payment
     *
     * @param string $reference Unique reference for the payment
     * @param float $amount Amount in kobo (e.g., 500000 for ₦5,000)
     * @return array
     */
    public function initiateVirtualAccount(string $reference, float $amount): array
    {
        try {
            Log::info('PayVibe: Initiating virtual account', [
                'reference' => $reference,
                'amount' => $amount
            ]);

            // Prepare headers with API key
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if ($this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
                $headers['X-API-Key'] = $this->apiKey;
            }

            $response = Http::withHeaders($headers)->post("{$this->baseUrl}/v1/payments/virtual-accounts/initiate", [
                'reference' => $reference,
                'product_identifier' => $this->productIdentifier,
                'amount' => $amount
            ]);

            $data = $response->json();

            // Check for success - PayVibe returns status as boolean true or string 'success'
            $isSuccess = ($response->successful() && 
                         (($data['status'] === true || $data['status'] === 'success') || 
                          (isset($data['data']) && isset($data['data']['virtual_account_number']))));

            if ($isSuccess) {
                // Handle different response formats
                $accountData = $data['data'] ?? $data;
                
                // Normalize field names for consistency
                if (isset($accountData['virtual_account_number']) && !isset($accountData['account_number'])) {
                    $accountData['account_number'] = $accountData['virtual_account_number'];
                }
                
                Log::info('PayVibe: Virtual account initiated successfully', [
                    'reference' => $reference,
                    'account_number' => $accountData['account_number'] ?? $accountData['virtual_account_number'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => $accountData
                ];
            }

            Log::error('PayVibe: Failed to initiate virtual account', [
                'reference' => $reference,
                'response' => $data
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initiate virtual account',
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('PayVibe: Exception while initiating virtual account', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check payment status
     *
     * @param string $reference Payment reference
     * @return array
     */
    public function checkPaymentStatus(string $reference): array
    {
        try {
            Log::info('PayVibe: Checking payment status', [
                'reference' => $reference
            ]);

            // Prepare headers with API key
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if ($this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
                $headers['X-API-Key'] = $this->apiKey;
            }

            $response = Http::withHeaders($headers)->post("{$this->baseUrl}/v1/payments/virtual-accounts/status", [
                'reference' => $reference,
                'product_identifier' => $this->productIdentifier
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['status'])) {
                Log::info('PayVibe: Payment status retrieved', [
                    'reference' => $reference,
                    'status' => $data['status'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            Log::error('PayVibe: Failed to check payment status', [
                'reference' => $reference,
                'response' => $data
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to check payment status',
                'data' => $data
            ];

        } catch (\Exception $e) {
            Log::error('PayVibe: Exception while checking payment status', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate a unique reference for PayVibe payment
     *
     * @param int $businessProfileId
     * @param int|null $siteId
     * @return string
     */
    public function generateReference(int $businessProfileId, ?int $siteId = null): string
    {
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $businessId = str_pad($businessProfileId, 4, '0', STR_PAD_LEFT);
        $sitePart = $siteId ? str_pad($siteId, 4, '0', STR_PAD_LEFT) : '0000';
        
        return "PAYVIBE_{$businessId}_{$sitePart}_{$timestamp}_{$random}";
    }
}

