<?php
/**
 * Test script to verify chart data generation
 */

require_once 'vendor/autoload.php';

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\User;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Chart Data Generation\n";
echo "================================\n\n";

// Get business profile and sites
$user = User::where('email', 'faddedog@gmail.com')->first();
$businessProfile = $user->businessProfile;
$sites = $businessProfile->sites;
$siteIds = $sites->pluck('id');
$now = Carbon::now();

echo "✅ Business Profile: {$businessProfile->business_name}\n";
echo "📊 Sites Found: " . $sites->count() . "\n\n";

// Test daily chart data (last 30 days)
echo "📅 Daily Chart Data (Last 30 Days):\n";
echo "====================================\n";

$dailyChartData = Transaction::whereIn('site_id', $siteIds)
    ->where('status', 'success')
    ->where('created_at', '>=', $now->copy()->subDays(30))
    ->selectRaw('DATE(created_at) as date, SUM(amount) as total_amount, COUNT(*) as transaction_count')
    ->groupBy('date')
    ->orderBy('date')
    ->get();

foreach ($dailyChartData as $item) {
    echo "📆 {$item->date}: ₦" . number_format($item->total_amount, 2) . " ({$item->transaction_count} transactions)\n";
}

echo "\n📊 Monthly Chart Data (Last 12 Months):\n";
echo "========================================\n";

$monthlyChartData = Transaction::whereIn('site_id', $siteIds)
    ->where('status', 'success')
    ->where('created_at', '>=', $now->copy()->subMonths(12))
    ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total_amount, COUNT(*) as transaction_count')
    ->groupBy('month')
    ->orderBy('month')
    ->get();

foreach ($monthlyChartData as $item) {
    echo "📅 {$item->month}: ₦" . number_format($item->total_amount, 2) . " ({$item->transaction_count} transactions)\n";
}

echo "\n📈 All-Time Chart Data by Year:\n";
echo "===============================\n";

$allTimeChartData = Transaction::whereIn('site_id', $siteIds)
    ->where('status', 'success')
    ->selectRaw('YEAR(created_at) as year, SUM(amount) as total_amount, COUNT(*) as transaction_count')
    ->groupBy('year')
    ->orderBy('year')
    ->get();

foreach ($allTimeChartData as $item) {
    echo "📅 {$item->year}: ₦" . number_format($item->total_amount, 2) . " ({$item->transaction_count} transactions)\n";
}

echo "\n🎉 Chart data test completed!\n";
echo "📊 Total data points:\n";
echo "   - Daily: " . $dailyChartData->count() . " days\n";
echo "   - Monthly: " . $monthlyChartData->count() . " months\n";
echo "   - All-time: " . $allTimeChartData->count() . " years\n"; 