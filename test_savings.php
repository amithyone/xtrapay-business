<?php

require_once 'vendor/autoload.php';

use App\Models\BusinessProfile;
use App\Services\SavingsCollectionService;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing savings initialization...\n";

try {
    // Check if business profile exists
    $business = BusinessProfile::find(1);
    
    if (!$business) {
        echo "❌ Business profile with ID 1 not found\n";
        exit(1);
    }
    
    echo "✅ Found business: " . $business->business_name . "\n";
    
    // Check if savings already exists
    $existingSavings = $business->savings;
    
    if ($existingSavings) {
        echo "ℹ️  Savings already exists for this business\n";
        echo "   Monthly Goal: ₦" . number_format($existingSavings->monthly_goal, 2) . "\n";
        echo "   Current Savings: ₦" . number_format($existingSavings->current_savings, 2) . "\n";
        echo "   Is Active: " . ($existingSavings->is_active ? 'Yes' : 'No') . "\n";
    } else {
        echo "ℹ️  No savings found for this business\n";
    }
    
    // Test the savings service
    $savingsService = new SavingsCollectionService();
    
    // Test initialization
    $savings = $savingsService->initializeSavings($business, 2400000);
    
    echo "✅ Savings initialized successfully!\n";
    echo "   Monthly Goal: ₦" . number_format($savings->monthly_goal, 2) . "\n";
    echo "   Daily Target: ₦" . number_format($savings->daily_collection_target, 2) . "\n";
    echo "   Is Active: " . ($savings->is_active ? 'Yes' : 'No') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 