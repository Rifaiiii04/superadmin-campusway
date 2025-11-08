#!/bin/bash

echo "=== Fixing Apache Alias Configuration ==="
echo ""

# Backup current config
echo "1. Backing up current Apache config..."
sudo cp /etc/apache2/sites-enabled/103.23.198.101.conf /etc/apache2/sites-enabled/103.23.198.101.conf.backup.$(date +%Y%m%d_%H%M%S)

# Check current alias
echo ""
echo "2. Current alias configuration:"
sudo grep -n "Alias /super-admin" /etc/apache2/sites-enabled/103.23.198.101.conf

# Check current directory
echo ""
echo "3. Current directory configuration:"
sudo grep -n "Directory.*superadmin" /etc/apache2/sites-enabled/103.23.198.101.conf

# Fix alias if needed
echo ""
echo "4. Fixing alias..."
sudo sed -i 's|/var/www/superadmin/superadmin-campusway/public|/var/www/superadmin/superadmin-backend/public|g' /etc/apache2/sites-enabled/103.23.198.101.conf

# Verify changes
echo ""
echo "5. Verifying changes:"
sudo grep -n "Alias /super-admin" /etc/apache2/sites-enabled/103.23.198.101.conf
sudo grep -n "Directory.*superadmin-backend" /etc/apache2/sites-enabled/103.23.198.101.conf

# Test Apache config
echo ""
echo "6. Testing Apache configuration..."
sudo apache2ctl configtest

if [ $? -eq 0 ]; then
    echo "   ✓ Apache configuration is valid"
    echo ""
    echo "7. Restarting Apache..."
    sudo systemctl restart apache2
    echo "   ✓ Apache restarted"
    echo ""
    echo "=== Fix Complete ==="
    echo ""
    echo "Test the endpoint:"
    echo "curl http://103.23.198.101/super-admin/api/school/test-student-detail/40"
else
    echo "   ✗ Apache configuration has errors!"
    echo "   Restoring backup..."
    sudo cp /etc/apache2/sites-enabled/103.23.198.101.conf.backup.* /etc/apache2/sites-enabled/103.23.198.101.conf
    echo "   Please fix manually"
fi

