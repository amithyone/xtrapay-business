#!/bin/bash

# Test PayVibe API Endpoint
# This simulates how a business would call the API

echo "🧪 Testing PayVibe API Endpoint"
echo "==============================="
echo ""

# Configuration - Update these with your actual values
API_URL="http://localhost:8000/api/v1/payvibe/request-account"
API_KEY="your_site_api_key_here"
SITE_API_CODE="your_site_api_code_here"

# Test data
AMOUNT=5000.00
CUSTOMER_EMAIL="test@example.com"
CUSTOMER_NAME="Test Customer"

echo "📝 Configuration:"
echo "   API URL: $API_URL"
echo "   Site API Code: $SITE_API_CODE"
echo "   Amount: ₦$AMOUNT"
echo ""

# Check if jq is installed for pretty JSON
if command -v jq &> /dev/null; then
    USE_JQ=true
else
    USE_JQ=false
    echo "⚠️  jq not installed - JSON output won't be formatted"
    echo ""
fi

echo "🔄 Sending request..."
echo ""

# Make the API request
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-Key: $API_KEY" \
  -d "{
    \"site_api_code\": \"$SITE_API_CODE\",
    \"amount\": $AMOUNT,
    \"description\": \"Test payment\",
    \"customer_email\": \"$CUSTOMER_EMAIL\",
    \"customer_name\": \"$CUSTOMER_NAME\",
    \"metadata\": {
      \"test\": true,
      \"order_id\": \"TEST_ORDER_123\"
    }
  }")

# Extract HTTP code and body
HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
BODY=$(echo "$RESPONSE" | sed '$d')

echo "📊 Response:"
echo "   HTTP Code: $HTTP_CODE"
echo ""

if [ "$USE_JQ" = true ]; then
    echo "$BODY" | jq .
else
    echo "$BODY"
fi

echo ""

if [ "$HTTP_CODE" = "201" ]; then
    echo "✅ SUCCESS! Virtual account generated"
    
    if [ "$USE_JQ" = true ]; then
        ACCOUNT_NUMBER=$(echo "$BODY" | jq -r '.data.account_number // empty')
        BANK_NAME=$(echo "$BODY" | jq -r '.data.bank_name // empty')
        REFERENCE=$(echo "$BODY" | jq -r '.data.reference // empty')
        
        if [ ! -z "$ACCOUNT_NUMBER" ]; then
            echo ""
            echo "📋 Account Details:"
            echo "   Account Number: $ACCOUNT_NUMBER"
            echo "   Bank: $BANK_NAME"
            echo "   Reference: $REFERENCE"
        fi
    fi
else
    echo "❌ FAILED"
    echo "   Check the error message above"
fi

echo ""
echo "✅ Test completed!"

