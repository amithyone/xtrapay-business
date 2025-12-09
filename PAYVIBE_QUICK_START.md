# XtraPay Virtual Account Integration - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Get Your Credentials (2 minutes)

1. Log in to XtraPay dashboard at **https://xtrapay.cash**
2. Go to **Sites** → **Create New Site** (if you don't have one)
   - Enter your site name, URL, and webhook URL
   - **API Code and API Key are automatically generated** for you
3. After creating your site, go to **Sites** → Select your site
4. Copy these auto-generated values:
   - **API Code** (e.g., `abc123xy`) - Auto-generated
   - **API Key** (64 characters) - Auto-generated
   - **Webhook URL** - The URL you entered where you'll receive notifications

**What is this?** XtraPay provides virtual account numbers that your customers can use to make bank transfers. When they pay, you'll receive instant notifications. **No manual API key generation needed** - everything is set up automatically!

### Step 2: Set Your Webhook URL (1 minute)

1. In site settings, enter your webhook URL:
   ```
   https://yourwebsite.com/webhooks/xtrapay
   ```
2. Save changes

### Step 3: Request Account Number (2 minutes)

**PHP Example:**
```php
<?php
$ch = curl_init('https://xtrapay.cash/api/v1/virtual-accounts/request');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        'site_api_code' => 'your_api_code',
        'amount' => 5000.00,
        'customer_email' => 'customer@example.com',
        'customer_name' => 'John Doe'
    ]),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-API-Key: your_api_key'
    ],
]);

$response = json_decode(curl_exec($ch), true);
curl_close($ch);

if ($response['success']) {
    echo "Account: " . $response['data']['account_number'];
    echo "Bank: " . $response['data']['bank_name'];
}
?>
```

**JavaScript Example:**
```javascript
const response = await fetch('https://xtrapay.cash/api/v1/virtual-accounts/request', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-API-Key': 'your_api_key'
    },
    body: JSON.stringify({
        site_api_code: 'your_api_code',
        amount: 5000.00,
        customer_email: 'customer@example.com',
        customer_name: 'John Doe'
    })
});

const result = await response.json();
if (result.success) {
    console.log('Account:', result.data.account_number);
    console.log('Bank:', result.data.bank_name);
}
```

### Step 4: Handle Webhook Notifications

Create a webhook endpoint on your website to receive payment notifications:

**PHP Example:**
```php
<?php
// webhooks/xtrapay.php
$payload = json_decode(file_get_contents('php://input'), true);

if ($payload['event'] === 'payment.received' && 
    $payload['transaction']['status'] === 'success') {
    
    $orderId = $payload['metadata']['order_id'];
    $amount = $payload['transaction']['amount'];
    
    // Update your order status
    updateOrderStatus($orderId, 'paid');
    
    // Send confirmation email
    sendEmail($payload['transaction']['customer_email'], $orderId);
}

http_response_code(200);
echo json_encode(['success' => true]);
?>
```

**Node.js Example:**
```javascript
app.post('/webhooks/xtrapay', (req, res) => {
    const payload = req.body;
    
    if (payload.event === 'payment.received' && 
        payload.transaction.status === 'success') {
        
        const orderId = payload.metadata.order_id;
        
        // Update order status
        updateOrderStatus(orderId, 'paid');
        
        // Send confirmation email
        sendEmail(payload.transaction.customer_email, orderId);
    }
    
    res.status(200).json({ success: true });
});
```

## 📋 What Happens Next?

1. **Customer pays** → Transfers money to the virtual account number
2. **XtraPay processes** → Payment is verified automatically
3. **Webhook sent** → Your website receives instant notification
4. **Order updated** → Your system processes the payment
5. **Customer notified** → Confirmation email sent

## 🔍 Testing

1. Request a test account with small amount (₦100)
2. Make a test payment
3. Check your webhook logs
4. Verify order status updated

## 📚 Full Documentation

- **Complete Guide:** [XtraPay Virtual Account Integration Guide](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md)
- **API Reference:** [XtraPay API Documentation](./PAYVIBE_API_DOCUMENTATION.md)

## 💰 Transaction Fees

XtraPay charges a small fee for each virtual account transaction:

- **1.5%** of the transaction amount
- **₦100** flat fee per transaction

### Fee Calculation Example

If a customer pays **₦10,000**:
- 1.5% fee: ₦150
- Flat fee: ₦100
- **Total fees: ₦250**
- **Amount credited to you: ₦9,750**

### Important Notes

- **Fees are deducted automatically** when payment is received
- **You will receive:** Requested amount - (1.5% + ₦100)
- **Account for fees** when displaying amounts to customers
- If you want to receive exactly ₦10,000, request ₦10,256.41 from customer
  - Calculation: ₦10,000 ÷ 0.985 + ₦100 = ₦10,256.41

### Fee Calculation Formula

```
Amount to Request = (Desired Amount + ₦100) ÷ 0.985
```

**Example:** To receive ₦5,000:
```
Amount to Request = (₦5,000 + ₦100) ÷ 0.985
Amount to Request = ₦5,177.66
```

## ⚠️ Important Notes

- Minimum amount: ₦1.00
- Account expires: 24 hours
- Webhook timeout: 10 seconds
- Always use HTTPS in production
- Store transaction references to prevent duplicates
- **Fees are automatically deducted** - plan accordingly

## 🆘 Need Help?

1. Check your site's API credentials
2. Verify webhook URL is accessible
3. Review logs in dashboard
4. See full documentation for troubleshooting

---

**Ready to go live?** Follow the complete guide for production-ready integration!

