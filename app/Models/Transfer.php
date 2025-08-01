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
        if ($this->is_approved === true) {
            return 'Approved';
        } elseif ($this->is_approved === false) {
            return 'Rejected';
        } else {
            return 'Pending';
        }
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->is_approved === true) {
            return 'bg-success';
        } elseif ($this->is_approved === false) {
            return 'bg-danger';
        } else {
            return 'bg-warning';
        }
    }
}
