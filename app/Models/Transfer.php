<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'reference',
        'amount',
        'currency',
        'status',
        'recipient_bank',
        'recipient_account_number',
        'recipient_account_name',
        'narration',
        'metadata',
        'type',
        'beneficiary_id',
        'is_approved',
        'processed_by',
        'admin_notes',
        'processed_at',
        'processing_method'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'is_approved' => 'boolean',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Get the status text for display
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Pending';
            case 'completed':
                return 'Completed';
            case 'failed':
                return 'Failed';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'bg-warning';
            case 'completed':
                return 'bg-success';
            case 'failed':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Check if withdrawal is pending approval
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if withdrawal is completed
     */
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if withdrawal is failed
     */
    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }
}
