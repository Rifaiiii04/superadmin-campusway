#!/bin/bash

echo "=== Checking Apache Configuration ==="
echo ""

# Check if Apache is running
echo "1. Checking Apache status..."
if systemctl is-active --quiet apache2; then
    echo "   ✓ Apache is running"
else
    echo "   ✗ Apache is NOT running"
fi

echo ""
echo "2. Checking Apache modules..."
echo "   - mod_rewrite:"
apache2ctl -M 2>/dev/null | grep rewrite || echo "     ✗ mod_rewrite NOT loaded"
echo "   - mod_headers:"
apache2ctl -M 2>/dev/null | grep headers || echo "     ✗ mod_headers NOT loaded"

echo ""
echo "3. Checking Apache error log (last 20 lines)..."
echo "   --- Apache Error Log ---"
sudo tail -20 /var/log/apache2/error.log 2>/dev/null || echo "   Cannot access error log (need sudo)"

echo ""
echo "4. Checking Laravel log (last 20 lines)..."
echo "   --- Laravel Log ---"
tail -20 storage/logs/laravel.log 2>/dev/null || echo "   No Laravel log found"

echo ""
echo "5. Checking custom debug log..."
echo "   --- Student Detail Debug Log ---"
tail -20 storage/logs/student_detail_debug.log 2>/dev/null || echo "   No debug log found"

echo ""
echo "6. Checking Apache virtual host configuration..."
echo "   Active virtual hosts:"
sudo apache2ctl -S 2>/dev/null | grep -E "namevhost|port" || echo "   Cannot check (need sudo)"

echo ""
echo "7. Testing route directly..."
echo "   Testing: GET /super-admin/api/school/students/40"
curl -s -o /dev/null -w "   Status: %{http_code}\n" \
  -H "Authorization: Bearer test" \
  http://localhost/super-admin/api/school/students/40 || echo "   ✗ Cannot test route"

echo ""
echo "=== Check Complete ==="

