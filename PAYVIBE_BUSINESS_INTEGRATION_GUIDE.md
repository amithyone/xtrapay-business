# XtraPay Virtual Account Integration Guide

## Overview

This guide explains how businesses can integrate XtraPay Virtual Accounts into their websites to collect payments from customers. XtraPay provides virtual account numbers that your customers can use to make bank transfers. The integration allows businesses to:

1. **Request virtual account numbers** via API when customers want to pay
2. **Receive real-time notifications** on their website when payments are received
3. **Track payment status** and manage transactions

## Table of Contents

1. [Getting Started](#getting-started)
2. [Requesting Virtual Account Numbers](#requesting-virtual-account-numbers)
3. [Setting Up Webhook Notifications](#setting-up-webhook-notifications)
4. [Webhook Payload Format](#webhook-payload-format)
5. [Handling Webhook Notifications](#handling-webhook-notifications)
6. [Checking Payment Status](#checking-payment-status)
7. [Complete Integration Example](#complete-integration-example)
8. [Security Best Practices](#security-best-practices)
9. [Troubleshooting](#troubleshooting)

---

## Getting Started

### Step 1: Get Your API Credentials

1. Log in to your XtraPay dashboard at **https://xtrapay.cash**
2. Go to **Sites** → Select your site (or create a new one)
3. Copy your **API Code** and **API Key**
4. Note your **Webhook URL** (you'll configure this in Step 3)

**Example Credentials:**
- **API Code:** `abc123xy`
- **API Key:** `abc123def456ghi789jkl012mno345pqr678stu901vwx234yz567890abcdefghijklmnop`
- **Webhook URL:** `https://yourwebsite.com/webhooks/xtrapay`

### Step 2: Configure Your Site

In your XtraPay dashboard:
1. Go to **Sites** → **Edit Site**
2. Set your **Webhook URL** (where you want to receive payment notifications)
   - Example: `https://yourwebsite.com/webhooks/xtrapay`
   - Must be HTTPS in production
   - Must be publicly accessible
3. (Optional) Add **Allowed IPs** for additional security
4. Save your changes

---

## 💰 Transaction Fees & Charges

### Fee Structure

XtraPay charges a small fee for each virtual account transaction:

- **1.5%** of the transaction amount
- **₦100** flat fee per transaction

### How Fees Work

When you request a virtual account for a customer payment:

1. **Customer pays:** The full amount they transfer
2. **Fees deducted:** 1.5% + ₦100 automatically deducted
3. **You receive:** Amount paid - (1.5% + ₦100)

### Fee Calculation Examples

**Example 1: Customer pays ₦5,000**
- Transaction amount: ₦5,000
- 1.5% fee: ₦75
- Flat fee: ₦100
- **Total fees: ₦175**
- **Amount credited to you: ₦4,825**

**Example 2: Customer pays ₦10,000**
- Transaction amount: ₦10,000
- 1.5% fee: ₦150
- Flat fee: ₦100
- **Total fees: ₦250**
- **Amount credited to you: ₦9,750**

**Example 3: Customer pays ₦50,000**
- Transaction amount: ₦50,000
- 1.5% fee: ₦750
- Flat fee: ₦100
- **Total fees: ₦850**
- **Amount credited to you: ₦49,150**

### Calculating Amount to Request

If you want to receive a specific amount after fees, use this formula:

```
Amount to Request = (Desired Amount + ₦100) ÷ 0.985
```

**Example:** To receive exactly ₦10,000:
```
Amount to Request = (₦10,000 + ₦100) ÷ 0.985
Amount to Request = ₦10,256.41
```

**Verification:**
- Customer pays: ₦10,256.41
- 1.5% fee: ₦153.85
- Flat fee: ₦100.00
- Total fees: ₦253.85
- You receive: ₦10,002.56 (rounded to ₦10,000)

### Important Reminders

⚠️ **Key Points:**
- Fees are **automatically deducted** - you don't need to pay separately
- The amount credited to your balance will be **less than** the customer paid
- **Account for fees** when displaying payment amounts to customers
- If you want to receive a specific amount, calculate the request amount accordingly
- Fees are calculated on the **amount the customer pays**, not the amount you receive

### Fee Information in Webhook

When payment is received, the webhook includes the transaction amount. The amount credited to your balance will be:

```
Credited Amount = Transaction Amount - (Transaction Amount × 0.015) - 100
```

---

## Requesting Virtual Account Numbers

When a customer wants to make a payment, your website should request a virtual account number from our API.

### API Endpoint

```
POST https://xtrapay.cash/api/v1/virtual-accounts/request
```

### Request Headers

```
Content-Type: application/json
Accept: application/json
X-API-Key: your_site_api_key_here
```

### Request Body

```json
{
    "site_api_code": "your_site_api_code",
    "amount": 5000.00,
    "description": "Payment for order #12345",
    "customer_email": "customer@example.com",
    "customer_name": "John Doe",
    "reference": "optional_custom_reference",
    "metadata": {
        "order_id": "ORDER_12345",
        "user_id": 123
    }
}
```

### Response (Success - 201)

```json
{
    "success": true,
    "message": "Virtual account generated successfully",
    "data": {
        "transaction_id": 123,
        "reference": "XTRAPAY_0001_0001_20241209120000_ABC123",
        "account_number": "8041009815",
        "bank_name": "Wema Bank",
        "account_name": "XtraPay Virtual Account",
        "amount": 5000.00,
        "currency": "NGN",
        "status": "pending",
        "expires_at": "2024-12-10T12:00:00+00:00",
        "created_at": "2024-12-09T12:00:00+00:00"
    }
}
```

### Example: PHP Implementation

```php
<?php
function requestVirtualAccount($amount, $customerEmail, $customerName, $orderId) {
    $apiUrl = 'https://xtrapay.cash/api/v1/virtual-accounts/request';
    $apiKey = 'your_site_api_key_here';
    $siteApiCode = 'your_site_api_code_here';
    
    $data = [
        'site_api_code' => $siteApiCode,
        'amount' => $amount,
        'description' => "Payment for order #{$orderId}",
        'customer_email' => $customerEmail,
        'customer_name' => $customerName,
        'metadata' => [
            'order_id' => $orderId
        ]
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $apiKey
        ],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 201) {
        $result = json_decode($response, true);
        if ($result['success']) {
            return $result['data'];
        }
    }
    
    return null;
}

// Usage
$accountDetails = requestVirtualAccount(
    5000.00,
    'customer@example.com',
    'John Doe',
    'ORDER_12345'
);

if ($accountDetails) {
    echo "Account Number: " . $accountDetails['account_number'] . "\n";
    echo "Bank: " . $accountDetails['bank_name'] . "\n";
    echo "Amount: ₦" . number_format($accountDetails['amount'], 2) . "\n";
    // Display to customer
}
?>
```

### Example: JavaScript Implementation

```javascript
async function requestVirtualAccount(amount, customerEmail, customerName, orderId) {
    const apiUrl = 'https://xtrapay.cash/api/v1/virtual-accounts/request';
    const apiKey = 'your_site_api_key_here';
    const siteApiCode = 'your_site_api_code_here';
    
    const data = {
        site_api_code: siteApiCode,
        amount: amount,
        description: `Payment for order #${orderId}`,
        customer_email: customerEmail,
        customer_name: customerName,
        metadata: {
            order_id: orderId
        }
    };
    
    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-API-Key': apiKey
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            return result.data;
        } else {
            console.error('Error:', result.message);
            return null;
        }
    } catch (error) {
        console.error('Request failed:', error);
        return null;
    }
}

// Usage
const accountDetails = await requestVirtualAccount(
    5000.00,
    'customer@example.com',
    'John Doe',
    'ORDER_12345'
);

if (accountDetails) {
    console.log('Account Number:', accountDetails.account_number);
    console.log('Bank:', accountDetails.bank_name);
    // Display to customer
}
```

---

## Setting Up Webhook Notifications

Webhooks allow you to receive real-time notifications when payments are received. This is essential for automatically updating orders, sending confirmation emails, etc.

### Step 1: Create Webhook Endpoint

Create a public endpoint on your website that can receive POST requests. This endpoint should:

- Accept POST requests
- Verify the request (optional but recommended)
- Process the payment notification
- Return a 200 status code

**Example Webhook URL:** `https://yourwebsite.com/webhooks/xtrapay`

### Step 2: Configure Webhook URL in Dashboard

1. Log in to XtraPay dashboard at **https://xtrapay.cash**
2. Go to **Sites** → **Edit Site**
3. Enter your **Webhook URL** in the "Webhook URL" field
4. Save changes

**Important:** 
- Use HTTPS in production
- Ensure the endpoint is publicly accessible
- The endpoint should respond quickly (within 10 seconds)

---

## Webhook Payload Format

When a payment is received, we'll send a POST request to your webhook URL with the following payload:

### Payment Received Event

```json
{
    "event": "payment.received",
    "transaction": {
        "id": 123,
        "reference": "XTRAPAY_0001_0001_20241209120000_ABC123",
        "external_id": "XTRAPAY_0001_0001_20241209120000_ABC123",
        "amount": 5000.00,
        "currency": "NGN",
        "status": "success",
        "payment_method": "virtual_account",
        "customer_email": "customer@example.com",
        "customer_name": "John Doe",
        "description": "Payment for order #12345",
        "created_at": "2024-12-09T12:00:00+00:00",
        "updated_at": "2024-12-09T12:15:30+00:00"
    },
    "site": {
        "id": 1,
        "name": "Your Site Name",
        "api_code": "your_site_api_code"
    },
    "metadata": {
        "order_id": "ORDER_12345",
        "user_id": 123,
        "xtrapay_reference": "XTRAPAY_0001_0001_20241209120000_ABC123",
        "account_number": "8041009815",
        "bank_name": "Wema Bank",
        "account_name": "XtraPay Virtual Account"
    },
    "timestamp": "2024-12-09T12:15:30+00:00"
}
```

### Event Types

Currently supported events:
- `payment.received` - Payment has been successfully received

---

## Handling Webhook Notifications

### PHP Example

```php
<?php
// webhooks/xtrapay.php

// Log the incoming webhook (for debugging)
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . "\n" . file_get_contents('php://input') . "\n\n", FILE_APPEND);

// Get the webhook payload
$payload = json_decode(file_get_contents('php://input'), true);

// Verify event type
if (!isset($payload['event']) || $payload['event'] !== 'payment.received') {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid event type']);
    exit;
}

// Extract transaction data
$transaction = $payload['transaction'];
$reference = $transaction['reference'];
$amount = $transaction['amount'];
$status = $transaction['status'];
$customerEmail = $transaction['customer_email'];
$metadata = $payload['metadata'] ?? [];

// Verify payment status
if ($status !== 'success') {
    http_response_code(200); // Still return 200 to acknowledge receipt
    echo json_encode(['message' => 'Payment not successful']);
    exit;
}

// Process the payment
try {
    // Find the order using reference or metadata
    $orderId = $metadata['order_id'] ?? null;
    
    if ($orderId) {
        // Update order status in your database
        updateOrderStatus($orderId, 'paid');
        
        // Send confirmation email to customer
        sendConfirmationEmail($customerEmail, $orderId, $amount);
        
        // Update inventory, etc.
        // ... your business logic here
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Webhook processed successfully'
    ]);
    
} catch (Exception $e) {
    // Log error but still return 200 to prevent retries
    error_log('Webhook processing error: ' . $e->getMessage());
    
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => 'Error processing webhook'
    ]);
}

function updateOrderStatus($orderId, $status) {
    // Your database update logic here
    // Example:
    // $db->query("UPDATE orders SET status = ? WHERE id = ?", [$status, $orderId]);
}

function sendConfirmationEmail($email, $orderId, $amount) {
    // Your email sending logic here
}
?>
```

### Node.js/Express Example

```javascript
const express = require('express');
const app = express();

app.use(express.json());

app.post('/webhooks/xtrapay', async (req, res) => {
    try {
        const payload = req.body;
        
        // Verify event type
        if (payload.event !== 'payment.received') {
            return res.status(400).json({ error: 'Invalid event type' });
        }
        
        const transaction = payload.transaction;
        
        // Verify payment status
        if (transaction.status !== 'success') {
            return res.status(200).json({ message: 'Payment not successful' });
        }
        
        // Process the payment
        const orderId = payload.metadata?.order_id;
        
        if (orderId) {
            // Update order status in database
            await updateOrderStatus(orderId, 'paid');
            
            // Send confirmation email
            await sendConfirmationEmail(
                transaction.customer_email,
                orderId,
                transaction.amount
            );
        }
        
        // Return success response
        res.status(200).json({
            success: true,
            message: 'Webhook processed successfully'
        });
        
    } catch (error) {
        console.error('Webhook processing error:', error);
        // Still return 200 to prevent retries
        res.status(200).json({
            success: false,
            message: 'Error processing webhook'
        });
    }
});

async function updateOrderStatus(orderId, status) {
    // Your database update logic here
}

async function sendConfirmationEmail(email, orderId, amount) {
    // Your email sending logic here
}

app.listen(3000, () => {
    console.log('Server running on port 3000');
});
```

### Python/Flask Example

```python
from flask import Flask, request, jsonify
import json

app = Flask(__name__)

@app.route('/webhooks/xtrapay', methods=['POST'])
def handle_xtrapay_webhook():
    try:
        payload = request.get_json()
        
        # Verify event type
        if payload.get('event') != 'payment.received':
            return jsonify({'error': 'Invalid event type'}), 400
        
        transaction = payload.get('transaction')
        
        # Verify payment status
        if transaction.get('status') != 'success':
            return jsonify({'message': 'Payment not successful'}), 200
        
        # Process the payment
        order_id = payload.get('metadata', {}).get('order_id')
        
        if order_id:
            # Update order status
            update_order_status(order_id, 'paid')
            
            # Send confirmation email
            send_confirmation_email(
                transaction.get('customer_email'),
                order_id,
                transaction.get('amount')
            )
        
        return jsonify({
            'success': True,
            'message': 'Webhook processed successfully'
        }), 200
        
    except Exception as e:
        print(f'Webhook processing error: {str(e)}')
        return jsonify({
            'success': False,
            'message': 'Error processing webhook'
        }), 200

def update_order_status(order_id, status):
    # Your database update logic here
    pass

def send_confirmation_email(email, order_id, amount):
    # Your email sending logic here
    pass

if __name__ == '__main__':
    app.run(port=3000)
```

---

## Checking Payment Status

You can check the payment status of a transaction using the API:

### API Endpoint

```
POST https://xtrapay.cash/api/v1/virtual-accounts/check-status
```

### Request Body

```json
{
    "site_api_code": "your_site_api_code",
    "reference": "XTRAPAY_0001_0001_20241209120000_ABC123"
}
```

### Response

```json
{
    "success": true,
    "data": {
        "transaction_id": 123,
        "reference": "XTRAPAY_0001_0001_20241209120000_ABC123",
        "status": "success",
        "amount": 5000.00,
        "xtrapay_status": {
            "status": "completed",
            "amount": "500000"
        }
    }
}
```

---

## Complete Integration Example

Here's a complete example showing the full payment flow:

### Step 1: Customer Initiates Payment

```php
<?php
// When customer clicks "Pay Now"
session_start();

$orderId = $_POST['order_id'];
$amount = $_POST['amount'];
$customerEmail = $_POST['email'];
$customerName = $_POST['name'];

// Request virtual account
$accountDetails = requestVirtualAccount($amount, $customerEmail, $customerName, $orderId);

if ($accountDetails) {
    // Store reference in session
    $_SESSION['payment_reference'] = $accountDetails['reference'];
    $_SESSION['order_id'] = $orderId;
    
    // Display account details to customer
    echo "<h2>Payment Instructions</h2>";
    echo "<p>Transfer ₦" . number_format($amount, 2) . " to:</p>";
    echo "<p><strong>Account Number:</strong> " . $accountDetails['account_number'] . "</p>";
    echo "<p><strong>Bank:</strong> " . $accountDetails['bank_name'] . "</p>";
    echo "<p><strong>Account Name:</strong> " . $accountDetails['account_name'] . "</p>";
    echo "<p>Your payment will be confirmed automatically.</p>";
} else {
    echo "Error generating payment account. Please try again.";
}
?>
```

### Step 2: Handle Webhook Notification

```php
<?php
// webhooks/xtrapay.php
$payload = json_decode(file_get_contents('php://input'), true);

if ($payload['event'] === 'payment.received' && $payload['transaction']['status'] === 'success') {
    $reference = $payload['transaction']['reference'];
    $orderId = $payload['metadata']['order_id'];
    
    // Update order status
    updateOrderStatus($orderId, 'paid');
    
    // Send confirmation email
    sendConfirmationEmail(
        $payload['transaction']['customer_email'],
        $orderId,
        $payload['transaction']['amount']
    );
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
```

### Step 3: Customer Checks Status (Optional)

```php
<?php
// check_payment_status.php
$reference = $_SESSION['payment_reference'] ?? null;

if ($reference) {
    $status = checkPaymentStatus($reference);
    
    if ($status && $status['status'] === 'success') {
        echo "Payment confirmed! Your order is being processed.";
    } else {
        echo "Payment pending. Please complete your transfer.";
    }
}
?>
```

---

## Security Best Practices

1. **Use HTTPS:** Always use HTTPS for webhook URLs in production
2. **Verify Requests:** Consider implementing request verification using IP whitelisting or signatures
3. **Idempotency:** Make your webhook handler idempotent (handle duplicate webhooks gracefully)
4. **Store References:** Store transaction references in your database to prevent duplicate processing
5. **Log Everything:** Log all webhook requests for debugging and audit purposes
6. **Timeout Handling:** Ensure your webhook endpoint responds within 10 seconds
7. **Error Handling:** Always return 200 status code even on errors to prevent retries

### Example: Idempotent Webhook Handler

```php
<?php
function handleWebhook($payload) {
    $reference = $payload['transaction']['reference'];
    
    // Check if already processed
    if (isTransactionProcessed($reference)) {
        return ['message' => 'Already processed'];
    }
    
    // Process payment
    processPayment($payload);
    
    // Mark as processed
    markTransactionAsProcessed($reference);
    
    return ['success' => true];
}

function isTransactionProcessed($reference) {
    // Check database if transaction was already processed
    // Return true if already processed
}
?>
```

---

## Troubleshooting

### Webhook Not Received

1. **Check Webhook URL:** Ensure the URL is correct and publicly accessible
2. **Check Logs:** Check Xtrabusiness logs for webhook delivery attempts
3. **Test Endpoint:** Use a tool like ngrok to test your webhook endpoint
4. **Check Firewall:** Ensure your server allows incoming POST requests

### Payment Not Updating

1. **Check Webhook Response:** Ensure your webhook returns 200 status code
2. **Check Logs:** Review webhook processing logs for errors
3. **Verify Reference:** Ensure the reference matches your stored transaction
4. **Check Status:** Verify the payment status in the webhook payload

### Common Errors

**"Invalid API key"**
- Verify your API key is correct
- Check for extra spaces or characters

**"Site not found"**
- Verify your site_api_code is correct
- Ensure your site is active in the dashboard

**"Webhook timeout"**
- Optimize your webhook handler
- Ensure it responds within 10 seconds

---

## Support

For additional help:
1. Check the API documentation: `XtraPay Virtual Account API Documentation`
2. Review logs in your Xtrabusiness dashboard
3. Contact support with your transaction reference

---

## Quick Reference

### API Endpoints

- **Request Account:** `POST /api/v1/virtual-accounts/request`
- **Check Status:** `POST /api/v1/virtual-accounts/check-status`

### Required Headers

```
Content-Type: application/json
Accept: application/json
X-API-Key: your_site_api_key
```

### Minimum Amount

- ₦1.00 (100 kobo)

### Account Expiry

- 24 hours from creation

### Webhook Timeout

- 10 seconds

---

**Last Updated:** December 2024

