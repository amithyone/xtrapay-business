<?php

namespace App\Console\Commands;

use App\Models\BusinessProfile;
use App\Models\Transaction;
use App\Services\SavingsCollectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoSavingsCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:auto-collect {--business-id=1 : Business profile ID to collect savings for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically collect savings every 12 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->option('business-id');

        $this->info("🔄 Starting automatic savings collection for Business ID: {$businessId}");

        $businessProfile = BusinessProfile::find($businessId);
        if (!$businessProfile) {
            $this->error("❌ Business Profile ID {$businessId} not found!");
            return 1;
        }

        $savings = $businessProfile->savings;
        if (!$savings || !$savings->is_active) {
            $this->info("ℹ️  No active savings found for Business ID {$businessId} - skipping collection");
            return 0;
        }

        // Check if collection is due (every 12 hours)
        if (!$savings->isCollectionDue()) {
            $this->info("⏰ Collection not due yet for Business ID {$businessId}");
            $this->info("🕐 Next collection in: {$savings->hours_until_next_collection} hours");
            return 0;
        }

        // Check if business has sufficient balance (minimum ₦15,000 for collection)
        $minBalanceRequired = 15000;
        if ((float) $businessProfile->balance < $minBalanceRequired) {
            $this->warn("⚠️  Insufficient balance for Business ID {$businessId}");
            $this->info("💰 Required: ₦" . number_format($minBalanceRequired, 2) . ", Available: ₦" . number_format($businessProfile->balance, 2));
            return 0;
        }

        // Get a recent transaction to trigger collection
        $transaction = Transaction::where('business_profile_id', $businessId)
            ->where('status', 'success')
            ->latest()
            ->first();

        if (!$transaction) {
            $this->warn("⚠️  No successful transactions found for Business ID {$businessId}");
            return 0;
        }

        $savingsService = new SavingsCollectionService();
        $result = $savingsService->processTransaction($transaction);

        if ($result && $result['collected']) {
            $this->info("✅ Automatic savings collection successful!");
            $this->info("💰 Amount collected: ₦" . number_format($result['amount'], 2));
            $this->info("💳 Balance deducted: ₦" . number_format($result['business_balance_deducted'], 2));
            $this->info("🏦 New savings total: ₦" . number_format($result['current_savings'], 2));
            $this->info("📈 Progress: " . round($result['progress'], 2) . "%");

            Log::info('Automatic savings collection completed', [
                'business_id' => $businessId,
                'amount_collected' => $result['amount'],
                'business_balance_deducted' => $result['business_balance_deducted'],
                'current_savings' => $result['current_savings'],
                'progress' => $result['progress']
            ]);
        } else {
            $this->warn("⚠️  Automatic savings collection failed");
            $this->info("📝 Reason: Collection not due or insufficient balance");
        }

        return 0;
    }
} 