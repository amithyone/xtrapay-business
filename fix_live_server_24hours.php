<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing 24-hour interval on live server...\n\n";

try {
    // 1. Update the savings config to use 24 hours
    echo "📝 Updating savings configuration...\n";
    
    $existingConfig = DB::table('savings_configs')
        ->where('key', 'collection_interval_hours')
        ->first();
    
    if ($existingConfig) {
        DB::table('savings_configs')
            ->where('key', 'collection_interval_hours')
            ->update([
                'value' => '24',
                'type' => 'integer',
                'updated_at' => now()
            ]);
        echo "✅ Updated existing collection_interval_hours to 24\n";
    } else {
        DB::table('savings_configs')->insert([
            'key' => 'collection_interval_hours',
            'value' => '24',
            'type' => 'integer',
            'description' => 'Hours between automatic collections',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        echo "✅ Created new collection_interval_hours config with value 24\n";
    }
    
    // 2. Check current savings data
    echo "\n📊 Checking current savings data...\n";
    $savings = DB::table('business_savings')->where('business_profile_id', 1)->first();
    
    if ($savings) {
        echo "Business Savings ID: " . $savings->id . "\n";
        echo "Last collection date: " . ($savings->last_collection_date ? $savings->last_collection_date : 'NULL') . "\n";
        echo "Is active: " . ($savings->is_active ? 'Yes' : 'No') . "\n";
        echo "Current savings: ₦" . number_format($savings->current_savings, 2) . "\n";
        
        // 3. Calculate correct hours until next collection
        if ($savings->last_collection_date) {
            $lastCollection = Carbon::parse($savings->last_collection_date);
            $now = Carbon::now();
            $hoursSinceLast = $now->diffInHours($lastCollection);
            $hoursUntilNext = max(0, 24 - $hoursSinceLast);
            
            echo "\n⏰ Time calculations:\n";
            echo "Last collection: " . $lastCollection->format('Y-m-d H:i:s') . "\n";
            echo "Current time: " . $now->format('Y-m-d H:i:s') . "\n";
            echo "Hours since last: " . $hoursSinceLast . "\n";
            echo "Hours until next (24h interval): " . $hoursUntilNext . "\n";
            echo "Next collection time: " . $lastCollection->addHours(24)->format('M d, H:i') . "\n";
        } else {
            echo "\n✅ No last collection date - should show 'Ready Now'\n";
        }
    } else {
        echo "❌ No savings record found for business ID 1\n";
    }
    
    // 4. Clear any caches
    echo "\n🧹 Clearing caches...\n";
    if (function_exists('shell_exec')) {
        shell_exec('php artisan cache:clear');
        shell_exec('php artisan config:clear');
        shell_exec('php artisan view:clear');
        echo "✅ Caches cleared\n";
    } else {
        echo "⚠️  Could not clear caches (shell_exec disabled)\n";
    }
    
    echo "\n🎉 Live server 24-hour interval fix completed!\n";
    echo "The savings collection should now use 24-hour intervals correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
