<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SavingsConfig;

class SavingsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Initializing savings configurations...');
        
        SavingsConfig::initializeDefaults();
        
        $this->command->info('✅ Savings configurations initialized successfully!');
        
        // Display the configurations
        $configs = SavingsConfig::all();
        foreach ($configs as $config) {
            $this->command->line("  • {$config->key}: {$config->value} ({$config->type})");
        }
    }
}
