<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "Testing Webhook Notification Flow\n";
echo "================================\n\n";

// Get the first user with a business profile
$user = User::whereHas('businessProfile', function($query) {
    $query->whereNotNull('telegram_chat_id')
          ->whereNotNull('telegram_bot_token');
})->first();

if (!$user) {
    echo "❌ No user found with Telegram settings configured.\n";
    exit;
}

$businessProfile = $user->businessProfile;
echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
echo "✅ Business Profile ID: {$businessProfile->id}\n\n";

// Get the first active site
$site = Site::where('is_active', true)->first();
if (!$site) {
    echo "❌ No active site found.\n";
    exit;
}

echo "✅ Using site: {$site->name} (ID: {$site->id})\n";
echo "✅ Site API Code: {$site->api_code}\n";
echo "✅ Site API Key: {$site->api_key}\n\n";

// Simulate webhook data
$webhookData = [
    'site_api_code' => $site->api_code,
    'reference' => 'WEBHOOK-TEST-' . time(),
    'amount' => 7500.00,
    'currency' => 'NGN',
    'status' => 'success',
    'payment_method' => 'bank_transfer',
    'customer_email' => 'customer@example.com',
    'customer_name' => 'John Doe',
    'description' => 'Test webhook transaction',
    'external_id' => 'EXT-' . time(),
    'metadata' => [
        'gateway' => 'xtrapay',
        'test' => true,
        'webhook_test' => true
    ],
    'timestamp' => now()->toISOString()
];

echo "📤 Sending webhook data:\n";
echo "   - Reference: {$webhookData['reference']}\n";
echo "   - Amount: ₦{$webhookData['amount']}\n";
echo "   - Status: {$webhookData['status']}\n\n";

// Send webhook to the actual endpoint
$response = Http::withHeaders([
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
    'X-API-Key' => 'test-api-key-12345'
])->post('http://localhost:8000/api/webhook/receive-transaction', $webhookData);

echo "📥 Webhook Response:\n";
echo "   - Status Code: {$response->status()}\n";
echo "   - Response: " . $response->body() . "\n\n";

$result = $response->json();

if ($response->successful() && isset($result['success']) && $result['success']) {
    echo "✅ Webhook processed successfully!\n";
    echo "   - Transaction ID: {$result['transaction_id']}\n";
    echo "   - Action: {$result['action']}\n";
    echo "   - Check your Telegram for the notification\n";
} else {
    echo "❌ Webhook failed:\n";
    echo "   - Error: " . ($result['message'] ?? 'Unknown error') . "\n";
}

echo "\n🎯 Webhook test completed!\n";
echo "Check the Laravel logs (storage/logs/laravel.log) for detailed information.\n";
?> 