#!/bin/bash
echo "🔍 Testing Application Isolation..."

echo "=== TEST 1: URL Access Patterns ==="

echo "1. Testing SuperAdmin Access:"
echo "   - SuperAdmin Login:"
curl -s -o /dev/null -w "       %{http_code} - http://localhost/super-admin/login\n" http://localhost/super-admin/login

echo "   - SuperAdmin Dashboard (unauthenticated):"
curl -s -o /dev/null -w "       %{http_code} - http://localhost/super-admin/dashboard\n" http://localhost/super-admin/dashboard

echo "2. Testing Guru Dashboard Access:"
echo "   - Guru Login:"
curl -s -o /dev/null -w "       %{http_code} - http://localhost/login\n" http://localhost/login

echo "   - Guru Dashboard:"
curl -s -o /dev/null -w "       %{http_code} - http://localhost/dashboard\n" http://localhost/dashboard

echo "=== TEST 2: Cross-Application Leakage ==="
echo "Testing if applications leak into each other:"

echo "   - Access Guru login from SuperAdmin context:"
curl -s http://localhost/super-admin/login 2>/dev/null | grep -i "guru" > /dev/null && echo "      ❌ GURU CONTENT FOUND IN SUPERADMIN" || echo "      ✅ SuperAdmin isolated"

echo "   - Access SuperAdmin from Guru context:"
curl -s http://localhost/login 2>/dev/null | grep -i "super.admin" > /dev/null && echo "      ❌ SUPERADMIN CONTENT FOUND IN GURU" || echo "      ✅ Guru isolated"

echo "=== TEST 3: Static Assets ==="
JS_FILE=$(find /var/www/superadmin/superadmin-campusway/public/build/assets -name "app-*.js" 2>/dev/null | head -1 | xargs basename 2>/dev/null)
if [ ! -z "$JS_FILE" ]; then
    curl -s -o /dev/null -w "       SuperAdmin JS: %{http_code} - /super-admin/build/assets/$JS_FILE\n" http://localhost/super-admin/build/assets/$JS_FILE
else
    echo "       ❌ No SuperAdmin JS files found"
fi

echo "=== TEST 4: Session Cookies ==="
echo "SuperAdmin session cookie path:"
curl -I http://localhost/super-admin/login 2>/dev/null | grep -i "set-cookie" | head -1

echo "=== ISOLATION TEST COMPLETE ==="
