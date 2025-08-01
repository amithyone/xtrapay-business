<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminCode;

class AdminCodeSeeder extends Seeder
{
    public function run()
    {
        AdminCode::create([
            'code_type' => 'first_code',
            'hashed_code' => Hash::make('24685'),
        ]);

        AdminCode::create([
            'code_type' => 'second_code',
            'hashed_code' => Hash::make('2468'),
        ]);
    }
} 