<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing email configuration...\n";

// Test basic email sending
try {
    Mail::raw('Test email from XtraPay Business - ' . date('Y-m-d H:i:s'), function($message) {
        $message->to('test@example.com')
                ->subject('Email Configuration Test')
                ->from('noreply@xtrapay.cash', 'XTRABUSINESS');
    });
    echo "✅ Basic email test completed\n";
} catch (Exception $e) {
    echo "❌ Email test failed: " . $e->getMessage() . "\n";
}

// Test user creation and verification email
try {
    // Create a test user
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@xtrapay.cash',
        'password' => Hash::make('password123'),
    ]);
    
    echo "✅ Test user created: " . $user->email . "\n";
    
    // Send verification email
    $user->sendEmailVerificationNotification();
    echo "✅ Verification email sent to: " . $user->email . "\n";
    
    // Clean up - delete test user
    $user->delete();
    echo "✅ Test user cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ User creation/verification test failed: " . $e->getMessage() . "\n";
}

echo "\nEmail configuration test completed!\n";
echo "Check your email server logs for delivery status.\n"; 