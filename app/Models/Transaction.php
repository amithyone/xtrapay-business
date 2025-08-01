<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'external_id',
        'site_id',
        'business_profile_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'customer_email',
        'customer_name',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function businessProfile(): BelongsTo
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'success' => 'green',
            'failed' => 'red',
            default => 'yellow',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    /**
     * Check if transaction should be abandoned (pending for more than 6 hours)
     */
    public function shouldBeAbandoned(int $hours = 6): bool
    {
        return $this->status === 'pending' && 
               $this->created_at->diffInHours(now()) >= $hours;
    }

    /**
     * Get the age of the transaction in hours
     */
    public function getAgeInHoursAttribute(): int
    {
        return $this->created_at->diffInHours(now());
    }

    /**
     * Get the age of the transaction in a human-readable format
     */
    public function getAgeForHumansAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
