<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessProfile;
use App\Models\BusinessSavings;
use App\Services\SavingsCollectionService;

class BusinessSavingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $savingsService = new SavingsCollectionService();
        
        // Initialize savings for business ID 1
        $business = BusinessProfile::find(1);
        
        if ($business) {
            $savings = $savingsService->initializeSavings($business, 1600000); // ₦1.6M
            
            $this->command->info("Savings initialized for business: {$business->business_name}");
            $this->command->info("Monthly goal: ₦" . number_format($savings->monthly_goal, 2));
            $this->command->info("Daily target: ₦" . number_format($savings->daily_collection_target, 2));
            $this->command->info("Daily limit: {$savings->daily_transaction_limit} transactions");
        } else {
            $this->command->warn("Business ID 1 not found. Please create a business profile first.");
        }
    }
} 