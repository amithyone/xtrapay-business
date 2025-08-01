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
        'daily_collection_target',
        'daily_transaction_limit',
        'transactions_today',
        'last_collection_date',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'monthly_goal' => 'decimal:2',
        'current_savings' => 'decimal:2',
        'daily_collection_target' => 'decimal:2',
        'daily_transaction_limit' => 'integer',
        'transactions_today' => 'integer',
        'last_collection_date' => 'date',
        'is_active' => 'boolean',
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
     * Check if we can collect from a transaction today
     */
    public function canCollectToday()
    {
        $today = Carbon::today();
        
        // Reset daily counter if it's a new day
        if ($this->last_collection_date != $today) {
            $this->transactions_today = 0;
            $this->last_collection_date = $today;
            $this->save();
        }

        return $this->transactions_today < $this->daily_transaction_limit;
    }

    /**
     * Add to savings from a transaction
     */
    public function addToSavings($amount)
    {
        if (!$this->canCollectToday()) {
            return false;
        }

        $this->current_savings += $amount;
        $this->transactions_today += 1;
        $this->last_collection_date = Carbon::today();
        $this->save();

        return true;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->monthly_goal <= 0) return 0;
        return min(100, ($this->current_savings / $this->monthly_goal) * 100);
    }

    /**
     * Get remaining amount to reach monthly goal
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->monthly_goal - $this->current_savings);
    }

    /**
     * Get daily progress
     */
    public function getDailyProgressAttribute()
    {
        $today = Carbon::today();
        $daysInMonth = Carbon::now()->daysInMonth;
        $currentDay = Carbon::now()->day;
        
        $expectedSavings = ($this->monthly_goal / $daysInMonth) * $currentDay;
        return min(100, ($this->current_savings / $expectedSavings) * 100);
    }
} 