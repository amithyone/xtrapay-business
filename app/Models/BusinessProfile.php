<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'registration_number',
        'tax_identification_number',
        'business_type',
        'industry',
        'address',
        'city',
        'state',
        'country',
        'phone',
        'email',
        'website',
        'logo',
        'verification_id_type',
        'verification_id_number',
        'verification_id_file',
        'proof_of_address_file',
        'is_verified',
        'pin',
        'balance',
        'actual_balance',
        'withdrawable_balance',
        'total_revenue',
        'total_withdrawals',
        'pending_withdrawals',
        'last_balance_update',
        'balance_notes',
        'telegram_bot_token',
        'telegram_chat_id'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'actual_balance' => 'decimal:2',
        'withdrawable_balance' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'pending_withdrawals' => 'decimal:2',
        'last_balance_update' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function savings()
    {
        return $this->hasOne(BusinessSavings::class);
    }

    /**
     * Get total revenue from all sites' successful transactions
     */
    public function getTotalRevenueAttribute()
    {
        return $this->sites->flatMap->transactions
            ->where('status', 'success')
            ->sum('amount');
    }

    /**
     * Get total withdrawals (completed)
     */
    public function getTotalWithdrawalsAttribute()
    {
        return $this->transfers->where('status', 'completed')->sum('amount');
    }

    /**
     * Get pending withdrawals
     */
    public function getPendingWithdrawalsAttribute()
    {
        return $this->transfers->where('status', 'pending')->sum('amount');
    }

    /**
     * Get failed withdrawals
     */
    public function getFailedWithdrawalsAttribute()
    {
        return $this->transfers->where('status', 'failed')->sum('amount');
    }
}
