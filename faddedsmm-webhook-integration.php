<?php
/**
 * faddedsmm.com - Xtrabusiness Webhook Integration
 * 
 * This file contains functions to send transaction notifications to Xtrabusiness
 * Use these functions when:
 * 1. User creates a transaction (status: pending)
 * 2. Xtrapay sends notification (status: success/failed)
 */

class XtrabusinessWebhook {
    
    // Configuration - Update these with your actual values
    private $webhookUrl = 'https://your-xtrabusiness-domain.com/api/webhook/receive-transaction';
    private $siteApiCode = 'FADDEDSMM_001'; // Your site's API code from Xtrabusiness
    private $apiKey = 'your-secret-api-key-here'; // Your site's API key from Xtrabusiness
    
    /**
     * Send transaction to Xtrabusiness
     * 
     * @param array $transactionData Transaction data
     * @return array Response from Xtrabusiness
     */
    public function sendTransaction($transactionData) {
        // Prepare the data for Xtrabusiness
        $data = [
            'site_api_code'   => $this->siteApiCode,
            'reference'       => $transactionData['reference'],
            'amount'          => $transactionData['amount'],
            'currency'        => $transactionData['currency'] ?? 'NGN',
            'status'          => $transactionData['status'], // pending, success, failed
            'payment_method'  => $transactionData['payment_method'] ?? 'xtrapay',
            'customer_email'  => $transactionData['customer_email'] ?? null,
            'customer_name'   => $transactionData['customer_name'] ?? null,
            'description'     => $transactionData['description'] ?? 'Transaction via Xtrapay',
            'external_id'     => $transactionData['external_id'] ?? null,
            'metadata'        => array_merge($transactionData['metadata'] ?? [], [
                'gateway' => 'xtrapay',
                'site' => 'faddedsmm.com',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            ]),
            'timestamp'       => date('c'),
        ];
        
        // Send to Xtrabusiness
        return $this->makeWebhookRequest($data);
    }
    
    /**
     * Send pending transaction (when user initiates payment)
     * 
     * @param string $reference Transaction reference
     * @param float $amount Transaction amount
     * @param string $customerEmail Customer email
     * @param string $customerName Customer name
     * @param string $description Transaction description
     * @return array Response from Xtrabusiness
     */
    public function sendPendingTransaction($reference, $amount, $customerEmail = null, $customerName = null, $description = null) {
        $transactionData = [
            'reference' => $reference,
            'amount' => $amount,
            'status' => 'pending',
            'customer_email' => $customerEmail,
            'customer_name' => $customerName,
            'description' => $description,
            'metadata' => [
                'event' => 'transaction_created',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];
        
        return $this->sendTransaction($transactionData);
    }
    
    /**
     * Update transaction status (when Xtrapay sends notification)
     * 
     * @param string $reference Transaction reference
     * @param string $status New status (success, failed, abandoned)
     * @param array $xtrapayData Original data from Xtrapay
     * @return array Response from Xtrabusiness
     */
    public function updateTransactionStatus($reference, $status, $xtrapayData = []) {
        $transactionData = [
            'reference' => $reference,
            'amount' => $xtrapayData['amount'] ?? 0,
            'status' => $status,
            'customer_email' => $xtrapayData['customer']['email'] ?? null,
            'customer_name' => $xtrapayData['customer']['name'] ?? null,
            'external_id' => $xtrapayData['id'] ?? null,
            'metadata' => array_merge($xtrapayData, [
                'event' => 'xtrapay_notification',
                'notification_received_at' => date('Y-m-d H:i:s'),
                'xtrapay_reference' => $xtrapayData['reference'] ?? null,
            ])
        ];
        
        return $this->sendTransaction($transactionData);
    }
    
    /**
     * Make HTTP request to Xtrabusiness
     * 
     * @param array $data Data to send
     * @return array Response
     */
    private function makeWebhookRequest($data) {
        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $this->apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Log the request and response
        $this->logWebhookRequest($data, $response, $httpCode, $error);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => json_decode($response, true),
            'error' => $error,
            'sent_data' => $data
        ];
    }
    
    /**
     * Log webhook requests for debugging
     * 
     * @param array $sentData Data sent
     * @param string $response Response received
     * @param int $httpCode HTTP status code
     * @param string $error cURL error if any
     */
    private function logWebhookRequest($sentData, $response, $httpCode, $error) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'sent_data' => $sentData,
            'response' => $response,
            'http_code' => $httpCode,
            'error' => $error,
            'success' => $httpCode >= 200 && $httpCode < 300
        ];
        
        // Log to file (adjust path as needed)
        $logFile = __DIR__ . '/logs/xtrabusiness-webhook.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);
    }
}

// ============================================================================
// USAGE EXAMPLES
// ============================================================================

// Initialize the webhook handler
$webhook = new XtrabusinessWebhook();

// Example 1: When user creates a transaction (pending)
function handleTransactionCreated($reference, $amount, $customerEmail, $customerName) {
    global $webhook;
    
    // First, save to your local database
    // $transaction = saveTransactionToLocalDB($reference, $amount, 'pending', ...);
    
    // Then send to Xtrabusiness
    $result = $webhook->sendPendingTransaction(
        $reference,
        $amount,
        $customerEmail,
        $customerName,
        'Deposit via Xtrapay'
    );
    
    if ($result['success']) {
        echo "Transaction sent to Xtrabusiness successfully\n";
    } else {
        echo "Failed to send to Xtrabusiness: " . json_encode($result) . "\n";
    }
    
    return $result;
}

// Example 2: When Xtrapay sends notification
function handleXtrapayNotification($xtrapayData) {
    global $webhook;
    
    // Extract data from Xtrapay notification
    $reference = $xtrapayData['reference'] ?? $xtrapayData['data']['reference'] ?? null;
    $status = $xtrapayData['status'] ?? $xtrapayData['data']['status'] ?? 'failed';
    $amount = $xtrapayData['amount'] ?? $xtrapayData['data']['amount'] ?? 0;
    
    if (!$reference) {
        echo "Error: No reference found in Xtrapay data\n";
        return false;
    }
    
    // Update your local database
    // updateLocalTransaction($reference, $status, $xtrapayData);
    
    // Send update to Xtrabusiness
    $result = $webhook->updateTransactionStatus($reference, $status, $xtrapayData);
    
    if ($result['success']) {
        echo "Transaction status updated in Xtrabusiness successfully\n";
    } else {
        echo "Failed to update in Xtrabusiness: " . json_encode($result) . "\n";
    }
    
    return $result;
}

// Example 3: Xtrapay webhook endpoint (put this in your webhook handler)
function xtrapayWebhookEndpoint() {
    // Verify Xtrapay signature (important for security)
    if (!verifyXtrapaySignature()) {
        http_response_code(401);
        echo "Invalid signature";
        return;
    }
    
    // Get the notification data
    $input = file_get_contents('php://input');
    $xtrapayData = json_decode($input, true);
    
    // Process the notification
    $result = handleXtrapayNotification($xtrapayData);
    
    // Respond to Xtrapay
    if ($result) {
        http_response_code(200);
        echo "OK";
    } else {
        http_response_code(500);
        echo "Error processing notification";
    }
}

// Example 4: Test the integration
function testWebhookIntegration() {
    global $webhook;
    
    echo "Testing Xtrabusiness webhook integration...\n\n";
    
    // Test 1: Send pending transaction
    echo "1. Sending pending transaction...\n";
    $result1 = $webhook->sendPendingTransaction(
        'TEST-' . time(),
        5000.00,
        'test@example.com',
        'Test User',
        'Test transaction'
    );
    
    echo "Result: " . json_encode($result1, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test 2: Update to success
    echo "2. Updating transaction to success...\n";
    $result2 = $webhook->updateTransactionStatus(
        'TEST-' . time(),
        'success',
        [
            'id' => 'xtrapay_123',
            'amount' => 5000.00,
            'customer' => [
                'email' => 'test@example.com',
                'name' => 'Test User'
            ]
        ]
    );
    
    echo "Result: " . json_encode($result2, JSON_PRETTY_PRINT) . "\n\n";
}

// Uncomment to run test
// testWebhookIntegration();
?> 