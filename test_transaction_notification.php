<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Support\Facades\Log;

echo "Testing Transaction Notification System\n";
echo "=====================================\n\n";

// Get the first user with a business profile
$user = User::whereHas('businessProfile', function($query) {
    $query->whereNotNull('telegram_chat_id')
          ->whereNotNull('telegram_bot_token');
})->first();

if (!$user) {
    echo "❌ No user found with Telegram settings configured.\n";
    echo "Please configure Telegram settings in the notifications page first.\n";
    exit;
}

$businessProfile = $user->businessProfile;
echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
echo "✅ Business Profile ID: {$businessProfile->id}\n";
echo "✅ Telegram Chat ID: {$businessProfile->telegram_chat_id}\n";
echo "✅ Telegram Bot Token: " . substr($businessProfile->telegram_bot_token, 0, 10) . "...\n\n";

// Get the first active site
$site = Site::where('is_active', true)->first();
if (!$site) {
    echo "❌ No active site found.\n";
    exit;
}

echo "✅ Using site: {$site->name} (ID: {$site->id})\n\n";

// Create a test transaction
$testTransaction = Transaction::create([
    'site_id' => $site->id,
    'business_profile_id' => $businessProfile->id,
    'reference' => 'TEST-' . time(),
    'external_id' => 'EXT-' . time(),
    'amount' => 5000.00,
    'currency' => 'NGN',
    'status' => 'pending',
    'payment_method' => 'card',
    'customer_email' => 'test@example.com',
    'customer_name' => 'Test Customer',
    'description' => 'Test transaction for notification',
    'metadata' => [
        'test' => true,
        'created_at' => now()->toISOString()
    ]
]);

echo "✅ Created test transaction:\n";
echo "   - ID: {$testTransaction->id}\n";
echo "   - Reference: {$testTransaction->reference}\n";
echo "   - Amount: ₦{$testTransaction->amount}\n";
echo "   - Status: {$testTransaction->status}\n\n";

// Now simulate the webhook process by updating the transaction to success
echo "🔄 Updating transaction status to 'success'...\n";

$oldStatus = $testTransaction->status;
$testTransaction->update(['status' => 'success']);

echo "✅ Transaction status updated from '{$oldStatus}' to 'success'\n\n";

// Now manually trigger the notification logic
echo "📱 Triggering Telegram notification...\n";

try {
    $businessProfile = $testTransaction->businessProfile;
    if (!$businessProfile || !$businessProfile->telegram_chat_id || !$businessProfile->telegram_bot_token) {
        echo "❌ Telegram notification skipped - no chat ID or bot token configured\n";
        exit;
    }

    $message = "🧪 Test Transaction Successful!\n\n" .
              "💰 Amount: ₦" . number_format($testTransaction->amount, 2) . "\n" .
              "📝 Reference: {$testTransaction->reference}\n" .
              "🏢 Site: {$testTransaction->site->name}\n" .
              "📅 Date: " . $testTransaction->created_at->format('M d, Y H:i') . "\n" .
              "💳 Payment Method: {$testTransaction->payment_method}\n" .
              "🔧 This is a test notification";

    $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$businessProfile->telegram_bot_token}/sendMessage", [
        'chat_id' => $businessProfile->telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    $result = $response->json();

    if ($result && isset($result['ok']) && $result['ok']) {
        echo "✅ Telegram notification sent successfully!\n";
        echo "   - Message ID: {$result['result']['message_id']}\n";
        echo "   - Check your Telegram chat for the notification\n";
    } else {
        echo "❌ Failed to send Telegram notification\n";
        echo "   - Response: " . json_encode($result) . "\n";
    }

} catch (\Exception $e) {
    echo "❌ Error sending Telegram notification: {$e->getMessage()}\n";
}

echo "\n🎯 Test completed!\n";
echo "Check the Laravel logs (storage/logs/laravel.log) for detailed information.\n";
?> 