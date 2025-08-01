<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:cleanup-duplicates {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identify and clean up duplicate transactions with the same reference and site_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking for duplicate transactions...');

        // Find duplicate transactions
        $duplicates = DB::table('transactions')
            ->select('reference', 'site_id', DB::raw('COUNT(*) as count'))
            ->groupBy('reference', 'site_id')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('✅ No duplicate transactions found!');
            return;
        }

        $this->warn("Found {$duplicates->count()} sets of duplicate transactions:");

        foreach ($duplicates as $duplicate) {
            $this->line("Reference: {$duplicate->reference}, Site ID: {$duplicate->site_id}, Count: {$duplicate->count}");
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run mode - no changes will be made');
            return;
        }

        if (!$this->confirm('Do you want to clean up these duplicates? This will keep the most recent transaction and delete older ones.')) {
            $this->info('❌ Cleanup cancelled');
            return;
        }

        $deletedCount = 0;

        foreach ($duplicates as $duplicate) {
            // Get all transactions with this reference and site_id
            $transactions = Transaction::where('reference', $duplicate->reference)
                ->where('site_id', $duplicate->site_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Keep the most recent one, delete the rest
            $keepTransaction = $transactions->first();
            $transactionsToDelete = $transactions->slice(1);

            foreach ($transactionsToDelete as $transaction) {
                $this->line("Deleting transaction ID: {$transaction->id} (Reference: {$transaction->reference}, Status: {$transaction->status}, Created: {$transaction->created_at})");
                $transaction->delete();
                $deletedCount++;
            }
        }

        $this->info("✅ Cleanup completed! Deleted {$deletedCount} duplicate transactions.");
    }
} 