<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_profile_id',
        'bank',
        'account_number',
        'account_name',
        'name',
        'bank_name',
        'account_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    // Accessor to get bank name (handles both 'bank' and 'bank_name' columns)
    public function getBankNameAttribute()
    {
        return $this->bank ?? $this->bank_name ?? 'Unknown Bank';
    }

    // Accessor to get account holder name (handles both 'account_name' and 'name' columns)
    public function getAccountHolderNameAttribute()
    {
        return $this->account_name ?? $this->name ?? 'Unknown';
    }

    // Mutator to set bank name
    public function setBankNameAttribute($value)
    {
        $this->attributes['bank'] = $value;
        $this->attributes['bank_name'] = $value;
    }

    // Mutator to set account holder name
    public function setAccountHolderNameAttribute($value)
    {
        $this->attributes['account_name'] = $value;
        $this->attributes['name'] = $value;
    }
}
