<?php
// Test MAMP MySQL connection with different passwords
$host = 'localhost';
$port = 8889;
$username = 'root';
$database = 'payment';

$passwords = ['', 'root', 'admin', 'password'];

echo "Testing MAMP MySQL connection on port $port...\n\n";

foreach ($passwords as $password) {
    echo "Testing password: " . ($password ?: 'empty') . "... ";
    
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "SUCCESS! ✅\n";
        echo "Connected to database: $database\n";
        echo "Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
        
        // Test if we can query
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables in database:\n";
        foreach ($tables as $table) {
            echo "- $table\n";
        }
        
        break; // Stop after first successful connection
        
    } catch (PDOException $e) {
        echo "FAILED ❌\n";
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

if (!isset($pdo)) {
    echo "\nAll password attempts failed. You may need to reset MAMP MySQL.\n";
    echo "To reset: Stop MAMP, delete /Applications/MAMP/db/mysql80, restart MAMP\n";
}
?> 