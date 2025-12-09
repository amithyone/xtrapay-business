# XtraPay Virtual Account Integration Documentation

## 📚 Documentation for Businesses

Welcome to XtraPay Virtual Account Integration! This documentation will help you integrate XtraPay Virtual Accounts into your website to collect payments from customers.

**Platform:** XtraPay  
**Website:** https://xtrapay.cash  
**API Base URL:** https://xtrapay.cash/api/v1

---

## 🚀 Quick Start

**New to XtraPay Virtual Accounts?** Start here:

👉 **[Quick Start Guide](./PAYVIBE_QUICK_START.md)** - Get up and running in 5 minutes

---

## 📖 Complete Documentation

### 1. **[Business Integration Guide](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md)**
   - Complete step-by-step integration guide
   - How to request virtual account numbers
   - Setting up webhook notifications
   - Webhook payload format and handling
   - Complete code examples (PHP, JavaScript, Python)
   - Security best practices
   - Troubleshooting guide

### 2. **[API Documentation](./PAYVIBE_API_DOCUMENTATION.md)**
   - API endpoint reference
   - Request/response formats
   - Authentication details
   - Error handling
   - Code examples

### 3. **[Transaction Isolation Guide](./TRANSACTION_ISOLATION.md)**
   - How transaction isolation works
   - Security measures
   - How webhooks are routed correctly
   - No cross-contamination guarantees

---

## 🎯 What You Can Do

1. **Request Virtual Account Numbers**
   - Generate virtual accounts via API
   - Display to customers for payment
   - Track payment status

2. **Receive Real-Time Notifications**
   - Get webhook notifications when payments are received
   - Automatically update orders
   - Send confirmation emails

3. **Check Payment Status**
   - Query payment status via API
   - Verify transactions
   - Handle payment confirmations

---

## 🔑 Getting Your API Credentials

1. Log in to **https://xtrapay.cash**
2. Go to **Sites** → Select your site
3. Copy your:
   - **API Code**
   - **API Key**
4. Configure your **Webhook URL**

---

## 📞 API Endpoints

### Request Virtual Account
```
POST https://xtrapay.cash/api/v1/virtual-accounts/request
```

### Check Payment Status
```
POST https://xtrapay.cash/api/v1/virtual-accounts/check-status
```

### Webhook (Receive Notifications)
```
Your Webhook URL (configured in dashboard)
```

---

## 💡 Quick Example

**Request Account:**
```php
POST https://xtrapay.cash/api/v1/virtual-accounts/request
Headers: X-API-Key: your_api_key
Body: {
    "site_api_code": "your_code",
    "amount": 5000.00,
    "customer_email": "customer@example.com"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "account_number": "8041009815",
        "bank_name": "Wema Bank",
        "account_name": "XtraPay Virtual Account",
        "amount": 5000.00
    }
}
```

---

## 💰 Transaction Fees

XtraPay charges a small fee for each transaction:

- **1.5%** of transaction amount
- **₦100** flat fee per transaction

**Example:** Customer pays ₦10,000
- Fees: ₦150 (1.5%) + ₦100 = ₦250
- **You receive: ₦9,750**

Fees are automatically deducted. See documentation for fee calculation formulas.

## 📋 Requirements

- **Minimum Amount:** ₦1.00
- **Account Expiry:** 24 hours
- **Webhook Timeout:** 10 seconds
- **HTTPS:** Required in production
- **Fees:** Automatically deducted (1.5% + ₦100)

---

## 🆘 Need Help?

1. Check the [Quick Start Guide](./PAYVIBE_QUICK_START.md)
2. Review the [Complete Integration Guide](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md)
3. Check your API credentials in dashboard
4. Verify webhook URL is accessible
5. Review logs in dashboard

---

## 📝 Documentation Files

- **[XtraPay Quick Start Guide](./PAYVIBE_QUICK_START.md)** - Quick start guide
- **[XtraPay Virtual Account Integration Guide](./PAYVIBE_BUSINESS_INTEGRATION_GUIDE.md)** - Complete integration guide
- **[XtraPay Virtual Account API Documentation](./PAYVIBE_API_DOCUMENTATION.md)** - API reference
- **[Fee Calculator Guide](./FEE_CALCULATOR.md)** - Fee calculation examples and formulas
- **[Transaction Isolation Guide](./TRANSACTION_ISOLATION.md)** - Security and isolation

---

**Last Updated:** December 2024  
**Platform:** XtraPay (xtrapay.cash)

