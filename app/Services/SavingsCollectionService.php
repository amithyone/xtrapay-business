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

        // Check if we can collect today (daily goal of ₦50,000, can be collected 2-5 times)
        $lastCollection = $savings->last_collection_date;
        $now = now();
        
        // Get today's collection count
        $todayCollections = $savings->daily_collections_count ?? 0;
        $maxDailyCollections = 5; // Maximum 5 collections per day
        $dailyGoal = 80000; // ₦80,000 daily goal
        
        // Calculate remaining amount for today
        $collectedToday = $savings->daily_collected_amount ?? 0;
        $remainingToday = $dailyGoal - $collectedToday;
        
        // Check if we can collect more today
        if ($todayCollections < $maxDailyCollections && $remainingToday > 0) {
            // Calculate collection amount (minimum ₦15,000, maximum remaining amount)
            $collectionAmount = min(max(15000, $remainingToday), 20000); // ₦15,000 to ₦20,000 per collection
            
            // Check if business has sufficient balance
            Log::info('🔍 BALANCE CHECK for savings collection', [
                'business_id' => $businessProfile->id,
                'business_balance' => $businessProfile->balance,
                'required_amount' => $collectionAmount,
                'balance_type' => gettype($businessProfile->balance),
                'amount_type' => gettype($collectionAmount)
            ]);
            
            if ($businessProfile->balance >= $collectionAmount) {
                // Deduct from business balance
                $businessProfile->decrement('balance', $collectionAmount);
                
                // Update savings with daily tracking
                $savings->current_savings += $collectionAmount;
                $savings->last_collection_date = $now;
                
                // Reset daily tracking if it's a new day
                if ($lastCollection && $lastCollection->format('Y-m-d') !== $now->format('Y-m-d')) {
                    $savings->daily_collections_count = 1;
                    $savings->daily_collected_amount = $collectionAmount;
                } else {
                    // Same day, increment the counters
                    $savings->daily_collections_count = $todayCollections + 1;
                    $savings->daily_collected_amount = $collectedToday + $collectionAmount;
                }
                
                $savings->save();
                
                Log::info('✅ SAVINGS COLLECTED - Daily deduction', [
                    'transaction_id' => $transaction->id,
                    'business_id' => $businessProfile->id,
                    'collection_amount' => $collectionAmount,
                    'business_balance_after' => $businessProfile->balance,
                    'current_savings' => $savings->current_savings,
                    'daily_collections_count' => $savings->daily_collections_count,
                    'daily_collected_amount' => $savings->daily_collected_amount,
                    'remaining_today' => $dailyGoal - $savings->daily_collected_amount,
                    'last_collection_date' => $savings->last_collection_date
                ]);

                return [
                    'collected' => true,
                    'amount' => $collectionAmount,
                    'percentage' => 0,
                    'current_savings' => $savings->current_savings,
                    'progress' => $savings->progress_percentage,
                    'business_balance_deducted' => $collectionAmount,
                    'collection_type' => 'daily_goal',
                    'daily_collections_count' => $savings->daily_collections_count,
                    'daily_collected_amount' => $savings->daily_collected_amount,
                    'remaining_today' => $dailyGoal - $savings->daily_collected_amount
                ];
            } else {
                Log::info('⚠️ INSUFFICIENT BUSINESS BALANCE for savings collection', [
                    'transaction_id' => $transaction->id,
                    'business_id' => $businessProfile->id,
                    'required_amount' => $collectionAmount,
                    'available_balance' => $businessProfile->balance,
                    'daily_collections_count' => $todayCollections,
                    'daily_collected_amount' => $collectedToday
                ]);
            }
        } else {
            Log::info('⏰ SAVINGS COLLECTION NOT DUE - Daily limit reached or max collections hit', [
                'transaction_id' => $transaction->id,
                'business_id' => $businessProfile->id,
                'daily_collections_count' => $todayCollections,
                'max_daily_collections' => $maxDailyCollections,
                'daily_collected_amount' => $collectedToday,
                'daily_goal' => $dailyGoal,
                'remaining_today' => $remainingToday,
                'last_collection' => $lastCollection
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