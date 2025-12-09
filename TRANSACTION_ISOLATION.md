# Transaction Isolation & Security

## Overview

This document explains how the PayVibe integration ensures that each business/site only receives webhooks and transactions for their own payments, preventing any cross-contamination or conflicts.

## How Transaction Isolation Works

### 1. Reference Generation (Unique per Site/Business)

When a business requests a virtual account, the reference is generated with site and business information:

```php
Reference Format: PAYVIBE_{BUSINESS_ID}_{SITE_ID}_{TIMESTAMP}_{RANDOM}
Example: PAYVIBE_0001_0001_20241209120000_ABC123
```

**Key Points:**
- Each reference contains `site_id` and `business_profile_id`
- References are unique and traceable
- No two businesses can have the same reference

### 2. Transaction Creation (Linked to Site & Business)

When a virtual account is requested:

```php
Transaction::create([
    'site_id' => $site->id,                    // Links to specific site
    'business_profile_id' => $site->business_profile_id,  // Links to specific business
    'reference' => $reference,               // Unique reference
    // ... other fields
]);
```

**Isolation Guarantees:**
- Every transaction is linked to ONE site
- Every site belongs to ONE business
- Transaction cannot belong to multiple businesses

### 3. Webhook Processing (Isolation Verification)

When PayVibe sends a webhook:

#### Step 1: Find Transaction by Reference
```php
$transaction = Transaction::where('reference', $reference)->first();
```
- Reference is unique → Only ONE transaction found
- Transaction has `site_id` and `business_profile_id`

#### Step 2: Verify Transaction Relationships
```php
// Verify site exists and matches
$site = Site::find($transaction->site_id);
if ($site->business_profile_id !== $transaction->business_profile_id) {
    // SECURITY: Reject if mismatch detected
    return;
}
```

#### Step 3: Send Webhook to Correct Business
```php
// Get webhook URL from the site
$webhookUrl = $site->webhook_url;

// Send webhook ONLY to this site's URL
Http::post($webhookUrl, $payload);
```

**Isolation Checks:**
- ✅ Transaction belongs to specific site
- ✅ Site belongs to specific business
- ✅ Site-Business relationship verified
- ✅ Webhook sent ONLY to that site's webhook_url

## Security Measures

### 1. Reference Uniqueness
- References are generated with business and site IDs
- Database enforces uniqueness on reference field
- No two transactions can have the same reference

### 2. Relationship Validation
```php
// Before sending webhook, verify:
1. Transaction exists
2. Site exists
3. Business profile exists
4. Site belongs to correct business
5. Site is active
```

### 3. Logging & Audit Trail
Every webhook includes detailed logging:
```php
Log::info('PayVibe Webhook: Transaction isolation verified', [
    'transaction_id' => $transaction->id,
    'site_id' => $site->id,
    'site_name' => $site->name,
    'business_profile_id' => $businessProfile->id,
    'business_name' => $businessProfile->business_name,
    'webhook_url' => $site->webhook_url
]);
```

### 4. Mismatch Detection
If a site-business mismatch is detected:
```php
if ($site->business_profile_id !== $transaction->business_profile_id) {
    Log::error('Site-Business mismatch detected!');
    // Webhook is NOT sent - security measure
    return;
}
```

## Flow Diagram

```
PayVibe Webhook Received
         ↓
Find Transaction by Reference
         ↓
Verify Transaction → Site → Business Chain
         ↓
Check Site-Business Match
         ↓
Get Site's Webhook URL
         ↓
Send Webhook ONLY to That URL
         ↓
Log All Details for Audit
```

## Example Scenario

### Business A (Site 1)
- **Business ID:** 1
- **Site ID:** 1
- **Webhook URL:** `https://business-a.com/webhooks/payvibe`
- **Reference:** `PAYVIBE_0001_0001_...`

### Business B (Site 2)
- **Business ID:** 2
- **Site ID:** 2
- **Webhook URL:** `https://business-b.com/webhooks/payvibe`
- **Reference:** `PAYVIBE_0002_0002_...`

**When PayVibe sends webhook for Business A's payment:**
1. Reference: `PAYVIBE_0001_0001_...`
2. Find transaction → Site ID: 1, Business ID: 1
3. Get Site 1 → Webhook URL: `https://business-a.com/webhooks/payvibe`
4. Send webhook ONLY to Business A's URL ✅

**Business B will NEVER receive Business A's webhook** because:
- Different reference (contains different site/business IDs)
- Different transaction record
- Different site record
- Different webhook URL

## Database Structure

### Transactions Table
```sql
transactions
├── id (primary key)
├── reference (unique, indexed)
├── site_id (foreign key → sites.id)
├── business_profile_id (foreign key → business_profiles.id)
└── ... other fields
```

### Sites Table
```sql
sites
├── id (primary key)
├── business_profile_id (foreign key → business_profiles.id)
├── webhook_url (unique per site)
└── ... other fields
```

### Business Profiles Table
```sql
business_profiles
├── id (primary key)
└── ... other fields
```

**Relationships:**
- One Business Profile → Many Sites
- One Site → Many Transactions
- Transaction → One Site → One Business Profile

## Testing Isolation

### Test Case 1: Same Business, Multiple Sites
- Business A has Site 1 and Site 2
- Payment for Site 1 → Webhook goes to Site 1's URL ✅
- Payment for Site 2 → Webhook goes to Site 2's URL ✅

### Test Case 2: Different Businesses
- Business A payment → Webhook goes to Business A's URL ✅
- Business B payment → Webhook goes to Business B's URL ✅
- No cross-contamination ✅

### Test Case 3: Missing Site
- Transaction with invalid site_id → Webhook NOT sent ✅
- Error logged ✅

### Test Case 4: Site-Business Mismatch
- If somehow site doesn't match transaction business → Webhook NOT sent ✅
- Security error logged ✅

## Monitoring & Alerts

### Logs to Monitor
1. **Isolation Verification:**
   ```
   PayVibe Webhook: Transaction isolation verified
   ```

2. **Mismatch Detection:**
   ```
   PayVibe Webhook: Site-Business mismatch detected!
   ```

3. **Webhook Delivery:**
   ```
   PayVibe Webhook: Webhook sent successfully to business
   ```

### What to Alert On
- Site-Business mismatches (should never happen)
- Missing site/business relationships
- Webhook delivery failures

## Best Practices

1. **Always verify relationships** before sending webhooks
2. **Log everything** for audit trail
3. **Reject mismatches** - don't send webhook if verification fails
4. **Monitor logs** for any anomalies
5. **Test isolation** regularly with different businesses/sites

## Conclusion

The system ensures **100% isolation** between businesses:

✅ Each transaction is linked to ONE site and ONE business
✅ Each site has ONE webhook URL
✅ Webhooks are sent ONLY to the correct business's URL
✅ Multiple verification checks prevent cross-contamination
✅ Detailed logging provides audit trail

**No business will ever receive another business's transaction webhook.**

