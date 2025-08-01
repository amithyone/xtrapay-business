<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'superadmin:create {email} {password} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->argument('name') ?? 'Super Admin';

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            $this->error("User with email {$email} already exists!");
            return 1;
        }

        try {
            // Create user
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(), // Auto-verify the super admin
            ]);

            // Create super admin record
            SuperAdmin::create([
                'user_id' => $user->id,
                'role' => 'super_admin',
                'permissions' => null, // Super admins have all permissions
                'is_active' => true,
            ]);

            $this->info("Super Admin created successfully!");
            $this->info("Email: {$email}");
            $this->info("Password: {$password}");
            $this->info("Name: {$name}");
            $this->info("Access URL: /super-admin/dashboard");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error creating super admin: " . $e->getMessage());
            return 1;
        }
    }
} 