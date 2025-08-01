<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SuperAdmin;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create super admin user
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@xtrapay.com',
            'password' => Hash::make('superadmin123'),
            'is_admin' => true,
        ]);

        // Create super admin record
        SuperAdmin::create([
            'user_id' => $superAdminUser->id,
            'role' => 'super_admin',
            'permissions' => null, // Super admins have all permissions
            'is_active' => true,
        ]);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@xtrapay.com');
        $this->command->info('Password: superadmin123');
    }
} 