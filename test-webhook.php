<?php
/**
 * Test script for Xtrabusiness webhook
 * Run this to test if your webhook endpoint is working
 */

// Configuration - Update these
$webhookUrl = 'http://localhost:8000/api/webhook/receive-transaction'; // Change to your actual URL
$siteApiCode = 'TEST_SITE_001'; // Use a test site API code
$apiKey = 'test-api-key-123'; // Use a test API key

// Test data
$testData = [
    'site_api_code' => $siteApiCode,
    'reference' => 'TEST-' . time(),
    'amount' => 1000.00,
    'currency' => 'NGN',
    'status' => 'pending',
    'payment_method' => 'card',
    'customer_email' => 'test@example.com',
    'customer_name' => 'Test Customer',
    'description' => 'Test transaction from webhook',
    'metadata' => [
        'test' => true,
        'timestamp' => date('Y-m-d H:i:s')
    ]
];

echo "Testing Xtrabusiness Webhook\n";
echo "============================\n";
echo "URL: $webhookUrl\n";
echo "Site API Code: $siteApiCode\n";
echo "Reference: {$testData['reference']}\n\n";

// Make the request
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-API-Key: ' . $apiKey,
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display results
echo "HTTP Status Code: $httpCode\n";
echo "Response:\n";
echo $response . "\n\n";

if ($error) {
    echo "cURL Error: $error\n";
}

$responseData = json_decode($response, true);
if ($responseData) {
    echo "Parsed Response:\n";
    print_r($responseData);
} else {
    echo "Could not parse JSON response\n";
}

echo "\nTest completed.\n";
?> 