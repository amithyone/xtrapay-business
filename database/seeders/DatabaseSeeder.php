<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessProfile;
use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create business profile
        $businessProfile = BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => 'Test Business',
            'business_type' => 'retail',
            'industry' => 'Technology',
            'registration_number' => 'REG123456',
            'tax_identification_number' => 'TIN123456',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Nigeria',
            'phone' => '+2341234567890',
            'email' => 'business@example.com',
            'website' => 'https://example.com',
            'balance' => 1000000.00,
            'verification_id_type' => 'CAC',
            'verification_id_number' => 'CAC123456',
            'verification_id_file' => 'cac_file.pdf',
            'proof_of_address_file' => 'address_file.pdf',
        ]);

        // Create test sites
        $sites = [
            [
                'name' => 'Main Branch',
                'url' => 'https://mainbranch.example.com',
                'daily_revenue' => 50000.00,
                'monthly_revenue' => 1500000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Branch 2',
                'url' => 'https://branch2.example.com',
                'daily_revenue' => 30000.00,
                'monthly_revenue' => 900000.00,
                'is_active' => true,
            ],
        ];

        foreach ($sites as $siteData) {
            $site = Site::create(array_merge($siteData, ['business_profile_id' => $businessProfile->id]));

            // Create some test transactions for each site
            for ($i = 0; $i < 5; $i++) {
                Transaction::create([
                    'site_id' => $site->id,
                    'reference' => 'TRX-' . strtoupper(uniqid()),
                    'amount' => rand(1000, 50000),
                    'currency' => 'NGN',
                    'status' => ['pending', 'success', 'failed'][rand(0, 2)],
                    'payment_method' => ['card', 'bank_transfer', 'ussd'][rand(0, 2)],
                    'customer_email' => 'customer' . $i . '@example.com',
                    'customer_name' => 'Customer ' . $i,
                    'description' => 'Test transaction ' . $i,
                ]);
            }
        }

        $this->call([
            AdminCodeSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
