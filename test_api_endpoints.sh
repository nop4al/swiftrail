#!/bin/bash

# Swiftrail API Endpoint Testing Script
# Tests all critical endpoints in the booking flow

BASE_URL="http://localhost/api/v1"

echo "====== SWIFTRAIL API ENDPOINT TEST ======"
echo "Base URL: $BASE_URL"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test counter
PASSED=0
FAILED=0

# Function to test endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local description=$4

    echo -n "Testing: $description ... "
    
    if [ "$method" == "GET" ]; then
        response=$(curl -s -X GET "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -w "\n%{http_code}")
    else
        response=$(curl -s -X POST "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -d "$data" \
            -w "\n%{http_code}")
    fi

    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')

    if [[ $http_code =~ ^[2] ]]; then
        echo -e "${GREEN}✓ PASS${NC} ($http_code)"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}✗ FAIL${NC} ($http_code)"
        FAILED=$((FAILED + 1))
        echo "  Response: $body"
    fi
    echo ""
}

# Test 1: Health Check
test_endpoint "GET" "/health" "" "Health Check"

# Test 2: Get All Schedules
test_endpoint "GET" "/schedules" "" "Get All Schedules"

# Test 3: Get Schedule Details
test_endpoint "GET" "/schedules/1" "" "Get Schedule Details (ID: 1)"

# Test 4: Get Payment Methods
test_endpoint "GET" "/payments/methods" "" "Get Payment Methods"

# Test 5: Get Trains
test_endpoint "GET" "/trains" "" "Get All Trains"

# Test 6: Get Stations
test_endpoint "GET" "/stations" "" "Get All Stations"

# Test 7: Get Promos
test_endpoint "GET" "/promos" "" "Get All Promos"

# Test 8: Get Destinations
test_endpoint "GET" "/destinations" "" "Get All Destinations"

# Test Summary
echo "====== TEST SUMMARY ======"
echo -e "${GREEN}Passed: $PASSED${NC}"
echo -e "${RED}Failed: $FAILED${NC}"
echo "Total: $((PASSED + FAILED))"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed!${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed!${NC}"
    exit 1
fi
