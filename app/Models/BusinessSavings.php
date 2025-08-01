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
        'is_active',
        'notes'
    ];

    protected $casts = [
        'monthly_goal' => 'decimal:2',
        'current_savings' => 'decimal:2',
        'last_collection_date' => 'datetime',
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
     * Check if savings collection is due (every 12 hours)
     */
    public function isCollectionDue(): bool
    {
        if (!$this->last_collection_date) {
            return true;
        }
        
        return now()->diffInHours($this->last_collection_date) >= 12;
    }

    /**
     * Get hours until next collection
     */
    public function getHoursUntilNextCollectionAttribute(): int
    {
        if (!$this->last_collection_date) {
            return 0;
        }
        
        $hoursSinceLast = now()->diffInHours($this->last_collection_date);
        return max(0, 12 - $hoursSinceLast);
    }
} 