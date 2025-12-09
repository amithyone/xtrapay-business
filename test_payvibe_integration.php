<?php
/**
 * Comprehensive test for PayVibe Integration
 * Tests: API endpoint, account generation, webhook simulation
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Site;
use App\Models\Transaction;
use App\Services\PayVibeService;
use Illuminate\Support\Facades\Log;

echo "🧪 PayVibe Integration Test\n";
echo "==========================\n\n";

try {
    // Step 1: Get a test site
    echo "📋 Step 1: Finding test site...\n";
    $site = Site::where('is_active', true)->first();
    
    if (!$site) {
        echo "❌ No active site found. Please create a site first.\n";
        exit(1);
    }
    
    echo "✅ Found site: {$site->name}\n";
    echo "   API Code: {$site->api_code}\n";
    echo "   API Key: " . substr($site->api_key, 0, 20) . "...\n";
    echo "   Webhook URL: " . ($site->webhook_url ?: 'Not set') . "\n\n";
    
    // Step 2: Test PayVibe Service directly
    echo "📋 Step 2: Testing PayVibe Service...\n";
    $payVibeService = new PayVibeService();
    
    $testBusinessId = $site->business_profile_id;
    $reference = $payVibeService->generateReference($testBusinessId, $site->id);
    $amountInKobo = 10000; // ₦100.00
    
    echo "   Reference: {$reference}\n";
    echo "   Amount: ₦" . number_format($amountInKobo / 100, 2) . "\n";
    
    $result = $payVibeService->initiateVirtualAccount($reference, $amountInKobo);
    
    if ($result['success']) {
        echo "✅ PayVibe Service: SUCCESS\n";
        $accountData = $result['data'];
        echo "   Account Number: " . ($accountData['account_number'] ?? $accountData['virtual_account_number'] ?? 'N/A') . "\n";
        echo "   Bank: " . ($accountData['bank_name'] ?? 'N/A') . "\n";
        echo "   Account Name: " . ($accountData['account_name'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ PayVibe Service: FAILED\n";
        echo "   Error: " . ($result['message'] ?? 'Unknown error') . "\n\n";
        exit(1);
    }
    
    // Step 3: Test API endpoint via HTTP request
    echo "📋 Step 3: Testing API Endpoint...\n";
    
    $apiUrl = 'http://localhost:8000/api/v1/payvibe/request-account';
    $testData = [
        'site_api_code' => $site->api_code,
        'amount' => 200.00, // ₦200.00
        'description' => 'Test payment integration',
        'customer_email' => 'test@example.com',
        'customer_name' => 'Test Customer',
        'metadata' => [
            'test' => true,
            'order_id' => 'TEST_ORDER_123'
        ]
    ];
    
    echo "   API URL: {$apiUrl}\n";
    echo "   Amount: ₦" . number_format($testData['amount'], 2) . "\n";
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($testData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $site->api_key
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        echo "❌ cURL Error: {$curlError}\n";
        echo "   Note: Make sure your Laravel server is running (php artisan serve)\n\n";
    } else {
        echo "   HTTP Code: {$httpCode}\n";
        
        $responseData = json_decode($response, true);
        
        if ($httpCode === 201 && isset($responseData['success']) && $responseData['success']) {
            echo "✅ API Endpoint: SUCCESS\n";
            echo "   Transaction ID: " . ($responseData['data']['transaction_id'] ?? 'N/A') . "\n";
            echo "   Reference: " . ($responseData['data']['reference'] ?? 'N/A') . "\n";
            echo "   Account Number: " . ($responseData['data']['account_number'] ?? 'N/A') . "\n";
            echo "   Bank: " . ($responseData['data']['bank_name'] ?? 'N/A') . "\n";
            echo "   Status: " . ($responseData['data']['status'] ?? 'N/A') . "\n\n";
            
            $transactionId = $responseData['data']['transaction_id'] ?? null;
            $transactionReference = $responseData['data']['reference'] ?? null;
        } else {
            echo "❌ API Endpoint: FAILED\n";
            echo "   Message: " . ($responseData['message'] ?? 'Unknown error') . "\n";
            if (isset($responseData['errors'])) {
                echo "   Errors: " . json_encode($responseData['errors'], JSON_PRETTY_PRINT) . "\n";
            }
            echo "   Response: " . substr($response, 0, 500) . "\n\n";
        }
    }
    
    // Step 4: Verify transaction was created
    if (isset($transactionReference)) {
        echo "📋 Step 4: Verifying transaction in database...\n";
        $transaction = Transaction::where('reference', $transactionReference)->first();
        
        if ($transaction) {
            echo "✅ Transaction Found:\n";
            echo "   ID: {$transaction->id}\n";
            echo "   Reference: {$transaction->reference}\n";
            echo "   Amount: ₦" . number_format($transaction->amount, 2) . "\n";
            echo "   Status: {$transaction->status}\n";
            echo "   Payment Method: {$transaction->payment_method}\n";
            echo "   Site ID: {$transaction->site_id}\n";
            echo "   Created: {$transaction->created_at}\n\n";
        } else {
            echo "❌ Transaction not found in database\n\n";
        }
    }
    
    // Step 5: Test webhook simulation
    echo "📋 Step 5: Testing webhook notification (simulation)...\n";
    
    if (isset($transaction) && $site->webhook_url) {
        echo "   Webhook URL: {$site->webhook_url}\n";
        echo "   Simulating payment received webhook...\n";
        
        // Simulate webhook payload
        $webhookPayload = [
            'event' => 'payment.received',
            'transaction' => [
                'id' => $transaction->id,
                'reference' => $transaction->reference,
                'amount' => $transaction->amount,
                'status' => 'success',
                'customer_email' => $transaction->customer_email,
                'customer_name' => $transaction->customer_name,
            ],
            'site' => [
                'id' => $site->id,
                'name' => $site->name,
                'api_code' => $site->api_code,
            ],
            'metadata' => $transaction->metadata ?? [],
            'timestamp' => now()->toIso8601String(),
        ];
        
        echo "   Payload prepared:\n";
        echo "   " . json_encode($webhookPayload, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "   Note: Actual webhook will be sent when PayVibe confirms payment\n";
        echo "   Your webhook endpoint should handle this payload format\n\n";
    } else {
        if (!$site->webhook_url) {
            echo "⚠️  Webhook URL not configured for this site\n";
            echo "   Set it in Dashboard → Sites → Edit Site → Webhook URL\n\n";
        }
    }
    
    // Summary
    echo "📊 Test Summary\n";
    echo "===============\n";
    echo "✅ PayVibe Service: Working\n";
    if (isset($httpCode) && $httpCode === 201) {
        echo "✅ API Endpoint: Working\n";
    } else {
        echo "⚠️  API Endpoint: Not tested (server may not be running)\n";
    }
    if (isset($transaction)) {
        echo "✅ Transaction Creation: Working\n";
    }
    if ($site->webhook_url) {
        echo "✅ Webhook URL: Configured\n";
    } else {
        echo "⚠️  Webhook URL: Not configured\n";
    }
    
    echo "\n🎉 Integration test completed!\n";
    echo "\nNext steps:\n";
    echo "1. Make sure Laravel server is running: php artisan serve\n";
    echo "2. Test API endpoint from your application\n";
    echo "3. Configure webhook URL in site settings\n";
    echo "4. Test with actual payment\n";
    
} catch (\Exception $e) {
    echo "\n❌ Test failed with exception:\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    exit(1);
}

