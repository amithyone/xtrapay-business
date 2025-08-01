<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AbandonPendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:abandon-pending {--hours=6 : Hours after which to abandon pending transactions} {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark pending transactions as abandoned after specified hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = Carbon::now()->subHours($hours);
        
        $this->info("🔍 Checking for pending transactions older than {$hours} hours...");
        $this->info("Cutoff time: {$cutoffTime->format('Y-m-d H:i:s')}");

        // Find pending transactions older than specified hours
        $pendingTransactions = Transaction::where('status', 'pending')
            ->where('created_at', '<', $cutoffTime)
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('✅ No pending transactions found that need to be abandoned.');
            return;
        }

        $this->warn("Found {$pendingTransactions->count()} pending transactions to abandon:");

        foreach ($pendingTransactions as $transaction) {
            $this->line("ID: {$transaction->id}, Reference: {$transaction->reference}, Created: {$transaction->created_at->format('Y-m-d H:i:s')}, Age: " . $transaction->created_at->diffForHumans());
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run mode - no changes will be made');
            return;
        }

        if (!$this->confirm("Do you want to mark these {$pendingTransactions->count()} transactions as abandoned?")) {
            $this->info('❌ Operation cancelled');
            return;
        }

        $abandonedCount = 0;

        foreach ($pendingTransactions as $transaction) {
            $oldStatus = $transaction->status;
            
            // Update transaction status to abandoned
            $transaction->update([
                'status' => 'abandoned',
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'abandoned_at' => now()->toISOString(),
                    'abandoned_reason' => "Auto-abandoned after {$hours} hours of pending status",
                    'abandoned_by' => 'system'
                ])
            ]);

            Log::info('Transaction auto-abandoned', [
                'transaction_id' => $transaction->id,
                'reference' => $transaction->reference,
                'old_status' => $oldStatus,
                'new_status' => 'abandoned',
                'abandoned_after_hours' => $hours,
                'site_id' => $transaction->site_id
            ]);

            $this->line("✅ Abandoned transaction ID: {$transaction->id} (Reference: {$transaction->reference})");
            $abandonedCount++;
        }

        $this->info("✅ Successfully abandoned {$abandonedCount} transactions.");
    }
} 