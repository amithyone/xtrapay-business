<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class CleanupOrphanedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:cleanup-orphaned {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up transactions that have no associated site';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        // Find orphaned transactions (transactions with no associated site)
        $orphanedTransactions = Transaction::whereDoesntHave('site')->get();
        
        $count = $orphanedTransactions->count();
        
        if ($count === 0) {
            $this->info('✅ No orphaned transactions found.');
            return 0;
        }
        
        $this->warn("Found {$count} orphaned transaction(s):");
        
        // Display the orphaned transactions
        $this->table(
            ['ID', 'Reference', 'Amount', 'Status', 'Created At'],
            $orphanedTransactions->map(function ($transaction) {
                return [
                    $transaction->id,
                    $transaction->reference,
                    '₦' . number_format($transaction->amount, 2),
                    $transaction->status,
                    $transaction->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );
        
        if ($dryRun) {
            $this->info('🔍 Dry run mode: No transactions were deleted.');
            $this->info('Run without --dry-run to actually delete these transactions.');
            return 0;
        }
        
        if ($this->confirm("Are you sure you want to delete {$count} orphaned transaction(s)?")) {
            $deleted = Transaction::whereDoesntHave('site')->delete();
            $this->info("✅ Successfully deleted {$deleted} orphaned transaction(s).");
        } else {
            $this->info('❌ Operation cancelled.');
        }
        
        return 0;
    }
} 