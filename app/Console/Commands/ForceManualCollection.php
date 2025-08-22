<?php

namespace App\Console\Commands;

use App\Models\BusinessProfile;
use App\Models\BusinessSavings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ForceManualCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:force-collect {--business-id=1 : Business profile ID} {--amount=20000 : Amount to collect}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force manual savings collection bypassing daily limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->option('business-id');
        $amount = (float) $this->option('amount');

        $this->info("🔄 Starting FORCE manual savings collection for Business ID: {$businessId}");
        $this->info("💰 Amount to collect: ₦" . number_format($amount, 2));

        // Get business profile
        $businessProfile = BusinessProfile::find($businessId);
        if (!$businessProfile) {
            $this->error("❌ Business Profile ID {$businessId} not found!");
            return 1;
        }

        // Get or create savings
        $savings = $businessProfile->savings;
        if (!$savings) {
            $this->error("❌ No savings record found for Business ID {$businessId}");
            return 1;
        }

        // Simple balance check
        $businessBalance = (float) $businessProfile->balance;
        
        $this->info("💳 Business Balance: ₦" . number_format($businessBalance, 2));
        $this->info("💰 Required Amount: ₦" . number_format($amount, 2));

        if ($businessBalance < $amount) {
            $this->error("❌ Insufficient balance!");
            $this->error("💳 Available: ₦" . number_format($businessBalance, 2));
            $this->error("💰 Required: ₦" . number_format($amount, 2));
            return 1;
        }

        // Perform the collection
        try {
            // Deduct from business balance
            $businessProfile->balance = $businessBalance - $amount;
            $businessProfile->save();

            // Add to savings
            $savings->current_savings += $amount;
            $savings->daily_collected_amount += $amount;
            $savings->daily_collections_count += 1;
            $savings->last_collection_date = now();
            $savings->save();

            $this->info("✅ FORCE collection successful!");
            $this->info("💰 Amount collected: ₦" . number_format($amount, 2));
            $this->info("💳 New business balance: ₦" . number_format($businessProfile->balance, 2));
            $this->info("🏦 New savings total: ₦" . number_format($savings->current_savings, 2));
            $this->info("📊 Daily collected today: ₦" . number_format($savings->daily_collected_amount, 2));
            $this->info("🔢 Daily collections count: " . $savings->daily_collections_count);

            Log::info('Force manual savings collection completed', [
                'business_id' => $businessId,
                'amount_collected' => $amount,
                'new_business_balance' => $businessProfile->balance,
                'new_savings_total' => $savings->current_savings,
                'daily_collected_amount' => $savings->daily_collected_amount,
                'daily_collections_count' => $savings->daily_collections_count
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error during collection: " . $e->getMessage());
            Log::error('Force manual savings collection failed', [
                'business_id' => $businessId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }
}
