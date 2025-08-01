<?php
/**
 * Test script to verify savings collection with multiple transactions
 */

require_once 'vendor/autoload.php';

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\BusinessSavings;
use App\Services\SavingsCollectionService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Multiple Savings Collections\n";
echo "=====================================\n\n";

// Get business profile ID 1
$businessProfile = BusinessProfile::find(1);
$site = Site::where('business_profile_id', $businessProfile->id)->first();
$savings = $businessProfile->savings;

echo "💰 Initial Savings: ₦" . number_format($savings->current_savings, 2) . "\n";
echo "🔄 Transactions Today: {$savings->transactions_today}/{$savings->daily_transaction_limit}\n\n";

$savingsService = new SavingsCollectionService();

// Test multiple transactions
$testAmounts = [5000, 15000, 8000, 12000, 10000, 7000]; // 6 transactions

foreach ($testAmounts as $index => $amount) {
    echo "--- Transaction " . ($index + 1) . " (₦" . number_format($amount, 2) . ") ---\n";
    
    // Create test transaction
    $transaction = Transaction::create([
        'site_id' => $site->id,
        'business_profile_id' => $businessProfile->id,
        'reference' => 'TEST_MULTI_' . time() . '_' . $index,
        'amount' => $amount,
        'currency' => 'NGN',
        'status' => 'success',
        'payment_method' => 'card',
        'customer_email' => 'test' . $index . '@example.com',
        'customer_name' => 'Test Customer ' . $index,
        'description' => 'Test transaction ' . ($index + 1)
    ]);
    
    // Process savings collection
    $result = $savingsService->processTransaction($transaction);
    
    if ($result && $result['collected']) {
        echo "✅ Collected: ₦" . number_format($result['amount'], 2) . " (" . $result['percentage'] . "%)\n";
        echo "💰 Total Savings: ₦" . number_format($result['current_savings'], 2) . "\n";
        echo "📈 Progress: " . round($result['progress'], 2) . "%\n";
    } else {
        echo "❌ No collection - Daily limit reached\n";
    }
    
    // Refresh savings to get updated counts
    $savings->refresh();
    echo "🔄 Transactions Today: {$savings->transactions_today}/{$savings->daily_transaction_limit}\n\n";
}

echo "🎉 Final Results:\n";
echo "💰 Total Savings: ₦" . number_format($savings->current_savings, 2) . "\n";
echo "📊 Progress: " . round($savings->progress_percentage, 2) . "%\n";
echo "🔄 Total Transactions Today: {$savings->transactions_today}/{$savings->daily_transaction_limit}\n"; 