<?php
/**
 * Simulate PayVibe webhook notification
 * This shows what payload will be sent to business webhooks
 */

echo "🧪 PayVibe Webhook Simulation\n";
echo "==============================\n\n";

// Simulate a successful payment webhook payload
$webhookPayload = [
    'event' => 'payment.received',
    'transaction' => [
        'id' => 123,
        'reference' => 'PAYVIBE_0001_0001_20241209120000_ABC123',
        'external_id' => 'PAYVIBE_0001_0001_20241209120000_ABC123',
        'amount' => 5000.00,
        'currency' => 'NGN',
        'status' => 'success',
        'payment_method' => 'payvibe',
        'customer_email' => 'customer@example.com',
        'customer_name' => 'John Doe',
        'description' => 'Payment for order #12345',
        'created_at' => '2024-12-09T12:00:00+00:00',
        'updated_at' => '2024-12-09T12:15:30+00:00',
    ],
    'site' => [
        'id' => 1,
        'name' => 'Your Business Site',
        'api_code' => 'your_site_api_code',
    ],
    'metadata' => [
        'order_id' => 'ORDER_12345',
        'user_id' => 123,
        'payvibe_reference' => 'PAYVIBE_0001_0001_20241209120000_ABC123',
        'account_number' => '8041009815',
        'bank_name' => 'Wema Bank',
        'account_name' => 'Finspa/PAYVIBE',
        'initiated_at' => '2024-12-09T12:00:00+00:00',
        'requested_via_api' => true,
    ],
    'timestamp' => '2024-12-09T12:15:30+00:00',
];

echo "📋 Webhook Payload Example:\n";
echo "============================\n\n";
echo json_encode($webhookPayload, JSON_PRETTY_PRINT);
echo "\n\n";

echo "📝 This is what will be sent to your webhook URL when payment is received.\n";
echo "   Your webhook endpoint should:\n";
echo "   1. Accept POST requests\n";
echo "   2. Verify event type: 'payment.received'\n";
echo "   3. Check transaction status: 'success'\n";
echo "   4. Process the payment (update order, send email, etc.)\n";
echo "   5. Return 200 status code\n\n";

echo "💡 Example webhook handler:\n";
echo "============================\n\n";
echo <<<'PHP'
<?php
// webhooks/payvibe.php
$payload = json_decode(file_get_contents('php://input'), true);

if ($payload['event'] === 'payment.received' && 
    $payload['transaction']['status'] === 'success') {
    
    $orderId = $payload['metadata']['order_id'];
    $amount = $payload['transaction']['amount'];
    
    // Update order status
    updateOrderStatus($orderId, 'paid');
    
    // Send confirmation email
    sendEmail($payload['transaction']['customer_email'], $orderId);
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
PHP;

echo "\n\n✅ Simulation completed!\n";

