<?php
/**
 * Test script to verify savings collection is working
 */

require_once 'vendor/autoload.php';

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\BusinessSavings;
use App\Jobs\ProcessSavingsCollection;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Savings Collection System\n";
echo "===================================\n\n";

// Get business profile ID 1
$businessProfile = BusinessProfile::find(1);
if (!$businessProfile) {
    echo "❌ Business Profile ID 1 not found\n";
    exit(1);
}

echo "✅ Found Business Profile: {$businessProfile->business_name}\n";

// Get or create a site for this business
$site = Site::where('business_profile_id', $businessProfile->id)->first();
if (!$site) {
    echo "❌ No site found for business profile\n";
    exit(1);
}

echo "✅ Found Site: {$site->name}\n";

// Check current savings
$savings = $businessProfile->savings;
if (!$savings) {
    echo "❌ No savings record found\n";
    exit(1);
}

echo "💰 Current Savings: ₦" . number_format($savings->current_savings, 2) . "\n";
echo "🎯 Monthly Goal: ₦" . number_format($savings->monthly_goal, 2) . "\n";
echo "📊 Progress: " . round($savings->progress_percentage, 2) . "%\n";
echo "📅 Daily Target: ₦" . number_format($savings->daily_collection_target, 2) . "\n";
echo "🔄 Transactions Today: {$savings->transactions_today}/{$savings->daily_transaction_limit}\n\n";

// Create a test successful transaction
$testTransaction = Transaction::create([
    'site_id' => $site->id,
    'business_profile_id' => $businessProfile->id,
    'reference' => 'TEST_SAVINGS_' . time(),
    'amount' => 10000, // ₦10,000
    'currency' => 'NGN',
    'status' => 'success',
    'payment_method' => 'card',
    'customer_email' => 'test@example.com',
    'customer_name' => 'Test Customer',
    'description' => 'Test transaction for savings collection'
]);

echo "✅ Created test transaction: ₦" . number_format($testTransaction->amount, 2) . "\n";

// Call savings collection service directly
$savingsService = new \App\Services\SavingsCollectionService();
$result = $savingsService->processTransaction($testTransaction);

echo "🚀 Processed savings collection\n\n";

if ($result && $result['collected']) {
    echo "✅ Savings collected: ₦" . number_format($result['amount'], 2) . "\n";
    echo "📊 Collection percentage: " . $result['percentage'] . "%\n";
    echo "💰 New savings total: ₦" . number_format($result['current_savings'], 2) . "\n";
    echo "📈 Progress: " . round($result['progress'], 2) . "%\n";
} else {
    echo "❌ No savings collected. Reason: Daily limit reached or savings inactive\n";
}

echo "\n🎉 Test completed!\n"; 