<?php
/**
 * Test script to verify site distribution data
 */

require_once 'vendor/autoload.php';

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Site Distribution Data\n";
echo "=================================\n\n";

// Get business profile and sites
$user = User::where('email', 'faddedog@gmail.com')->first();
$businessProfile = $user->businessProfile;
$sites = $businessProfile->sites;

echo "✅ Business Profile: {$businessProfile->business_name}\n";
echo "📊 Sites Found: " . $sites->count() . "\n\n";

foreach ($sites as $site) {
    echo "🏢 Site: {$site->name}\n";
    
    // Get successful transactions for this site
    $successfulTransactions = Transaction::where('site_id', $site->id)
        ->where('status', 'success')
        ->get();
    
    $totalAmount = $successfulTransactions->sum('amount');
    $transactionCount = $successfulTransactions->count();
    
    echo "   💰 Total Amount: ₦" . number_format($totalAmount, 2) . "\n";
    echo "   📈 Transaction Count: {$transactionCount}\n";
    echo "   📅 Average per Transaction: ₦" . ($transactionCount > 0 ? number_format($totalAmount / $transactionCount, 2) : '0.00') . "\n\n";
}

// Test the distribution calculation
echo "🎯 Testing Distribution Calculation:\n";
echo "===================================\n";

$siteIds = $sites->pluck('id');
$siteDistribution = Transaction::whereIn('site_id', $siteIds)
    ->where('status', 'success')
    ->with('site')
    ->get()
    ->groupBy('site_id')
    ->map(function ($transactions, $siteId) {
        $site = $transactions->first()->site;
        return [
            'site_name' => $site ? $site->name : 'Unknown Site',
            'total_amount' => $transactions->sum('amount'),
            'transaction_count' => $transactions->count(),
            'site_id' => $siteId
        ];
    })
    ->values()
    ->toArray();

foreach ($siteDistribution as $site) {
    echo "🏢 {$site['site_name']}:\n";
    echo "   💰 ₦" . number_format($site['total_amount'], 2) . "\n";
    echo "   📈 {$site['transaction_count']} transactions\n";
    echo "   📊 " . round(($site['total_amount'] / array_sum(array_column($siteDistribution, 'total_amount'))) * 100, 2) . "% of total\n\n";
}

echo "🎉 Test completed!\n"; 