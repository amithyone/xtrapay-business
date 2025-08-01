<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transfer;

class FixWithdrawalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fix completed withdrawals that don't have is_approved set
        $completedWithdrawals = Transfer::where('status', 'completed')
            ->where('is_approved', false)
            ->get();

        foreach ($completedWithdrawals as $withdrawal) {
            $withdrawal->update([
                'is_approved' => true,
                'processed_by' => 'System Fix',
                'processed_at' => now(),
                'processing_method' => 'system_fix'
            ]);
        }

        $this->command->info("Fixed {$completedWithdrawals->count()} completed withdrawals");

        // Show current status
        $pending = Transfer::where('status', 'pending')->count();
        $completed = Transfer::where('status', 'completed')->count();
        $failed = Transfer::where('status', 'failed')->count();

        $this->command->info("Current withdrawal statuses:");
        $this->command->info("- Pending: {$pending}");
        $this->command->info("- Completed: {$completed}");
        $this->command->info("- Failed: {$failed}");
    }
} 