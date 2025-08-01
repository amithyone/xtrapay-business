<?php
/**
 * XtraPay Business - Migration Runner
 * 
 * This script can be run via web browser on shared hosting
 * to execute Laravel migrations when SSH access is not available.
 * 
 * IMPORTANT: Delete this file after running migrations for security!
 */

// Prevent direct access if not in web context
if (php_sapi_name() === 'cli') {
    echo "This script is designed to run via web browser on shared hosting.\n";
    exit(1);
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>XtraPay Business - Migration Runner</h1>";
echo "<p>Running migrations for your application...</p>";

// Check if Laravel is properly set up
if (!file_exists('artisan')) {
    echo "<p style='color: red;'>Error: artisan file not found. Make sure you're running this from your Laravel project root.</p>";
    exit;
}

if (!file_exists('.env')) {
    echo "<p style='color: red;'>Error: .env file not found. Please create your .env file with production settings first.</p>";
    exit;
}

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h2>Step 1: Clearing Caches</h2>";
echo "<p>Clearing application cache...</p>";
$kernel->call('cache:clear');
echo "<p style='color: green;'>✓ Application cache cleared</p>";

echo "<p>Clearing config cache...</p>";
$kernel->call('config:clear');
echo "<p style='color: green;'>✓ Config cache cleared</p>";

echo "<p>Clearing route cache...</p>";
$kernel->call('route:clear');
echo "<p style='color: green;'>✓ Route cache cleared</p>";

echo "<p>Clearing view cache...</p>";
$kernel->call('view:clear');
echo "<p style='color: green;'>✓ View cache cleared</p>";

echo "<h2>Step 2: Running Migrations</h2>";
echo "<p>Executing database migrations...</p>";

try {
    $kernel->call('migrate', ['--force' => true]);
    echo "<p style='color: green;'>✓ Migrations completed successfully!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in .env file.</p>";
    exit;
}

echo "<h2>Step 3: Migration Status</h2>";
echo "<p>Checking migration status...</p>";

try {
    $output = $kernel->call('migrate:status');
    echo "<pre>" . $output . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Could not check migration status: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 4: Security</h2>";
echo "<p style='color: orange;'>⚠️ IMPORTANT: Please delete this file (run-migrations.php) after successful migration for security!</p>";

echo "<h2>Step 5: Next Steps</h2>";
echo "<p>Your application should now be ready. Please:</p>";
echo "<ul>";
echo "<li>Test your website functionality</li>";
echo "<li>Delete this migration script file</li>";
echo "<li>Set APP_DEBUG=false in your .env file</li>";
echo "<li>Configure your web server to point to the public/ directory</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold;'>Migration process completed!</p>";
?> 