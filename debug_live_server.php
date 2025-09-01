<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Bootstrap Laravel
$app = Application::configure(basePath: __DIR__)
    ->withRouting(
        web: __DIR__.'/routes/web.php',
        commands: __DIR__.'/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Live Server Debug ===\n\n";

// Test with user 2
$user = \App\Models\User::find(2);
if (!$user) {
    echo "❌ User 2 not found\n";
    exit;
}

echo "User 2 Details:\n";
echo "- ID: {$user->id}\n";
echo "- Email: {$user->email}\n";
echo "- is_admin: " . ($user->is_admin ? 'true' : 'false') . "\n";

// Test isSuperAdmin method
try {
    $isSuperAdmin = $user->isSuperAdmin();
    echo "- isSuperAdmin(): " . ($isSuperAdmin ? 'true' : 'false') . "\n";
} catch (\Exception $e) {
    echo "- isSuperAdmin() ERROR: " . $e->getMessage() . "\n";
}

// Test business profile
try {
    $hasBusinessProfile = $user->businessProfile ? true : false;
    echo "- Has business profile: " . ($hasBusinessProfile ? 'true' : 'false') . "\n";
    if ($user->businessProfile) {
        echo "- Business Profile ID: {$user->businessProfile->id}\n";
    }
} catch (\Exception $e) {
    echo "- Business profile ERROR: " . $e->getMessage() . "\n";
}

// Test sites
try {
    $sites = \App\Models\Site::where('business_profile_id', 1)->get();
    echo "\nSites for Business Profile 1:\n";
    foreach ($sites as $site) {
        echo "- Site ID: {$site->id}, Name: {$site->name}, Business ID: {$site->business_profile_id}\n";
    }
} catch (\Exception $e) {
    echo "Sites ERROR: " . $e->getMessage() . "\n";
}

// Test authorization logic
if ($sites->count() > 0) {
    $site = $sites->first();
    echo "\nTesting authorization for Site ID: {$site->id}:\n";
    
    $canAccess = false;
    $reason = "";
    
    if ($isSuperAdmin) {
        $canAccess = true;
        $reason = "User is super admin";
    } elseif ($hasBusinessProfile && $site->business_profile_id === $user->businessProfile->id) {
        $canAccess = true;
        $reason = "User owns this site";
    } else {
        $canAccess = false;
        $reason = "User does not own this site";
    }
    
    echo "- Can access: " . ($canAccess ? 'true' : 'false') . "\n";
    echo "- Reason: {$reason}\n";
}

// Test database connection
echo "\nDatabase Tables:\n";
try {
    $tables = \DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- {$tableName}\n";
    }
} catch (\Exception $e) {
    echo "Database ERROR: " . $e->getMessage() . "\n";
}
