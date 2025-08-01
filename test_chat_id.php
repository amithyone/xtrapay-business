<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BusinessProfile;

// Get the current user's business profile
$user = auth()->user();
if (!$user) {
    echo "No authenticated user found. Please log in first.\n";
    exit;
}

$businessProfile = BusinessProfile::where('user_id', $user->id)->first();
if (!$businessProfile) {
    echo "No business profile found for user.\n";
    exit;
}

// Test chat ID
$chatId = '6859958780';
$botToken = $businessProfile->telegram_bot_token;

if (!$botToken) {
    echo "No Telegram bot token found in business profile.\n";
    exit;
}

echo "Testing Telegram notification with:\n";
echo "Bot Token: " . substr($botToken, 0, 10) . "...\n";
echo "Chat ID: $chatId\n\n";

// Send test message
$message = "🔔 Test notification from XtraBusiness\n\nThis is a test message to verify your Telegram integration is working correctly!";

$url = "https://api.telegram.org/bot{$botToken}/sendMessage";
$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Response Code: $httpCode\n";
echo "Response: $response\n\n";

$result = json_decode($response, true);

if ($result && isset($result['ok']) && $result['ok']) {
    echo "✅ SUCCESS! Telegram notification sent successfully.\n";
    echo "Message ID: " . $result['result']['message_id'] . "\n";
    
    // Update the business profile with the correct chat ID
    $businessProfile->telegram_chat_id = $chatId;
    $businessProfile->save();
    
    echo "✅ Chat ID saved to database.\n";
} else {
    echo "❌ FAILED to send Telegram notification.\n";
    if (isset($result['description'])) {
        echo "Error: " . $result['description'] . "\n";
    }
}
?> 