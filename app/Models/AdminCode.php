<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminCode extends Model
{
    protected $fillable = [
        'code_type',
        'hashed_code'
    ];
} 