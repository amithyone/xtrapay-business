<?php
// Script to get chat IDs of people who have messaged @faddedsmmbot

// Replace with your actual bot token for @faddedsmmbot
$botToken = 'YOUR_FADDEDSMMBOT_TOKEN_HERE';

if ($botToken === 'YOUR_FADDEDSMMBOT_TOKEN_HERE') {
    echo "❌ Please update the bot token in this script first!\n";
    echo "Get your bot token from @BotFather for @faddedsmmbot\n";
    exit;
}

echo "🔍 Getting chat IDs for @faddedsmmbot\n";
echo "=====================================\n\n";

// Get updates from the bot
$url = "https://api.telegram.org/bot{$botToken}/getUpdates";
$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data || !isset($data['ok']) || !$data['ok']) {
    echo "❌ Failed to get updates: " . json_encode($data) . "\n";
    exit;
}

$updates = $data['result'];
echo "📊 Found " . count($updates) . " updates\n\n";

if (empty($updates)) {
    echo "ℹ️  No messages found. Make sure people have sent messages to your bot.\n";
    echo "   They need to:\n";
    echo "   1. Go to @faddedsmmbot\n";
    echo "   2. Click 'Start' or send any message\n";
    exit;
}

$chatIds = [];
$uniqueChats = [];

foreach ($updates as $update) {
    if (isset($update['message'])) {
        $message = $update['message'];
        $chat = $message['chat'];
        $chatId = $chat['id'];
        $chatType = $chat['type'];
        
        // Only show unique chats
        if (!in_array($chatId, $uniqueChats)) {
            $uniqueChats[] = $chatId;
            
            $chatInfo = [
                'chat_id' => $chatId,
                'type' => $chatType,
                'first_name' => $chat['first_name'] ?? 'N/A',
                'last_name' => $chat['last_name'] ?? '',
                'username' => $chat['username'] ?? 'N/A',
                'title' => $chat['title'] ?? 'N/A',
                'message_text' => $message['text'] ?? 'N/A',
                'date' => date('Y-m-d H:i:s', $message['date'])
            ];
            
            $chatIds[] = $chatInfo;
        }
    }
}

echo "👥 Unique chats found: " . count($chatIds) . "\n\n";

foreach ($chatIds as $index => $chat) {
    $number = $index + 1;
    echo "{$number}. Chat ID: {$chat['chat_id']}\n";
    echo "   Type: {$chat['type']}\n";
    
    if ($chat['type'] === 'private') {
        echo "   Name: {$chat['first_name']} {$chat['last_name']}\n";
        echo "   Username: @{$chat['username']}\n";
    } else {
        echo "   Title: {$chat['title']}\n";
    }
    
    echo "   Message: {$chat['message_text']}\n";
    echo "   Date: {$chat['date']}\n";
    echo "\n";
}

echo "📋 Summary:\n";
echo "===========\n";
echo "Chat IDs for private chats (users):\n";
foreach ($chatIds as $chat) {
    if ($chat['type'] === 'private') {
        echo "- {$chat['chat_id']} (@{$chat['username']})\n";
    }
}

echo "\nChat IDs for groups/channels:\n";
foreach ($chatIds as $chat) {
    if ($chat['type'] !== 'private') {
        echo "- {$chat['chat_id']} ({$chat['title']})\n";
    }
}

echo "\n💡 To send a test message to all users:\n";
foreach ($chatIds as $chat) {
    if ($chat['type'] === 'private') {
        echo "curl -X POST \"https://api.telegram.org/bot{$botToken}/sendMessage\" \\\n";
        echo "     -d \"chat_id={$chat['chat_id']}&text=Hello from @faddedsmmbot!\"\n\n";
    }
}
?> 