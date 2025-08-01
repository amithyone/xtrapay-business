<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        // Check if user has a business profile
        if (!$user->businessProfile) {
            return false;
        }
        
        // Check if the transaction belongs to one of the user's sites
        return $user->businessProfile->sites->contains($transaction->site_id);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        // Check if user has a business profile
        if (!$user->businessProfile) {
            return false;
        }
        
        // Check if the transaction belongs to one of the user's sites
        return $user->businessProfile->sites->contains($transaction->site_id);
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        // Check if user has a business profile
        if (!$user->businessProfile) {
            return false;
        }
        
        // Check if the transaction belongs to one of the user's sites
        return $user->businessProfile->sites->contains($transaction->site_id);
    }
} 