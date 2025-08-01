<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'name',
        'url',
        'webhook_url',
        'api_code',
        'api_key',
        'daily_revenue',
        'monthly_revenue',
        'is_active',
        'is_archived',
        'archived_at',
        'allowed_ips',
    ];

    protected $casts = [
        'daily_revenue' => 'decimal:2',
        'monthly_revenue' => 'decimal:2',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
