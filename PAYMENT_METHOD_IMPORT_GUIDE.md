# Payment Method Import Guide

This guide explains how to configure the external database import to filter for instant payment methods only.

## Overview

The system now supports filtering transactions by payment method type to ensure only instant payment methods are imported. This helps maintain consistency across different external databases that may use different naming conventions or coding systems for payment methods.

## Configuration Options

### Method Type

Choose how payment methods are stored in your external database:

1. **Method** - Direct method names (e.g., "instant", "card", "bank_transfer")
2. **Method Code** - Numeric codes that map to method types (e.g., 1=instant, 2=manual, 3=crypto)

### Required Fields

- **Method Key**: The JSON key that contains the payment method information
- **Instant Method Values**: List of values that indicate instant payment methods (one per line)
- **Method Code Mapping** (optional): Mapping of codes to method types (only needed for method codes)

## Configuration Examples

### Example 1: Direct Method Names

If your database stores payment methods as direct names:

```
Method Type: Method
Method Key: payment_method
Instant Method Values:
instant
card
bank_transfer
online_payment
```

### Example 2: Method Codes

If your database uses numeric codes:

```
Method Type: Method Code
Method Key: payment_method_code
Instant Method Values:
instant
card
bank_transfer
Method Code Mapping:
1=instant
2=manual
3=crypto
4=card
5=bank_transfer
```

## How It Works

1. **Connection**: The system connects to your external database using the configured credentials
2. **Filtering**: Only transactions with the specified status AND instant payment methods are selected
3. **Import**: Filtered transactions are imported into the local system
4. **Logging**: Detailed logs are created for monitoring and debugging

## Database Structure Requirements

Your external database should have:

1. **Transaction Table**: Contains transaction records with JSON data
2. **User Table**: Contains user information
3. **JSON Column**: Contains payment data including method information
4. **Status Field**: Indicates transaction completion status

## Testing

Use the following command to test the import functionality:

```bash
php artisan test:import-transactions
```

Check the logs for detailed information about the import process.

## Monitoring

The system logs the following information:

- Number of transactions processed per site
- Payment method filtering details
- Any errors or warnings during import
- Success/failure status for each site

## Troubleshooting

### Common Issues

1. **No transactions imported**: Check that your instant method values match the actual data in your database
2. **Method code mapping errors**: Ensure the format is correct (code=type, one per line)
3. **Database connection issues**: Verify database credentials and network connectivity

### Log Locations

Check the Laravel logs for detailed information:
- `storage/logs/laravel.log`

## Scheduled Import

The import job runs automatically every hour. You can modify the schedule in `app/Console/Kernel.php`:

```php
$schedule->job(new ImportExternalTransactions)->hourly();
```

## Security Notes

- Database credentials are stored encrypted in the database
- Only instant payment methods are imported to maintain data quality
- Duplicate transactions are automatically skipped based on reference number and site ID 