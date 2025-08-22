<?php
echo "PHP Path: " . PHP_BINARY . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . __DIR__ . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Path: " . $_SERVER['SCRIPT_FILENAME'] . "\n";

// Check if we can access Laravel
if (file_exists(__DIR__ . '/artisan')) {
    echo "Laravel artisan found: " . __DIR__ . '/artisan' . "\n";
} else {
    echo "Laravel artisan NOT found in current directory\n";
}

// Check common PHP locations
$commonPaths = [
    '/usr/bin/php',
    '/usr/local/bin/php',
    '/opt/alt/php81/usr/bin/php',
    '/opt/alt/php82/usr/bin/php',
    '/opt/alt/php83/usr/bin/php',
    '/opt/alt/php74/usr/bin/php',
    '/opt/alt/php80/usr/bin/php'
];

echo "\nChecking common PHP paths:\n";
foreach ($commonPaths as $path) {
    if (file_exists($path)) {
        echo "✅ Found: $path\n";
    } else {
        echo "❌ Not found: $path\n";
    }
}
?>
