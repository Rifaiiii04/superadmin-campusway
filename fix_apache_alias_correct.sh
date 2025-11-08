#!/bin/bash

echo "=== Fixing Apache Alias to Correct Folder ==="
echo ""

CONFIG_FILE="/etc/apache2/sites-enabled/103.23.198.101.conf"

# Backup
echo "1. Backing up current config..."
sudo cp "$CONFIG_FILE" "${CONFIG_FILE}.backup.$(date +%Y%m%d_%H%M%S)"

# Check current
echo ""
echo "2. Current configuration:"
sudo grep -n "Alias /super-admin" "$CONFIG_FILE"
sudo grep -n "Directory.*superadmin" "$CONFIG_FILE"

# Fix
echo ""
echo "3. Fixing alias to superadmin-backend..."
sudo sed -i 's|/var/www/superadmin/superadmin-campusway/public|/var/www/superadmin/superadmin-backend/public|g' "$CONFIG_FILE"

# Verify
echo ""
echo "4. Verifying changes:"
sudo grep -n "Alias /super-admin" "$CONFIG_FILE"
sudo grep -n "Directory.*superadmin-backend" "$CONFIG_FILE"

# Test config
echo ""
echo "5. Testing Apache configuration..."
sudo apache2ctl configtest

if [ $? -eq 0 ]; then
    echo "   ✓ Configuration is valid"
    echo ""
    echo "6. Restarting Apache..."
    sudo systemctl restart apache2
    echo "   ✓ Apache restarted"
    echo ""
    echo "=== Fix Complete ==="
    echo ""
    echo "Now test:"
    echo "curl http://103.23.198.101/super-admin/api/school/test-student-detail/40"
else
    echo "   ✗ Configuration has errors!"
    echo "   Restoring backup..."
    sudo cp "${CONFIG_FILE}.backup."* "$CONFIG_FILE" 2>/dev/null
    echo "   Please check manually"
fi

