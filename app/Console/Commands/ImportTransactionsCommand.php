<?php

namespace App\Console\Commands;

use App\Jobs\ImportExternalTransactions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:import {--force : Force import even if no new data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import transactions from external databases for all configured sites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting transaction import process...');
        
        try {
            // Run the import job synchronously (better for cron)
            $job = new ImportExternalTransactions();
            $job->handle();
            
            $this->info('Transaction import completed successfully.');
            Log::info('Transaction import completed via command line');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to run transaction import: ' . $e->getMessage());
            Log::error('Failed to run transaction import: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
