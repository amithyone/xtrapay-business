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

        // Check if we can collect today (twice a day - every 12 hours)
        $lastCollection = $savings->last_collection_date;
        $now = now();
        
        // If no last collection or more than 12 hours have passed
        if (!$lastCollection || $now->diffInHours($lastCollection) >= 12) {
            $collectionAmount = 40000; // Fixed ₦40,000
            
            // Check if business has sufficient balance
            if ($businessProfile->balance >= $collectionAmount) {
                // Deduct from business balance
                $businessProfile->decrement('balance', $collectionAmount);
                
                // Add to savings
                $savings->current_savings += $collectionAmount;
                $savings->last_collection_date = $now;
                $savings->save();
                
                Log::info('Savings collected - twice daily deduction', [
                    'transaction_id' => $transaction->id,
                    'business_id' => $businessProfile->id,
                    'collection_amount' => $collectionAmount,
                    'business_balance_after' => $businessProfile->balance,
                    'current_savings' => $savings->current_savings,
                    'last_collection_date' => $savings->last_collection_date
                ]);

                return [
                    'collected' => true,
                    'amount' => $collectionAmount,
                    'percentage' => 0, // Not percentage-based anymore
                    'current_savings' => $savings->current_savings,
                    'progress' => $savings->progress_percentage,
                    'business_balance_deducted' => $collectionAmount,
                    'collection_type' => 'twice_daily'
                ];
            } else {
                Log::info('Insufficient business balance for savings collection', [
                    'transaction_id' => $transaction->id,
                    'business_id' => $businessProfile->id,
                    'required_amount' => $collectionAmount,
                    'available_balance' => $businessProfile->balance
                ]);
            }
        } else {
            Log::info('Savings collection not due yet - waiting for 12-hour interval', [
                'transaction_id' => $transaction->id,
                'business_id' => $businessProfile->id,
                'last_collection' => $lastCollection,
                'hours_since_last' => $now->diffInHours($lastCollection)
            ]);
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