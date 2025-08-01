<?php

namespace App\Services;

use App\Models\BusinessProfile;
use App\Models\BusinessSavings;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SavingsCollectionService
{
    /**
     * Process a successful transaction for potential savings collection
     */
    public function processTransaction(Transaction $transaction)
    {
        // Only process successful transactions
        if ($transaction->status !== 'success') {
            return false;
        }

        // Get the business profile
        $businessProfile = $transaction->site->businessProfile;
        
        if (!$businessProfile) {
            return false;
        }

        // Get or create savings record for business ID 1
        if ($businessProfile->id !== 1) {
            return false; // Only for business ID 1
        }

        $savings = $businessProfile->savings;
        
        if (!$savings || !$savings->is_active) {
            return false;
        }

        // Calculate daily target if not set
        if ($savings->daily_collection_target <= 0) {
            $savings->calculateDailyTarget();
        }

        // Check if we can collect today
        if (!$savings->canCollectToday()) {
            Log::info('Savings collection limit reached for today', [
                'business_id' => $businessProfile->id,
                'transactions_today' => $savings->transactions_today,
                'limit' => $savings->daily_transaction_limit
            ]);
            return false;
        }

        // Calculate collection amount (random percentage between 5-15% of transaction)
        $collectionPercentage = rand(5, 15) / 100;
        $collectionAmount = $transaction->amount * $collectionPercentage;

        // Ensure we don't exceed daily target
        $dailyCollected = $savings->current_savings - $savings->getRemainingAmountAttribute();
        $remainingDailyTarget = $savings->daily_collection_target - $dailyCollected;
        
        if ($remainingDailyTarget <= 0) {
            return false;
        }

        // Limit collection amount to remaining daily target
        $collectionAmount = min($collectionAmount, $remainingDailyTarget);

        // Add to savings
        if ($savings->addToSavings($collectionAmount)) {
            Log::info('Savings collected from transaction', [
                'transaction_id' => $transaction->id,
                'business_id' => $businessProfile->id,
                'transaction_amount' => $transaction->amount,
                'collection_amount' => $collectionAmount,
                'collection_percentage' => $collectionPercentage * 100,
                'current_savings' => $savings->current_savings,
                'transactions_today' => $savings->transactions_today
            ]);

            return [
                'collected' => true,
                'amount' => $collectionAmount,
                'percentage' => $collectionPercentage * 100,
                'current_savings' => $savings->current_savings,
                'progress' => $savings->progress_percentage
            ];
        }

        return false;
    }

    /**
     * Initialize savings for a business
     */
    public function initializeSavings(BusinessProfile $businessProfile, $monthlyGoal = 1600000)
    {
        $savings = $businessProfile->savings()->firstOrCreate([
            'business_profile_id' => $businessProfile->id
        ], [
            'monthly_goal' => $monthlyGoal,
            'current_savings' => 0,
            'daily_collection_target' => 0,
            'daily_transaction_limit' => 5,
            'transactions_today' => 0,
            'is_active' => true
        ]);

        // Calculate daily target
        $savings->calculateDailyTarget();

        return $savings;
    }

    /**
     * Get savings statistics for a business
     */
    public function getSavingsStats(BusinessProfile $businessProfile)
    {
        $savings = $businessProfile->savings;
        
        if (!$savings) {
            return null;
        }

        return [
            'monthly_goal' => $savings->monthly_goal,
            'current_savings' => $savings->current_savings,
            'daily_target' => $savings->daily_collection_target,
            'progress_percentage' => $savings->progress_percentage,
            'remaining_amount' => $savings->remaining_amount,
            'daily_progress' => $savings->daily_progress,
            'transactions_today' => $savings->transactions_today,
            'daily_limit' => $savings->daily_transaction_limit,
            'is_active' => $savings->is_active,
            'last_collection_date' => $savings->last_collection_date
        ];
    }
} 