<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'api_token',
        'bvn',
        'date_of_birth',
        'pin',
        'profile_photo',
        'gender',
        'phone_number',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    public function businessProfile()
    {
        return $this->hasOne(BusinessProfile::class);
    }

    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        // User must be admin AND have active super admin record
        try {
            // Check if super_admins table exists first
            if (!\Schema::hasTable('super_admins')) {
                return $this->is_admin;
            }
            return $this->is_admin && $this->superAdmin()->where('is_active', true)->exists();
        } catch (\Exception $e) {
            // If any error occurs, just check is_admin
            return $this->is_admin;
        }
    }

    /**
     * Check if user has super admin permission
     */
    public function hasSuperAdminPermission($permission)
    {
        return $this->isSuperAdmin() && $this->superAdmin->hasPermission($permission);
    }

    /**
     * Get the user's profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Check if user has completed profile
     */
    public function hasCompletedProfile()
    {
        return !empty($this->phone_number) && !empty($this->date_of_birth) && !empty($this->gender);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }
}
