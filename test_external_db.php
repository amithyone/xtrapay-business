<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

try {
    // Get the site configuration
    $site = Site::find(1);
    if (!$site || !$site->field_mapping) {
        echo "No site configuration found\n";
        exit;
    }

    $config = $site->field_mapping;
    echo "Site: {$site->name}\n";
    echo "Database: {$config['db_name']}\n";
    echo "Transaction Table: {$config['transaction_table']}\n";
    echo "Amount Column: " . ($config['amount_column'] ?? 'amount') . "\n\n";

    // Build external DB connection config
    $connectionName = 'external_test_' . time();
    
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

    $externalDB = DB::connection($connectionName);
    
    // Test connection
    echo "Testing connection...\n";
    $externalDB->select('SELECT 1');
    echo "✓ Connection successful\n\n";

    $transactionTable = $config['transaction_table'];
    
    // Check total number of transactions
    $totalTransactions = $externalDB->table($transactionTable)->count();
    echo "Total transactions in table: {$totalTransactions}\n\n";

    // Check unique method values
    echo "Unique method values:\n";
    $methods = $externalDB->table($transactionTable)
        ->select('method')
        ->distinct()
        ->get();
    
    foreach ($methods as $method) {
        $count = $externalDB->table($transactionTable)
            ->where('method', $method->method)
            ->count();
        echo "- '{$method->method}': {$count} transactions\n";
    }
    echo "\n";

    // Check recent transactions (last 10)
    echo "Recent transactions (last 10):\n";
    $recentTransactions = $externalDB->table($transactionTable)
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get(['id', 'method', 'status', 'created_at', 'amount']);
    
    foreach ($recentTransactions as $tx) {
        echo "ID: {$tx->id}, Method: '{$tx->method}', Status: {$tx->status}, Amount: {$tx->amount}, Date: {$tx->created_at}\n";
    }
    echo "\n";

    // Check for transactions with method = 'instant'
    $instantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->count();
    echo "Transactions with method = 'instant': {$instantCount}\n";

    // Check for successful instant transactions (status = 1)
    $successfulInstantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->count();
    echo "Successful instant transactions (status = 1): {$successfulInstantCount}\n";

    // Check for pending instant transactions (status = 0)
    $pendingInstantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 0)
        ->count();
    echo "Pending instant transactions (status = 0): {$pendingInstantCount}\n";

    // Check date range - last 7 days
    $sevenDaysAgo = now()->subDays(7)->format('Y-m-d H:i:s');
    echo "Looking for transactions since: {$sevenDaysAgo}\n";
    
    $recentInstantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('created_at', '>=', $sevenDaysAgo)
        ->count();
    echo "Instant transactions in last 7 days: {$recentInstantCount}\n";

    // Check successful instant transactions in last 7 days
    $recentSuccessfulInstantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->where('created_at', '>=', $sevenDaysAgo)
        ->count();
    echo "Successful instant transactions in last 7 days: {$recentSuccessfulInstantCount}\n";

    // Check pending instant transactions in last 7 days
    $recentPendingInstantCount = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 0)
        ->where('created_at', '>=', $sevenDaysAgo)
        ->count();
    echo "Pending instant transactions in last 7 days: {$recentPendingInstantCount}\n\n";

    // Test with 30-day lookback
    $thirtyDaysAgo = now()->subDays(30)->format('Y-m-d H:i:s');
    echo "=== TESTING WITH 30-DAY LOOKBACK ===\n";
    echo "Looking for transactions since: {$thirtyDaysAgo}\n";
    
    $recentInstantCount30 = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->count();
    echo "Instant transactions in last 30 days: {$recentInstantCount30}\n";

    $recentSuccessfulInstantCount30 = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->count();
    echo "Successful instant transactions in last 30 days: {$recentSuccessfulInstantCount30}\n\n";

    // Show the EXACT query the import job runs
    echo "=== IMPORT JOB QUERY SIMULATION (7 days) ===\n";
    echo "Query: SELECT * FROM {$transactionTable} WHERE method = 'instant' AND status = 1 AND created_at >= '{$sevenDaysAgo}'\n";
    
    $importQuery = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->where('created_at', '>=', $sevenDaysAgo)
        ->get(['id', 'method', 'status', 'created_at', 'amount']);
    
    echo "Results found: " . count($importQuery) . "\n";
    foreach ($importQuery as $tx) {
        echo "ID: {$tx->id}, Method: '{$tx->method}', Status: {$tx->status}, Amount: {$tx->amount}, Date: {$tx->created_at}\n";
    }
    echo "\n";

    // Show what would be imported with 30-day lookback
    echo "=== IMPORT JOB QUERY SIMULATION (30 days) ===\n";
    echo "Query: SELECT * FROM {$transactionTable} WHERE method = 'instant' AND status = 1 AND created_at >= '{$thirtyDaysAgo}'\n";
    
    $importQuery30 = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->get(['id', 'method', 'status', 'created_at', 'amount']);
    
    echo "Results found: " . count($importQuery30) . "\n";
    $totalAmount = 0;
    foreach ($importQuery30 as $tx) {
        echo "ID: {$tx->id}, Method: '{$tx->method}', Status: {$tx->status}, Amount: {$tx->amount}, Date: {$tx->created_at}\n";
        $totalAmount += $tx->amount;
    }
    echo "Total amount to be credited: ₦" . number_format($totalAmount, 2) . "\n\n";

    // Check JSON data structure
    echo "=== JSON DATA STRUCTURE CHECK ===\n";
    $sampleTransaction = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('status', 1)
        ->first();
    
    if ($sampleTransaction) {
        $jsonData = json_decode($sampleTransaction->detail, true);
        echo "Sample JSON data for transaction ID {$sampleTransaction->id}:\n";
        echo json_encode($jsonData, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "Available JSON keys:\n";
        if ($jsonData) {
            foreach (array_keys($jsonData) as $key) {
                echo "- {$key}: " . ($jsonData[$key] ?? 'null') . "\n";
            }
        }
        echo "\n";
    }

    // Show ALL instant transactions in 30 days (all statuses)
    echo "=== ALL INSTANT TRANSACTIONS (30 days, all statuses) ===\n";
    $allInstant30 = $externalDB->table($transactionTable)
        ->where('method', 'instant')
        ->where('created_at', '>=', $thirtyDaysAgo)
        ->orderBy('created_at', 'desc')
        ->get(['id', 'method', 'status', 'created_at', 'amount']);
    
    $statusBreakdown = ['1' => 0, '0' => 0, '2' => 0];
    $amountsByStatus = ['1' => 0, '0' => 0, '2' => 0];
    
    foreach ($allInstant30 as $tx) {
        $statusText = $tx->status == 1 ? 'SUCCESS' : ($tx->status == 0 ? 'PENDING' : 'REJECTED');
        echo "ID: {$tx->id}, Method: '{$tx->method}', Status: {$tx->status} ({$statusText}), Amount: {$tx->amount}, Date: {$tx->created_at}\n";
        $statusBreakdown[$tx->status]++;
        $amountsByStatus[$tx->status] += $tx->amount;
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Total instant transactions (30 days): " . count($allInstant30) . "\n";
    echo "Success (status 1): {$statusBreakdown['1']} transactions, ₦" . number_format($amountsByStatus['1'], 2) . "\n";
    echo "Pending (status 0): {$statusBreakdown['0']} transactions, ₦" . number_format($amountsByStatus['0'], 2) . "\n";
    echo "Rejected (status 2): {$statusBreakdown['2']} transactions, ₦" . number_format($amountsByStatus['2'], 2) . "\n";
    echo "Total amount to be credited (success only): ₦" . number_format($amountsByStatus['1'], 2) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 