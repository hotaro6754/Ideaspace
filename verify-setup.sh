#!/bin/bash

echo "🔍 IdeaSync Setup Verification"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check PHP
echo -n "Checking PHP 8.3..."
if /usr/bin/php8.3 --version > /dev/null 2>&1; then
    echo " ✓"
else
    echo " ✗"
fi

# Check PHP MySQLi
echo -n "Checking PHP MySQL Extension..."
if /usr/bin/php8.3 -m | grep -q mysqli; then
    echo " ✓"
else
    echo " ✗"
fi

# Check MySQL
echo -n "Checking MySQL Server..."
if sudo service mysql status > /dev/null 2>&1; then
    echo " ✓ (running)"
else
    echo " ✗ (not running)"
fi

# Check Database
echo -n "Checking ideaspace_dev database..."
if sudo mysql -e "SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'ideaspace_dev';" 2>/dev/null | grep -q 1; then
    echo " ✓"
else
    echo " ✗"
fi

# Check Web Files
echo -n "Checking public/index.php..."
if [ -f "/workspaces/Ideaspace/public/index.php" ]; then
    echo " ✓"
else
    echo " ✗"
fi

# Check .env
echo -n "Checking .env configuration..."
if [ -f "/workspaces/Ideaspace/.env" ]; then
    echo " ✓"
else
    echo " ✗"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Count data
echo "Database Statistics:"
USERS=$(sudo mysql -u ideaspace_user -pIdeaSpace@Local2024 ideaspace_dev -se "SELECT COUNT(*) FROM users;" 2>/dev/null)
IDEAS=$(sudo mysql -u ideaspace_user -pIdeaSpace@Local2024 ideaspace_dev -se "SELECT COUNT(*) FROM ideas;" 2>/dev/null)
echo "  👥 Users: $USERS"
echo "  💡 Ideas: $IDEAS"

echo ""
echo "✓ Setup verification complete!"
echo ""
echo "To start the server:"
echo "  ./start.sh"
echo ""
