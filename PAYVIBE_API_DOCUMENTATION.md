# XtraPay Virtual Account API Documentation

> **📚 For a complete integration guide with webhook setup, see [XtraPay Virtual Account Integration Guide](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md)**

## Overview

This API allows businesses to request virtual account numbers from XtraPay at **https://xtrapay.cash**. XtraPay provides virtual account numbers that your customers can use to make bank transfers. When a business requests an account number, the system will:

1. Authenticate the business using API credentials
2. Generate a virtual account number
3. Create a pending transaction record
4. Return the account details to the business

## Authentication

All API requests require authentication using:
- **X-API-Key Header**: Your site's API key (64 characters)
- **site_api_code**: Your site's API code (in request body)

You can find these credentials in your XtraPay dashboard at **https://xtrapay.cash** under **Sites** → **Your Site** → **API Credentials**.

## 💰 Transaction Fees

### Fee Structure

XtraPay charges a small fee for each virtual account transaction:

- **1.5%** of the transaction amount
- **₦100** flat fee per transaction

### How It Works

When a customer makes a payment:
- **Customer pays:** Full amount (e.g., ₦10,000)
- **Fees deducted:** 1.5% (₦150) + ₦100 = ₦250
- **You receive:** ₦9,750

### Calculating Amount to Request

If you want to receive a specific amount after fees:

```
Amount to Request = (Desired Amount + ₦100) ÷ 0.985
```

**Example:** To receive ₦5,000:
```
Amount to Request = (₦5,000 + ₦100) ÷ 0.985 = ₦5,177.66
```

⚠️ **Important:** Fees are automatically deducted. The amount credited to your balance will be less than the customer paid.

## Endpoints

### 1. Request Virtual Account

Generate a new virtual account number for payment collection.

**Endpoint:** `POST https://xtrapay.cash/api/v1/virtual-accounts/request`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-API-Key: your_site_api_key_here
```

**Request Body:**
```json
{
    "site_api_code": "your_site_api_code",
    "amount": 5000.00,
    "reference": "optional_custom_reference",
    "description": "Payment for order #12345",
    "customer_email": "customer@example.com",
    "customer_name": "John Doe",
    "metadata": {
        "order_id": "ORDER_12345",
        "user_id": 123
    }
}
```

**Required Fields:**
- `site_api_code` (string): Your site's API code
- `amount` (numeric): Amount in Naira (minimum ₦1.00)
  - **Note:** This is the amount the customer will pay
  - Fees (1.5% + ₦100) will be deducted automatically
  - You will receive: `amount - (amount × 0.015) - 100`

**Optional Fields:**
- `reference` (string): Custom reference (max 255 chars). If not provided, system generates one
- `description` (string): Transaction description (max 255 chars)
- `customer_email` (email): Customer's email address
- `customer_name` (string): Customer's name (max 255 chars)
- `metadata` (object): Additional data you want to store

**Success Response (201):**
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

**Error Responses:**

**401 Unauthorized:**
```json
{
    "success": false,
    "message": "API key required. Please provide X-API-Key header."
}
```

**403 Forbidden:**
```json
{
    "success": false,
    "message": "Invalid API key or site not found"
}
```

**422 Validation Error:**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "amount": ["The amount must be at least 100."]
    }
}
```

**500 Server Error:**
```json
{
    "success": false,
    "message": "Failed to generate virtual account"
}
```

### 2. Check Payment Status

Check the status of a payment transaction.

**Endpoint:** `POST /api/v1/virtual-accounts/check-status`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-API-Key: your_site_api_key_here
```

**Request Body:**
```json
{
    "site_api_code": "your_site_api_code",
    "reference": "PAYVIBE_0001_0001_20241209120000_ABC123"
}
```

**Success Response (200):**
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

## Example Usage

### PHP (cURL)

```php
<?php
$apiUrl = 'https://xtrapay.cash/api/v1/virtual-accounts/request';
$apiKey = 'your_site_api_key_here';
$siteApiCode = 'your_site_api_code_here';

$data = [
    'site_api_code' => $siteApiCode,
    'amount' => 5000.00,
    'description' => 'Payment for order #12345',
    'customer_email' => 'customer@example.com',
    'customer_name' => 'John Doe'
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

$result = json_decode($response, true);

if ($httpCode === 201 && $result['success']) {
    echo "Account Number: " . $result['data']['account_number'] . "\n";
    echo "Bank: " . $result['data']['bank_name'] . "\n";
    echo "Amount: ₦" . number_format($result['data']['amount'], 2) . "\n";
} else {
    echo "Error: " . $result['message'] . "\n";
}
?>
```

### JavaScript (Fetch API)

```javascript
const apiUrl = 'https://xtrapay.cash/api/v1/virtual-accounts/request';
const apiKey = 'your_site_api_key_here';
const siteApiCode = 'your_site_api_code_here';

const data = {
    site_api_code: siteApiCode,
    amount: 5000.00,
    description: 'Payment for order #12345',
    customer_email: 'customer@example.com',
    customer_name: 'John Doe'
};

fetch(apiUrl, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-API-Key': apiKey
    },
    body: JSON.stringify(data)
})
.then(response => response.json())
.then(result => {
    if (result.success) {
        console.log('Account Number:', result.data.account_number);
        console.log('Bank:', result.data.bank_name);
        console.log('Amount: ₦' + result.data.amount.toFixed(2));
    } else {
        console.error('Error:', result.message);
    }
})
.catch(error => {
    console.error('Request failed:', error);
});
```

### Python (Requests)

```python
import requests
import json

api_url = 'https://xtrapay.cash/api/v1/virtual-accounts/request'
api_key = 'your_site_api_key_here'
site_api_code = 'your_site_api_code_here'

data = {
    'site_api_code': site_api_code,
    'amount': 5000.00,
    'description': 'Payment for order #12345',
    'customer_email': 'customer@example.com',
    'customer_name': 'John Doe'
}

headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-API-Key': api_key
}

response = requests.post(api_url, json=data, headers=headers)

if response.status_code == 201:
    result = response.json()
    if result['success']:
        print(f"Account Number: {result['data']['account_number']}")
        print(f"Bank: {result['data']['bank_name']}")
        print(f"Amount: ₦{result['data']['amount']:.2f}")
    else:
        print(f"Error: {result['message']}")
else:
    print(f"Request failed with status {response.status_code}")
```

## Payment Flow

1. **Business requests virtual account:**
   - Business calls `/api/v1/virtual-accounts/request` with amount and customer details
   - System authenticates the request
   - System generates virtual account number
   - System creates pending transaction record
   - System returns account details to business

2. **Customer makes payment:**
   - Customer transfers money to the virtual account number
   - Payment is processed automatically

3. **Payment notification:**
   - System sends webhook to your configured webhook URL
   - System updates transaction status to "success"
   - System credits business profile balance (after deducting fees)
   - System sends Telegram notification to business (if configured)

4. **Business checks status:**
   - Business can call `/api/v1/virtual-accounts/check-status` to verify payment
   - Or check transaction status in dashboard

## Important Notes

- **Minimum Amount:** ₦1.00 (100 kobo)
- **Account Expiry:** Virtual accounts expire after 24 hours
- **Transaction Status:** Initially "pending", changes to "success" when payment is received
- **IP Whitelisting:** If your site has IP whitelisting enabled, ensure your server IP is added
- **Rate Limiting:** Consider implementing rate limiting on your side to prevent abuse
- **Security:** Always use HTTPS in production
- **Error Handling:** Always check the `success` field in responses before processing

## Testing

Use the provided test script to test the API:

```bash
php test_api_endpoint.sh
```

Make sure to update the configuration variables in the test script with your actual credentials.

## Webhook Notifications

When payments are received, we can send webhook notifications to your website. To set this up:

1. Configure your **Webhook URL** in your site settings (Dashboard → Sites → Edit Site)
2. Your webhook endpoint should accept POST requests with the payment data
3. See [PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md) for complete webhook setup instructions

## Support

For issues or questions:
1. Check the logs in your XtraPay dashboard at **https://xtrapay.cash**
2. Verify your API credentials are correct
3. Ensure your site is active in the dashboard
4. See [PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md) for detailed integration guide
5. Contact support if issues persist

