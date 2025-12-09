# XtraPay Fee Calculator

## Quick Fee Calculator

Use this guide to calculate fees and determine the amount to request from customers.

## Fee Structure

- **1.5%** of transaction amount
- **₦100** flat fee per transaction

## Formula

### Calculate Fees
```
Fees = (Amount × 0.015) + 100
Amount You Receive = Amount - Fees
```

### Calculate Amount to Request (to receive specific amount)
```
Amount to Request = (Desired Amount + 100) ÷ 0.985
```

## Examples

### Example 1: Customer pays ₦1,000
```
Transaction Amount: ₦1,000
1.5% Fee: ₦15
Flat Fee: ₦100
Total Fees: ₦115
Amount You Receive: ₦885
```

### Example 2: Customer pays ₦5,000
```
Transaction Amount: ₦5,000
1.5% Fee: ₦75
Flat Fee: ₦100
Total Fees: ₦175
Amount You Receive: ₦4,825
```

### Example 3: Customer pays ₦10,000
```
Transaction Amount: ₦10,000
1.5% Fee: ₦150
Flat Fee: ₦100
Total Fees: ₦250
Amount You Receive: ₦9,750
```

### Example 4: Customer pays ₦50,000
```
Transaction Amount: ₦50,000
1.5% Fee: ₦750
Flat Fee: ₦100
Total Fees: ₦850
Amount You Receive: ₦49,150
```

### Example 5: Customer pays ₦100,000
```
Transaction Amount: ₦100,000
1.5% Fee: ₦1,500
Flat Fee: ₦100
Total Fees: ₦1,600
Amount You Receive: ₦98,400
```

## Reverse Calculation: How Much to Request

If you want to receive a specific amount, calculate backwards:

### Example: Want to receive ₦5,000
```
Amount to Request = (₦5,000 + ₦100) ÷ 0.985
Amount to Request = ₦5,177.66

Verification:
Customer pays: ₦5,177.66
1.5% fee: ₦77.66
Flat fee: ₦100.00
Total fees: ₦177.66
You receive: ₦5,000.00 ✅
```

### Example: Want to receive ₦10,000
```
Amount to Request = (₦10,000 + ₦100) ÷ 0.985
Amount to Request = ₦10,256.41

Verification:
Customer pays: ₦10,256.41
1.5% fee: ₦153.85
Flat fee: ₦100.00
Total fees: ₦253.85
You receive: ₦10,002.56 (≈ ₦10,000) ✅
```

### Example: Want to receive ₦20,000
```
Amount to Request = (₦20,000 + ₦100) ÷ 0.985
Amount to Request = ₦20,406.09

Verification:
Customer pays: ₦20,406.09
1.5% fee: ₦306.09
Flat fee: ₦100.00
Total fees: ₦406.09
You receive: ₦20,000.00 ✅
```

## Quick Reference Table

| Customer Pays | 1.5% Fee | Flat Fee | Total Fees | You Receive |
|---------------|----------|----------|------------|-------------|
| ₦1,000        | ₦15      | ₦100     | ₦115       | ₦885        |
| ₦2,500        | ₦37.50   | ₦100     | ₦137.50    | ₦2,362.50   |
| ₦5,000        | ₦75      | ₦100     | ₦175       | ₦4,825      |
| ₦10,000       | ₦150     | ₦100     | ₦250       | ₦9,750      |
| ₦25,000       | ₦375     | ₦100     | ₦475       | ₦24,525     |
| ₦50,000       | ₦750     | ₦100     | ₦850       | ₦49,150     |
| ₦100,000      | ₦1,500   | ₦100     | ₦1,600     | ₦98,400     |

## Reverse Calculation Table

| You Want to Receive | Amount to Request | Customer Pays |
|---------------------|-------------------|---------------|
| ₦1,000              | ₦1,116.75         | ₦1,116.75     |
| ₦5,000              | ₦5,177.66         | ₦5,177.66     |
| ₦10,000             | ₦10,256.41        | ₦10,256.41    |
| ₦20,000             | ₦20,406.09        | ₦20,406.09    |
| ₦50,000             | ₦50,761.42        | ₦50,761.42    |
| ₦100,000            | ₦101,522.84       | ₦101,522.84   |

## Code Examples

### PHP: Calculate Amount to Request
```php
function calculateAmountToRequest($desiredAmount) {
    return ($desiredAmount + 100) / 0.985;
}

// Example: Want to receive ₦10,000
$amountToRequest = calculateAmountToRequest(10000);
echo "Request: ₦" . number_format($amountToRequest, 2);
// Output: Request: ₦10,256.41
```

### JavaScript: Calculate Amount to Request
```javascript
function calculateAmountToRequest(desiredAmount) {
    return (desiredAmount + 100) / 0.985;
}

// Example: Want to receive ₦10,000
const amountToRequest = calculateAmountToRequest(10000);
console.log(`Request: ₦${amountToRequest.toFixed(2)}`);
// Output: Request: ₦10,256.41
```

### PHP: Calculate Fees
```php
function calculateFees($amount) {
    $percentageFee = $amount * 0.015;
    $flatFee = 100;
    $totalFees = $percentageFee + $flatFee;
    $amountReceived = $amount - $totalFees;
    
    return [
        'transaction_amount' => $amount,
        'percentage_fee' => $percentageFee,
        'flat_fee' => $flatFee,
        'total_fees' => $totalFees,
        'amount_received' => $amountReceived
    ];
}

// Example
$result = calculateFees(10000);
echo "Customer pays: ₦" . number_format($result['transaction_amount'], 2) . "\n";
echo "Fees: ₦" . number_format($result['total_fees'], 2) . "\n";
echo "You receive: ₦" . number_format($result['amount_received'], 2) . "\n";
```

## Important Notes

1. **Fees are automatic** - No need to pay separately
2. **Deducted on payment** - Fees are deducted when customer pays
3. **Plan accordingly** - Account for fees when displaying amounts
4. **Minimum amount** - Ensure customer pays enough to cover fees
5. **Round appropriately** - Use 2 decimal places for Naira amounts

## Support

For questions about fees or calculations, contact support at https://xtrapay.cash

