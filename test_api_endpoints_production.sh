#!/bin/bash

# =====================================================
# Test API Endpoints Production
# =====================================================

# Production Base URL
BASE_URL="http://103.23.198.101/super-admin/api"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo "=========================================="
echo "Testing API Endpoints Production"
echo "=========================================="
echo ""

# Function to test endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    local data=$4
    
    echo -e "${BLUE}Testing: $description${NC}"
    echo -e "${YELLOW}$method $BASE_URL$endpoint${NC}"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -w "\nHTTP_CODE:%{http_code}" "$BASE_URL$endpoint")
    else
        response=$(curl -s -w "\nHTTP_CODE:%{http_code}" -X "$method" "$BASE_URL$endpoint" \
            -H "Content-Type: application/json" \
            -d "$data")
    fi
    
    http_code=$(echo "$response" | grep "HTTP_CODE:" | cut -d: -f2)
    body=$(echo "$response" | sed '/HTTP_CODE:/d')
    
    if [ "$http_code" = "200" ] || [ "$http_code" = "201" ]; then
        echo -e "${GREEN}✅ Status: $http_code${NC}"
        echo "$body" | python3 -m json.tool 2>/dev/null || echo "$body"
    else
        echo -e "${RED}❌ Status: $http_code${NC}"
        echo "$body"
    fi
    
    echo ""
}

echo "=========================================="
echo "1. STUDENT WEB API (Public)"
echo "=========================================="
echo ""

test_endpoint "GET" "/web/health" "Health Check"
test_endpoint "GET" "/web/schools" "Get Schools List"
test_endpoint "GET" "/web/majors" "Get Majors List"

echo "=========================================="
echo "2. PUBLIC API (SuperAdmin Integration)"
echo "=========================================="
echo ""

test_endpoint "GET" "/public/health" "Public Health Check"
test_endpoint "GET" "/public/schools" "Public Schools List"
test_endpoint "GET" "/public/majors" "Public Majors List"

echo "=========================================="
echo "3. TKA SCHEDULES (Public)"
echo "=========================================="
echo ""

test_endpoint "GET" "/web/tka-schedules" "Get TKA Schedules"
test_endpoint "GET" "/web/tka-schedules/upcoming" "Get Upcoming TKA Schedules"
test_endpoint "GET" "/tka-schedules" "Get Public TKA Schedules"
test_endpoint "GET" "/tka-schedules/upcoming" "Get Public Upcoming TKA Schedules"

echo "=========================================="
echo "Testing Complete!"
echo "=========================================="
echo ""
echo "Next Steps:"
echo "1. If all tests pass, update frontend configuration"
echo "2. Test frontend integration"
echo "3. Monitor error logs: tail -f /var/www/html/super-admin/storage/logs/laravel.log"
echo ""

