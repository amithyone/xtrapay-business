<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_profile_id',
        'subject',
        'message',
        'category',
        'priority',
        'status',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'resolution_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessProfile()
    {
        return $this->belongsTo(BusinessProfile::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function getInitialMessageAttribute()
    {
        return $this->messages()->where('is_support', false)->first();
    }

    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }
} 