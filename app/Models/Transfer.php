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
        'processed_by',
        'admin_notes',
        'processed_at',
        'processing_method'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
