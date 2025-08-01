<?php

require_once 'vendor/autoload.php';

use App\Models\Site;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing External Database JSON Structure\n";
echo "========================================\n\n";

try {
    // Get the first site with field mapping
    $site = Site::whereNotNull('field_mapping')->first();
    
    if (!$site) {
        echo "No sites with field mapping found.\n";
        exit(1);
    }
    
    echo "Testing site: {$site->name} (ID: {$site->id})\n";
    
    $config = $site->field_mapping;
    
    // Build external DB connection config
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
    
    $externalDB = DB::connection($connectionName);
    
    // Test the connection
    echo "Testing database connection...\n";
    $externalDB->getPdo();
    echo "✅ Database connection successful!\n\n";
    
    // Get sample data
    $transactionTable = $config['transaction_table'];
    $userTable = $config['user_table'];
    
    echo "Fetching sample data from {$transactionTable} table...\n";
    
    $sampleData = $externalDB->table($transactionTable)
        ->join($userTable, $transactionTable . '.' . $config['user_id_column'], '=', $userTable . '.' . $config['user_id_column_in_user_table'])
        ->whereIn($transactionTable . '.method', ['instant'])
        ->select(
            $transactionTable . '.' . $config['json_column'] . ' as json_data',
            $transactionTable . '.method as payment_method',
            $transactionTable . '.' . $config['status_column'] . ' as status',
            $userTable . '.' . $config['user_name_column'] . ' as user_name',
            $userTable . '.' . $config['user_email_column'] . ' as user_email'
        )
        ->limit(3)
        ->get();
    
    if ($sampleData->isEmpty()) {
        echo "❌ No data found in external database.\n";
        exit(1);
    }
    
    echo "Found " . $sampleData->count() . " sample records:\n\n";
    
    foreach ($sampleData as $index => $record) {
        echo "Record " . ($index + 1) . ":\n";
        echo "Payment Method: {$record->payment_method}\n";
        echo "Status: {$record->status}\n";
        echo "User: {$record->user_name} ({$record->user_email})\n";
        echo "JSON Data:\n";
        
        $jsonData = $record->json_data;
        echo "Raw JSON: " . $jsonData . "\n";
        
        $decoded = json_decode($jsonData, true);
        if ($decoded) {
            echo "Decoded JSON:\n";
            print_r($decoded);
            
            // Check for reference key
            $referenceKey = $config['reference_key'] ?? 'reference';
            if (isset($decoded[$referenceKey])) {
                echo "✅ Found reference key '{$referenceKey}': {$decoded[$referenceKey]}\n";
            } else {
                echo "❌ Reference key '{$referenceKey}' not found in JSON\n";
                echo "Available keys: " . implode(', ', array_keys($decoded)) . "\n";
            }
        } else {
            echo "❌ Failed to decode JSON: " . json_last_error_msg() . "\n";
        }
        
        echo "\n" . str_repeat('-', 50) . "\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 