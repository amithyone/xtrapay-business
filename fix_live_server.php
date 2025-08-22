<?php

// Quick fix script for live server
// Run this on your live server to create missing tables

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Starting live server database fix...\n";

try {
    // Check if super_admins table exists
    if (!Schema::hasTable('super_admins')) {
        echo "Creating super_admins table...\n";
        
        Schema::create('super_admins', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('super_admin');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add migration record
        DB::table('migrations')->insert([
            'migration' => '2025_06_17_000000_create_super_admins_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "super_admins table created successfully!\n";
    } else {
        echo "super_admins table already exists.\n";
    }
    
    // Check if there are any super admin users in the users table
    $superAdminUsers = DB::table('users')->where('is_admin', 1)->get();
    
    if ($superAdminUsers->count() > 0) {
        echo "Found " . $superAdminUsers->count() . " super admin user(s) in users table.\n";
        
        foreach ($superAdminUsers as $user) {
            // Check if super admin record already exists for this user
            $existingSuperAdmin = DB::table('super_admins')->where('user_id', $user->id)->first();
            
            if (!$existingSuperAdmin) {
                echo "Creating super admin record for user ID: " . $user->id . " (" . $user->email . ")\n";
                
                DB::table('super_admins')->insert([
                    'user_id' => $user->id,
                    'role' => 'super_admin',
                    'permissions' => json_encode([
                        'manage_users',
                        'manage_businesses', 
                        'manage_withdrawals',
                        'manage_tickets',
                        'manage_balance',
                        'view_reports',
                        'manage_sites',
                        'manage_transactions'
                    ]),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                echo "✅ Super admin record created for user: " . $user->email . "\n";
            } else {
                echo "Super admin record already exists for user: " . $user->email . "\n";
            }
        }
    } else {
        echo "No super admin users found in users table.\n";
        echo "You may need to manually create a super admin record.\n";
    }
    
    // Check if business_profiles has ledger_balance column
    if (!Schema::hasColumn('business_profiles', 'ledger_balance')) {
        echo "Adding ledger_balance column to business_profiles...\n";
        
        Schema::table('business_profiles', function ($table) {
            $table->decimal('ledger_balance', 15, 2)->default(0)->comment('Actual business ledger balance (super admin managed)');
        });
        
        echo "ledger_balance column added successfully!\n";
    }
    
    // Check if business_profiles has withdrawable_balance column
    if (!Schema::hasColumn('business_profiles', 'withdrawable_balance')) {
        echo "Adding withdrawable_balance column to business_profiles...\n";
        
        Schema::table('business_profiles', function ($table) {
            $table->decimal('withdrawable_balance', 15, 2)->default(0);
            $table->text('balance_notes')->nullable();
            $table->timestamp('last_balance_update')->nullable();
        });
        
        // Add migration record
        DB::table('migrations')->insert([
            'migration' => '2025_07_13_000404_add_balance_fields_to_business_profiles_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "withdrawable_balance column added successfully!\n";
    }
    
    // Check if business_savings table exists
    if (!Schema::hasTable('business_savings')) {
        echo "Creating business_savings table...\n";
        
        Schema::create('business_savings', function ($table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained()->onDelete('cascade');
            $table->decimal('monthly_goal', 15, 2)->default(1600000);
            $table->decimal('current_savings', 15, 2)->default(0);
            $table->decimal('daily_collection_target', 15, 2)->default(0);
            $table->integer('daily_transaction_limit')->default(5);
            $table->integer('transactions_today')->default(0);
            $table->date('last_collection_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Add migration record
        DB::table('migrations')->insert([
            'migration' => '2025_08_01_110000_create_business_savings_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "business_savings table created successfully!\n";
    }
    
    // Check if savings_configs table exists
    if (!Schema::hasTable('savings_configs')) {
        echo "Creating savings_configs table...\n";
        
        Schema::create('savings_configs', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('settings');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add migration record
        DB::table('migrations')->insert([
            'migration' => '2025_08_22_063439_create_savings_configs_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        echo "savings_configs table created successfully!\n";
    }
    
    echo "\n✅ All database fixes completed successfully!\n";
    echo "Your application should now work without the 'super_admins table not found' error.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
