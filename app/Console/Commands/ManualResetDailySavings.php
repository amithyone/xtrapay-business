<?php

namespace App\Console\Commands;

use App\Models\BusinessSavings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManualResetDailySavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:manual-reset {--business-id=1 : Business profile ID to reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually reset daily savings tracking fields for immediate fix';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->option('business-id');

        $this->info("🔄 Starting manual daily savings reset for Business ID: {$businessId}");

        $savings = BusinessSavings::where('business_profile_id', $businessId)
                                 ->where('is_active', true)
                                 ->first();

        if (!$savings) {
            $this->error("❌ No active savings found for Business ID {$businessId}!");
            return 1;
        }

        $this->info("📊 Current daily tracking:");
        $this->info("   Collections today: {$savings->daily_collections_count}");
        $this->info("   Amount collected today: ₦" . number_format($savings->daily_collected_amount, 2));
        $this->info("   Last collection: " . ($savings->last_collection_date ? $savings->last_collection_date->format('Y-m-d H:i:s') : 'Never'));

        if ($this->confirm('Do you want to reset the daily tracking fields?')) {
            $savings->daily_collections_count = 0;
            $savings->daily_collected_amount = 0;
            $savings->save();

            Log::info('Manual daily savings reset completed', [
                'business_profile_id' => $businessId,
                'reset_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'previous_daily_collections' => $savings->daily_collections_count,
                'previous_daily_amount' => $savings->daily_collected_amount
            ]);

            $this->info("✅ Daily tracking reset successfully!");
            $this->info("   Collections today: 0");
            $this->info("   Amount collected today: ₦0.00");
            $this->info("   Savings collection can now proceed normally.");
        } else {
            $this->info("❌ Reset cancelled.");
        }

        return 0;
    }
}
