<?php
/**
 * XtraPay Business - Complete Setup (Public Folder Version)
 * 
 * This script runs both migrations and seeders in one go
 * for complete application setup on shared hosting.
 * 
 * IMPORTANT: Delete this file after successful setup for security!
 */

// Prevent direct access if not in web context
if (php_sapi_name() === 'cli') {
    echo "This script is designed to run via web browser on shared hosting.\n";
    exit(1);
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>XtraPay Business - Complete Setup</h1>";
echo "<p>Setting up your application with migrations and sample data...</p>";

// Since we're in public folder, we need to go up one level to find Laravel root
$laravelRoot = dirname(__DIR__);

// Check if Laravel is properly set up
if (!file_exists($laravelRoot . '/artisan')) {
    echo "<p style='color: red;'>Error: artisan file not found. Make sure this script is in the public/ folder of your Laravel project.</p>";
    exit;
}

if (!file_exists($laravelRoot . '/.env')) {
    echo "<p style='color: red;'>Error: .env file not found. Please create your .env file with production settings first.</p>";
    exit;
}

// Change to Laravel root directory
chdir($laravelRoot);

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

echo "<p>Optimizing application...</p>";
$kernel->call('config:cache');
echo "<p style='color: green;'>✓ Application optimized</p>";

echo "<h2>Step 2: Running Database Migrations</h2>";
echo "<p>Creating database tables...</p>";

try {
    $kernel->call('migrate', ['--force' => true]);
    echo "<p style='color: green;'>✓ Migrations completed successfully!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Migration failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in .env file.</p>";
    exit;
}

echo "<h2>Step 3: Running Database Seeders</h2>";
echo "<p>Populating database with initial data...</p>";

try {
    $kernel->call('db:seed', ['--force' => true]);
    echo "<p style='color: green;'>✓ Database seeded successfully!</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Seeding failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 4: Migration Status</h2>";
echo "<p>Checking migration status...</p>";

try {
    $output = $kernel->call('migrate:status');
    echo "<pre>" . $output . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Could not check migration status: " . $e->getMessage() . "</p>";
}

echo "<h2>Step 5: Setup Summary</h2>";
echo "<p>Your application has been successfully set up with:</p>";
echo "<ul>";
echo "<li><strong>Database Tables:</strong> All 25 migrations completed</li>";
echo "<li><strong>Admin User:</strong> admin@xtrapay.com (Password: admin123)</li>";
echo "<li><strong>Test User:</strong> test@example.com (Password: password)</li>";
echo "<li><strong>Admin Codes:</strong> 24685 (first code) and 2468 (second code)</li>";
echo "<li><strong>Business Profile:</strong> Test Business with ₦1,000,000 balance</li>";
echo "<li><strong>Sites:</strong> 2 test sites (Main Branch and Branch 2)</li>";
echo "<li><strong>Transactions:</strong> 10 test transactions across both sites</li>";
echo "</ul>";

echo "<h2>Step 6: Login Credentials</h2>";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Admin Access:</h3>";
echo "<p><strong>Email:</strong> admin@xtrapay.com</p>";
echo "<p><strong>Password:</strong> admin123</p>";
echo "<p><strong>Admin Codes:</strong> 24685, 2468</p>";
echo "</div>";

echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>Regular User Access:</h3>";
echo "<p><strong>Email:</strong> test@example.com</p>";
echo "<p><strong>Password:</strong> password</p>";
echo "</div>";

echo "<h2>Step 7: Security</h2>";
echo "<p style='color: orange;'>⚠️ IMPORTANT: Please delete this file (run-migrations-and-seeders-public.php) after successful setup for security!</p>";

echo "<h2>Step 8: Next Steps</h2>";
echo "<p>Your application is now fully set up and ready to use. Please:</p>";
echo "<ul>";
echo "<li>Test the login functionality with the provided credentials</li>";
echo "<li>Delete this setup script file</li>";
echo "<li>Change the default passwords for security</li>";
echo "<li>Update the business information with your actual data</li>";
echo "<li>Set APP_DEBUG=false in your .env file</li>";
echo "<li>Configure your email settings in .env file</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 Application setup completed successfully!</p>";
echo "<p>You can now start using your XtraPay Business application.</p>";
?> 