<?php
/**
 * Test script to verify PayVibe API integration
 * Run this from command line: php test_payvibe.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\PayVibeService;
use Illuminate\Support\Facades\Log;

echo "🧪 Testing PayVibe API Integration\n";
echo "===================================\n\n";

try {
    $payVibeService = new PayVibeService();
    
    // Check if API key is configured
    $apiKey = config('services.payvibe.api_key');
    if (!$apiKey) {
        echo "⚠️  WARNING: PAYVIBE_API_KEY not configured in .env file\n";
        echo "   Please add PAYVIBE_API_KEY=your_api_key to your .env file\n";
        echo "   The API will still be called but may fail without authentication\n\n";
    } else {
        echo "✅ API Key configured: " . substr($apiKey, 0, 10) . "...\n\n";
    }
    
    // Generate a test reference
    $testBusinessId = 1;
    $reference = $payVibeService->generateReference($testBusinessId);
    
    echo "📝 Test Details:\n";
    echo "   Reference: {$reference}\n";
    echo "   Amount: ₦100.00 (10000 kobo)\n";
    echo "   API URL: https://payvibeapi.six3tech.com/api/v1/payments/virtual-accounts/initiate\n";
    echo "   Product Identifier: fadded_sms\n\n";
    
    // Test amount: ₦100 (10000 kobo)
    $amountInKobo = 10000;
    
    echo "🔄 Calling PayVibe API...\n";
    $result = $payVibeService->initiateVirtualAccount($reference, $amountInKobo);
    
    echo "\n📊 Results:\n";
    echo "   Success: " . ($result['success'] ? '✅ YES' : '❌ NO') . "\n";
    
    if ($result['success']) {
        echo "   ✅ Virtual Account Generated!\n";
        $accountNumber = $result['data']['account_number'] ?? $result['data']['virtual_account_number'] ?? 'N/A';
        echo "   Account Number: {$accountNumber}\n";
        echo "   Bank Name: " . ($result['data']['bank_name'] ?? 'N/A') . "\n";
        echo "   Account Name: " . ($result['data']['account_name'] ?? 'N/A') . "\n";
        echo "   Amount: ₦" . number_format($amountInKobo / 100, 2) . "\n";
        echo "   Reference: " . ($result['data']['reference'] ?? 'N/A') . "\n";
        echo "\n   Full Response:\n";
        echo "   " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "   ❌ Failed to generate virtual account\n";
        echo "   Message: " . ($result['message'] ?? 'Unknown error') . "\n";
        if (isset($result['data'])) {
            echo "\n   Response Data:\n";
            echo "   " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
    echo "\n✅ Test completed!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Test failed with exception:\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "\n   Stack Trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
    exit(1);
}

