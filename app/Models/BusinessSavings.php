<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class BusinessSavings extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'monthly_goal',
        'current_savings',
        'last_collection_date',
        'daily_collections_count',
        'daily_collected_amount',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'monthly_goal' => 'decimal:2',
        'current_savings' => 'decimal:2',
        'last_collection_date' => 'datetime',
        'daily_collections_count' => 'integer',
        'daily_collected_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    /**
     * Calculate daily collection target based on monthly goal
     */
    public function calculateDailyTarget()
    {
        $daysInMonth = Carbon::now()->daysInMonth;
        $this->daily_collection_target = $this->monthly_goal / $daysInMonth;
        $this->save();
        return $this->daily_collection_target;
    }

    /**
     * Add to savings from a transaction
     */
    public function addToSavings($amount)
    {
        if (!$this->isCollectionDue()) {
            return false;
        }

        $this->current_savings += $amount;
        $this->last_collection_date = Carbon::today();
        $this->save();

        return true;
    }

    /**
     * Get the progress percentage toward monthly goal
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->monthly_goal <= 0) {
            return 0;
        }
        return ($this->current_savings / $this->monthly_goal) * 100;
    }

    /**
     * Get the remaining amount to reach monthly goal
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->monthly_goal - $this->current_savings);
    }

    /**
     * Check if savings collection is due (dynamic daily goal and max collections)
     */
    public function isCollectionDue(): bool
    {
        $dailyGoal = \App\Models\SavingsConfig::getValue('daily_goal', 80000);
        $maxDailyCollections = \App\Models\SavingsConfig::getValue('max_daily_collections', 5);
        
        // If no last collection, we can collect
        if (!$this->last_collection_date) {
            return true;
        }
        
        // Check if it's a new day
        $lastCollectionDate = $this->last_collection_date->format('Y-m-d');
        $today = now()->format('Y-m-d');
        
        if ($lastCollectionDate !== $today) {
            // New day, reset daily tracking and allow collection
            $this->daily_collections_count = 0;
            $this->daily_collected_amount = 0;
            $this->save();
            return true;
        }
        
        // Same day, check if we can collect more
        $collectedToday = $this->daily_collected_amount ?? 0;
        $collectionsToday = $this->daily_collections_count ?? 0;
        
        return $collectedToday < $dailyGoal && $collectionsToday < $maxDailyCollections;
    }

    /**
     * Get hours until next collection (fixed daily schedule at 11 AM)
     */
    public function getHoursUntilNextCollectionAttribute(): int
    {
        if (!$this->last_collection_date) {
            return 0;
        }
        
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $lastCollectionDate = $this->last_collection_date->format('Y-m-d');
        
        // If last collection was today, next collection is tomorrow at 11 AM
        if ($lastCollectionDate === $today) {
            $nextCollection = Carbon::tomorrow()->setTime(11, 0, 0);
        } else {
            // If last collection was yesterday or earlier, next collection is today at 11 AM
            $nextCollection = Carbon::today()->setTime(11, 0, 0);
            
            // If it's already past 11 AM today, next collection is tomorrow at 11 AM
            if ($now->hour >= 11) {
                $nextCollection = Carbon::tomorrow()->setTime(11, 0, 0);
            }
        }
        
        return max(0, $now->diffInHours($nextCollection, false));
    }
    
    /**
     * Get the next collection date and time
     */
    public function getNextCollectionDateTimeAttribute()
    {
        if (!$this->last_collection_date) {
            return null;
        }
        
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $lastCollectionDate = $this->last_collection_date->format('Y-m-d');
        
        // If last collection was today, next collection is tomorrow at 11 AM
        if ($lastCollectionDate === $today) {
            $nextCollection = Carbon::tomorrow()->setTime(11, 0, 0);
        } else {
            // If last collection was yesterday or earlier, next collection is today at 11 AM
            $nextCollection = Carbon::today()->setTime(11, 0, 0);
            
            // If it's already past 11 AM today, next collection is tomorrow at 11 AM
            if ($now->hour >= 11) {
                $nextCollection = Carbon::tomorrow()->setTime(11, 0, 0);
            }
        }
        
        return $nextCollection;
    }
} 