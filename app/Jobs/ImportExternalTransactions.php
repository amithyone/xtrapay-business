<?php

namespace App\Jobs;

use App\Models\Site;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportExternalTransactions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Loop over all sites with field_mapping config
        $sites = Site::whereNotNull('field_mapping')->get();

        foreach ($sites as $site) {
            $config = $site->field_mapping;

            // Build external DB connection config dynamically
            $connectionName = 'external_site_' . $site->id;

            config([
                "database.connections.$connectionName" => [
                    'driver' => 'mysql',
                    'host' => $config['db_host'],
                    'port' => $config['db_port'] ?? '3306',
                    'database' => $config['db_name'],
                    'username' => $config['db_username'],
                    'password' => $config['db_password'],
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ],
            ]);

            try {
                $externalDB = DB::connection($connectionName);

                // Build query to join transaction and user tables
                $transactionTable = $config['transaction_table'];
                $userTable = $config['user_table'];

                // Parse status values
                $successStatusValues = array_filter(
                    array_map('trim', explode("\n", $config['success_status_values']))
                );
                $pendingStatusValues = array_filter(
                    array_map('trim', explode("\n", $config['pending_status_values']))
                );
                $rejectedStatusValues = array_filter(
                    array_map('trim', explode("\n", $config['rejected_status_values']))
                );

                // Parse instant method values
                $instantMethodValues = array_filter(
                    array_map('trim', explode("\n", $config['instant_method_values']))
                );

                // Parse method code mapping if provided
                $methodCodeMapping = [];
                if (!empty($config['method_code_mapping'])) {
                    $mappingLines = array_filter(
                        array_map('trim', explode("\n", $config['method_code_mapping']))
                    );
                    foreach ($mappingLines as $line) {
                        if (strpos($line, '=') !== false) {
                            [$code, $type] = explode('=', $line, 2);
                            $methodCodeMapping[trim($code)] = trim($type);
                        }
                    }
                }

                Log::info("Processing site ID {$site->id} - Method type: {$config['method_type']}, Success statuses: " . implode(', ', $successStatusValues) . ", Instant values: " . implode(', ', $instantMethodValues));

                // Determine the date filter based on whether this is the first import or not
                $dateColumn = $config['date_column'] ?? 'created_at';
                
                if ($site->last_import_at === null) {
                    // First time import - fetch all historical data within the lookback period
                    $startDate = now()->subDays($config['import_lookback_days'] ?? 7);
                    Log::info("First time import for site ID {$site->id} - fetching all data from last " . ($config['import_lookback_days'] ?? 7) . " days");
                } else {
                    // Subsequent imports - only fetch new transactions since last import
                    $startDate = $site->last_import_at;
                    Log::info("Subsequent import for site ID {$site->id} - fetching new transactions since " . $startDate);
                }
                
                // Fetch all instant payments (method = 'instant')
                $query = $externalDB->table($transactionTable)
                    ->join($userTable, $transactionTable . '.' . $config['user_id_column'], '=', $userTable . '.' . $config['user_id_column_in_user_table'])
                    ->whereIn($transactionTable . '.method', $instantMethodValues)
                    ->where($transactionTable . '.' . $dateColumn, '>=', $startDate);

                $query->select(
                    $transactionTable . '.' . $config['json_column'] . '->reference as reference_number',
                    $transactionTable . '.' . ($config['amount_column'] ?? 'amount') . ' as amount',
                    $transactionTable . '.' . $config['json_column'] . '->virtual_account as account_number',
                    $transactionTable . '.' . $config['json_column'] . '->virtual_account as virtual_account_number',
                    $transactionTable . '.method as payment_method',
                    $transactionTable . '.' . $config['status_column'] . ' as ext_status',
                    $transactionTable . '.' . $dateColumn . ' as original_created_at',
                    $userTable . '.' . $config['user_name_column'] . ' as user_name',
                    $userTable . '.' . $config['user_email_column'] . ' as user_email',
                    $transactionTable . '.' . $config['json_column'] . ' as json_data'
                );

                $externalTransactions = $query->get();

                $importedCount = 0;
                $creditedCount = 0;
                $statusCounts = ['success' => 0, 'pending' => 0, 'failed' => 0];

                foreach ($externalTransactions as $extTx) {
                    // Parse the reference from the JSON details column using the configured key
                    $referenceKey = $config['reference_key'] ?? 'reference';
                    $details = json_decode($extTx->json_data, true);
                    $reference = $details[$referenceKey] ?? null;
                    if (!$reference) {
                        Log::warning("No reference found for transaction (external ID: {$extTx->reference_number}) using key '{$referenceKey}'");
                        continue;
                    }

                    // Skip duplicates based on reference + site_id
                    $exists = Transaction::where('reference', $reference)
                        ->where('site_id', $site->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    // Determine local status
                    $externalStatus = (string) $extTx->ext_status;
                    if (in_array($externalStatus, $successStatusValues)) {
                        $localStatus = 'success';
                    } elseif (in_array($externalStatus, $pendingStatusValues)) {
                        $localStatus = 'pending';
                    } elseif (in_array($externalStatus, $rejectedStatusValues)) {
                        $localStatus = 'failed';
                    } else {
                        $localStatus = 'failed';
                    }

                    // Only credit if status is success
                    $credit = $localStatus === 'success';

                    // Insert into local transactions table
                    Transaction::create([
                        'site_id' => $site->id,
                        'business_profile_id' => $site->business_profile_id,
                        'reference' => $reference,
                        'external_id' => $extTx->reference_number,
                        'amount' => $extTx->amount,
                        'currency' => 'NGN', // Assuming NGN, could be extended
                        'status' => $localStatus,
                        'payment_method' => $extTx->payment_method,
                        'customer_email' => $extTx->user_email,
                        'customer_name' => $extTx->user_name,
                        'metadata' => $extTx->json_data, // Store the full JSON data
                        'description' => 'Imported from external DB - Instant Payment (External Status: ' . $externalStatus . ')',
                        'created_at' => $extTx->original_created_at, // Use original transaction time
                        'updated_at' => $extTx->original_created_at, // Use original transaction time
                    ]);

                    $importedCount++;
                    $statusCounts[$localStatus]++;

                    // Credit business profile balance for successful transactions
                    if ($credit) {
                        $creditedCount++;
                        $businessProfile = $site->businessProfile;
                        if ($businessProfile) {
                            $businessProfile->increment('balance', $extTx->amount);
                            Log::info("Credited business profile ID {$businessProfile->id} with amount: {$extTx->amount} for transaction: {$reference}");
                        } else {
                            Log::warning("No business profile found for site ID {$site->id}, cannot credit balance for transaction: {$reference}");
                        }
                    }
                }

                Log::info("Successfully imported {$importedCount} instant payment transactions for site ID {$site->id}");
                Log::info("Status breakdown: " . json_encode($statusCounts));
                Log::info("Credited {$creditedCount} transactions to site balance");

                // Update the last import time for this site
                $site->update(['last_import_at' => now()]);

            } catch (\Exception $e) {
                Log::error("Failed to import transactions for site ID {$site->id}: " . $e->getMessage());
                continue;
            }
        }
    }
}
