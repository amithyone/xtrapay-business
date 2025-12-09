<?php
/**
 * Test script for PayVibe API endpoint
 * This simulates how businesses will call the API to request virtual accounts
 * 
 * Usage: php test_payvibe_api.php
 */

// Configuration - Replace with your actual site credentials
$apiUrl = 'http://localhost:8000/api/v1/payvibe/request-account'; // Update with your domain
$apiKey = 'your_site_api_key_here'; // Get from your site settings
$siteApiCode = 'your_site_api_code_here'; // Get from your site settings

echo "🧪 Testing PayVibe API Endpoint\n";
echo "================================\n\n";

// Test data
$testData = [
    'site_api_code' => $siteApiCode,
    'amount' => 5000.00, // ₦5,000.00
    'description' => 'Test payment for virtual account',
    'customer_email' => 'test@example.com',
    'customer_name' => 'Test Customer',
    'metadata' => [
        'order_id' => 'ORDER_12345',
        'user_id' => 123
    ]
];

echo "📝 Request Details:\n";
echo "   URL: {$apiUrl}\n";
echo "   Method: POST\n";
echo "   Amount: ₦" . number_format($testData['amount'], 2) . "\n";
echo "   Site API Code: {$siteApiCode}\n\n";

// Initialize cURL
$ch = curl_init($apiUrl);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-API-Key: ' . $apiKey
    ],
    CURLOPT_SSL_VERIFYPEER => false, // Set to true in production
]);

echo "🔄 Sending request to API...\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($error) {
    echo "❌ cURL Error: {$error}\n";
    exit(1);
}

echo "📊 Response:\n";
echo "   HTTP Code: {$httpCode}\n\n";

$responseData = json_decode($response, true);

if ($httpCode === 201 && isset($responseData['success']) && $responseData['success']) {
    echo "✅ SUCCESS! Virtual Account Generated\n\n";
    echo "📋 Account Details:\n";
    echo "   Transaction ID: " . ($responseData['data']['transaction_id'] ?? 'N/A') . "\n";
    echo "   Reference: " . ($responseData['data']['reference'] ?? 'N/A') . "\n";
    echo "   Account Number: " . ($responseData['data']['account_number'] ?? 'N/A') . "\n";
    echo "   Bank Name: " . ($responseData['data']['bank_name'] ?? 'N/A') . "\n";
    echo "   Account Name: " . ($responseData['data']['account_name'] ?? 'N/A') . "\n";
    echo "   Amount: ₦" . number_format($responseData['data']['amount'] ?? 0, 2) . "\n";
    echo "   Status: " . ($responseData['data']['status'] ?? 'N/A') . "\n";
    echo "   Expires At: " . ($responseData['data']['expires_at'] ?? 'N/A') . "\n";
    echo "\n   Full Response:\n";
    echo "   " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ FAILED\n\n";
    echo "   Message: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    if (isset($responseData['errors'])) {
        echo "   Errors:\n";
        foreach ($responseData['errors'] as $field => $messages) {
            echo "     - {$field}: " . implode(', ', $messages) . "\n";
        }
    }
    echo "\n   Full Response:\n";
    echo "   " . json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
}

echo "\n✅ Test completed!\n";

