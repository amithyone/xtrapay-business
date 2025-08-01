            // Prepare the webhook payload
            $payload = [
                'site_api_code' => $apiCode,
                'reference' => $deposit->ref_id,
                'amount' => $deposit->amount,
                'currency' => 'NGN',
                'status' => $status === 'successful' ? 'success' : $status,
                'payment_method' => 'xtrapay',
                'customer_email' => $user->email,
                'customer_name' => $user->name,
                'description' => 'Deposit via Xtrapay',
                'external_id' => (string)$deposit->id,
                'metadata' => [
                    'deposit_id' => $deposit->id,
                    'user_id' => $user->id,
                    'final_amount' => $deposit->final_amount,
                    'charge' => $deposit->charge,
                    'payment_reference' => $deposit->ref_id,
                    'site_name' => 'faddedsmm.com',
                    'site_url' => 'https://faddedsmm.com'
                ],
                'timestamp' => $deposit->created_at->toISOString()
            ]; 