<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if super admin has specific permission
     */
    public function hasPermission($permission)
    {
        if ($this->role === 'super_admin') {
            return true; // Super admins have all permissions
        }

        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Get all available permissions
     */
    public static function getAvailablePermissions()
    {
        return [
            'manage_users' => 'Manage Users',
            'manage_businesses' => 'Manage Businesses',
            'manage_withdrawals' => 'Manage Withdrawals',
            'manage_tickets' => 'Manage Support Tickets',
            'manage_balance' => 'Manage Business Balances',
            'view_reports' => 'View Reports',
            'manage_sites' => 'Manage Sites',
            'manage_transactions' => 'Manage Transactions',
        ];
    }

    /**
     * Check if user is super admin
     */
    public static function isSuperAdmin($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get super admin by user ID
     */
    public static function getByUserId($userId)
    {
        return static::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }
} 