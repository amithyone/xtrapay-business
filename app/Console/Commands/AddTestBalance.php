<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\BusinessProfile;

class AddTestBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:add-test {email} {amount=100000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add test balance to a user\'s business profile';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $amount = $this->argument('amount');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $businessProfile = $user->businessProfile;

        if (!$businessProfile) {
            $this->error("Business profile not found for user {$email}!");
            return 1;
        }

        $oldBalance = $businessProfile->balance ?? 0;
        $businessProfile->balance = $oldBalance + $amount;
        $businessProfile->save();

        $this->info("Successfully added ₦" . number_format($amount, 2) . " to {$email}");
        $this->info("Old balance: ₦" . number_format($oldBalance, 2));
        $this->info("New balance: ₦" . number_format($businessProfile->balance, 2));

        return 0;
    }
}
