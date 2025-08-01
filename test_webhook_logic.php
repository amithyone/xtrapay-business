<?php
/**
 * Test script to verify webhook transaction update logic
 * This simulates what happens when we receive webhook notifications
 */

require_once 'vendor/autoload.php';

use App\Models\Transaction;
use App\Models\Site;
use App\Models\BusinessProfile;
use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Webhook Transaction Logic\n";
echo "=====================================\n\n";

// Get or create test data
$user = User::where('email', 'faddedog@gmail.com')->first();
if (!$user) {
    echo "❌ User faddedog@gmail.com not found. Please run migrations and seeders first.\n";
    exit(1);
}

$businessProfile = BusinessProfile::where('user_id', $user->id)->first();
if (!$businessProfile) {
    echo "❌ No business profile found for user. Please create a business profile first.\n";
    exit(1);
}

$site = Site::where('business_profile_id', $businessProfile->id)->first();
if (!$site) {
    echo "❌ No site found for business profile. Please create a site first.\n";
    exit(1);
}

echo "✅ Test data found:\n";
echo "   User: {$user->email}\n";
echo "   Business: {$businessProfile->business_name}\n";
echo "   Site: {$site->name}\n\n";

// Test 1: Create a pending transaction
echo "📝 Test 1: Creating pending transaction...\n";
$reference = 'TEST-' . time();
$pendingTransaction = Transaction::create([
    'site_id' => $site->id,
    'business_profile_id' => $businessProfile->id,
    'reference' => $reference,
    'amount' => 5000.00,
    'currency' => 'NGN',
    'status' => 'pending',
    'payment_method' => 'card',
    'customer_email' => 'test@example.com',
    'customer_name' => 'Test User',
    'description' => 'Test pending transaction',
]);

echo "   ✅ Created pending transaction ID: {$pendingTransaction->id}\n";
echo "   Reference: {$reference}\n";
echo "   Status: {$pendingTransaction->status}\n\n";

// Test 2: Simulate webhook update to success
echo "🔄 Test 2: Simulating webhook update to success...\n";
$existingTransaction = Transaction::where('reference', $reference)
    ->where('site_id', $site->id)
    ->first();

if ($existingTransaction) {
    echo "   ✅ Found existing transaction ID: {$existingTransaction->id}\n";
    echo "   Old status: {$existingTransaction->status}\n";
    
    // Update the transaction
    $oldStatus = $existingTransaction->status;
    $existingTransaction->update([
        'status' => 'success',
        'amount' => 5000.00,
        'payment_method' => 'card',
        'customer_email' => 'test@example.com',
        'customer_name' => 'Test User',
        'description' => 'Test successful transaction',
        'metadata' => ['webhook_update' => true, 'updated_at' => now()],
    ]);
    
    echo "   ✅ Updated transaction to success\n";
    echo "   New status: {$existingTransaction->status}\n";
    echo "   Updated at: {$existingTransaction->updated_at}\n\n";
} else {
    echo "   ❌ Could not find existing transaction!\n\n";
}

// Test 3: Try to create another transaction with same reference
echo "🚫 Test 3: Attempting to create duplicate transaction...\n";
try {
    $duplicateTransaction = Transaction::create([
        'site_id' => $site->id,
        'business_profile_id' => $businessProfile->id,
        'reference' => $reference, // Same reference
        'amount' => 5000.00,
        'currency' => 'NGN',
        'status' => 'success',
        'payment_method' => 'card',
        'customer_email' => 'test@example.com',
        'customer_name' => 'Test User',
        'description' => 'Duplicate transaction',
    ]);
    echo "   ❌ Should have failed due to unique constraint!\n";
} catch (\Exception $e) {
    echo "   ✅ Correctly prevented duplicate transaction\n";
    echo "   Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Check final state
echo "📊 Test 4: Checking final transaction state...\n";
$finalTransaction = Transaction::where('reference', $reference)
    ->where('site_id', $site->id)
    ->first();

if ($finalTransaction) {
    echo "   ✅ Final transaction found:\n";
    echo "   ID: {$finalTransaction->id}\n";
    echo "   Reference: {$finalTransaction->reference}\n";
    echo "   Status: {$finalTransaction->status}\n";
    echo "   Amount: {$finalTransaction->amount}\n";
    echo "   Created: {$finalTransaction->created_at}\n";
    echo "   Updated: {$finalTransaction->updated_at}\n";
} else {
    echo "   ❌ No final transaction found!\n";
}

echo "\n🎉 Test completed!\n";
echo "The webhook logic should now properly update existing transactions instead of creating duplicates.\n"; 