<?php

namespace App\Console\Commands;

use App\Jobs\ImportExternalTransactions;
use Illuminate\Console\Command;

class TestImportTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:import-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the import external transactions functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing import external transactions...');
        
        try {
            // Run the job synchronously instead of dispatching to queue
            $job = new \App\Jobs\ImportExternalTransactions();
            $job->handle();
            
            $this->info('Import job completed successfully!');
            $this->info('Check the logs for import results.');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
} 