<?php

namespace App\Console\Commands;

use App\Models\BusinessProfile;
use App\Models\Transaction;
use App\Services\SavingsCollectionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TriggerSavingsCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'savings:trigger {--business-id=1 : Business profile ID to trigger savings for} {--count=6 : Number of random transactions to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually trigger savings collection from random successful transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $businessId = $this->option('business-id');
        $count = $this->option('count');

        $this->info("🎯 Triggering savings collection for Business ID: {$businessId}");
        $this->info("📊 Processing {$count} random transactions...\n");

        $businessProfile = BusinessProfile::find($businessId);
        if (!$businessProfile) {
            $this->error("❌ Business Profile ID {$businessId} not found!");
            return 1;
        }

        $savings = $businessProfile->savings;
        if (!$savings || !$savings->is_active) {
            $this->error("❌ No active savings found for Business ID {$businessId}!");
            return 1;
        }

        $this->info("✅ Business: {$businessProfile->business_name}");
        $this->info("💰 Current Balance: ₦" . number_format($businessProfile->balance, 2));
        $this->info("🏦 Current Savings: ₦" . number_format($savings->current_savings, 2));
        $this->info("🎯 Monthly Goal: ₦" . number_format($savings->monthly_goal, 2));
        $this->info("📈 Progress: " . round($savings->progress_percentage, 2) . "%");
        
        if ($savings->last_collection_date) {
            $this->info("🕐 Last Collection: " . $savings->last_collection_date->format('Y-m-d H:i:s'));
            $this->info("⏰ Hours Until Next: " . $savings->hours_until_next_collection);
        } else {
            $this->info("🕐 Last Collection: Never");
        }
        
        $this->info("");

        // Check if collection is due
        if (!$savings->isCollectionDue()) {
            $this->warn("⚠️  Collection not due yet. Waiting for 24-hour interval.");
            $this->info("⏰ Next collection in: " . $savings->hours_until_next_collection . " hours");
            return 0;
        }

        $this->info("✅ Collection is due! Processing...\n");

        // Get a single recent successful transaction to trigger collection
        $transaction = Transaction::where('business_profile_id', $businessId)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subDays(30))
            ->latest()
            ->first();

        if (!$transaction) {
            $this->error("❌ No successful transactions found for Business ID {$businessId}!");
            return 1;
        }

        $this->info("🔄 Processing Transaction: {$transaction->reference}");
        $this->info("   💰 Amount: ₦" . number_format($transaction->amount, 2));
        $this->info("   📅 Date: {$transaction->created_at->format('Y-m-d H:i')}");

        $savingsService = new SavingsCollectionService();
        $result = $savingsService->processTransaction($transaction);

        if ($result && $result['collected']) {
            $this->info("   ✅ Collected: ₦" . number_format($result['amount'], 2) . " (Twice Daily Deduction)");
            $this->info("   💳 Deducted from balance: ₦" . number_format($result['business_balance_deducted'], 2));
        } else {
            $this->info("   ❌ No collection - Insufficient balance or not due yet");
        }

        // Refresh data
        $businessProfile->refresh();
        $savings->refresh();

        $this->info("\n🎉 Savings Collection Complete!");
        $this->info("📊 Results:");
        $this->info("   💰 Amount collected: ₦" . number_format($result['amount'] ?? 0, 2));
        $this->info("   💳 New business balance: ₦" . number_format($businessProfile->balance, 2));
        $this->info("   🏦 New savings total: ₦" . number_format($savings->current_savings, 2));
        $this->info("   📈 New progress: " . round($savings->progress_percentage, 2) . "%");
        $this->info("   🕐 Next collection: " . ($savings->next_collection_date_time ? $savings->next_collection_date_time->format('Y-m-d H:i:s') : 'N/A'));

        return 0;
    }
} 