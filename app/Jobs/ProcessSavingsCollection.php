<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\SavingsCollectionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSavingsCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $connection = 'sync';
    public $timeout = 120;

    protected $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(SavingsCollectionService $savingsService): void
    {
        try {
            $result = $savingsService->processTransaction($this->transaction);
            
            if ($result && $result['collected']) {
                Log::info('Savings collection job completed successfully', [
                    'transaction_id' => $this->transaction->id,
                    'collection_amount' => $result['amount'],
                    'collection_percentage' => $result['percentage'],
                    'current_savings' => $result['current_savings'],
                    'progress' => $result['progress']
                ]);
            } else {
                Log::info('Savings collection job completed - no collection', [
                    'transaction_id' => $this->transaction->id,
                    'reason' => 'Daily limit reached or savings inactive'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in savings collection job: ' . $e->getMessage(), [
                'transaction_id' => $this->transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 