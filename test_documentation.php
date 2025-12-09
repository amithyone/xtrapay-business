<?php
/**
 * Test Documentation Pages
 * Verifies all documentation routes are accessible
 */

echo "🧪 Testing Documentation Pages\n";
echo "==============================\n\n";

$baseUrl = 'http://localhost:8000'; // Update if your server runs on different port
$routes = [
    '/docs' => 'Documentation Index',
    '/docs/quick-start' => 'Quick Start Guide',
    '/docs/integration-guide' => 'Integration Guide',
    '/docs/api' => 'API Documentation',
    '/docs/fee-calculator' => 'Fee Calculator',
];

echo "📋 Testing Routes:\n";
echo "Base URL: {$baseUrl}\n\n";

$allPassed = true;

foreach ($routes as $route => $name) {
    $url = $baseUrl . $route;
    
    echo "Testing: {$name} ({$route})\n";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "  ⚠️  Error: {$error}\n";
        echo "  Note: Make sure Laravel server is running (php artisan serve)\n";
        $allPassed = false;
    } elseif ($httpCode === 200) {
        echo "  ✅ SUCCESS (HTTP {$httpCode})\n";
    } else {
        echo "  ❌ FAILED (HTTP {$httpCode})\n";
        $allPassed = false;
    }
    echo "\n";
}

if ($allPassed) {
    echo "✅ All documentation pages are accessible!\n";
    echo "\nYou can access them at:\n";
    foreach ($routes as $route => $name) {
        echo "  - {$baseUrl}{$route} ({$name})\n";
    }
} else {
    echo "⚠️  Some tests failed. Make sure:\n";
    echo "  1. Laravel server is running: php artisan serve\n";
    echo "  2. Routes are properly registered\n";
    echo "  3. Views exist in resources/views/documentation/\n";
}

echo "\n✅ Test completed!\n";

