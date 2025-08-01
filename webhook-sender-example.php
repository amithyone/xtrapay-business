<?php
// webhook-sender-example.php
// Example script to send transaction notification to Xtrabusiness from faddedsmm.com

// Xtrabusiness webhook endpoint URL
$xtrabusinessWebhookUrl = 'https://your-xtrabusiness-domain.com/api/webhook/receive-transaction'; // Change to your actual URL

// Example data to send (replace with real data from your deposit notification)
$data = [
    'site_api_code'   => 'YOUR_SITE_API_CODE', // The API code for the site as registered in Xtrabusiness
    'reference'       => 'TRX-123456789',      // Unique transaction reference
    'amount'          => 5000.00,              // Transaction amount
    'currency'        => 'NGN',                // Currency code
    'status'          => 'success',            // Transaction status: pending, success, failed, abandoned
    'payment_method'  => 'card',               // Payment method (optional)
    'customer_email'  => 'customer@example.com', // Customer email (optional)
    'customer_name'   => 'John Doe',           // Customer name (optional)
    'description'     => 'Deposit via Xtrapay',// Description (optional)
    'external_id'     => 'EXT-987654321',      // External transaction ID (optional)
    'metadata'        => [                     // Any extra data (optional)
        'gateway' => 'xtrapay',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
    ],
    'timestamp'       => date('c'),            // ISO8601 timestamp (optional)
];

// Initialize cURL
$ch = curl_init($xtrabusinessWebhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-API-Key: YOUR_SITE_API_KEY', // Add your site's API key here
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Output the response
header('Content-Type: application/json');
echo json_encode([
    'sent_data' => $data,
    'response' => json_decode($response, true),
    'http_code' => $httpCode,
    'error' => $error,
]); 