<?php

namespace App\Console\Commands;

use App\Models\BusinessSavings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ResetDailySavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:reset-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily savings tracking fields at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔄 Starting daily savings reset...");

        $today = Carbon::now()->format('Y-m-d');
        
        // Get all active savings records
        $savingsRecords = BusinessSavings::where('is_active', true)->get();
        
        $resetCount = 0;
        
        foreach ($savingsRecords as $savings) {
            $lastCollectionDate = $savings->last_collection_date;
            
            // Reset daily tracking if it's a new day or if last collection was not today
            if (!$lastCollectionDate || $lastCollectionDate->format('Y-m-d') !== $today) {
                $savings->daily_collections_count = 0;
                $savings->daily_collected_amount = 0;
                $savings->save();
                
                $resetCount++;
                
                Log::info('Daily savings reset completed', [
                    'business_profile_id' => $savings->business_profile_id,
                    'reset_date' => $today,
                    'previous_daily_collections' => $savings->daily_collections_count,
                    'previous_daily_amount' => $savings->daily_collected_amount
                ]);
                
                $this->info("✅ Reset daily tracking for Business ID: {$savings->business_profile_id}");
            } else {
                $this->info("⏭️  Skipping Business ID {$savings->business_profile_id} - already collected today");
            }
        }

        $this->info("🎉 Daily savings reset completed! Reset {$resetCount} business profiles.");
        
        Log::info('Daily savings reset process completed', [
            'reset_count' => $resetCount,
            'total_savings_records' => $savingsRecords->count(),
            'reset_date' => $today
        ]);

        return 0;
    }
}
