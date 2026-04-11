#!/bin/bash

# IdeaSync Local Development Startup Script

echo "🚀 Starting IdeaSync Local Development Environment..."
echo ""

# Use system PHP with MySQL support
PHP_BIN="/usr/bin/php8.3"

# Check if PHP is available
if ! command -v $PHP_BIN &> /dev/null; then
    echo "❌ PHP 8.3 not found. Installing..."
    sudo apt-get install -y php8.3-cli php8.3-mysql
fi

# Check MySQL status
echo "Checking MySQL..."
if sudo service mysql status > /dev/null 2>&1; then
  echo "✓ MySQL is running"
else
  echo "Starting MySQL..."
  sudo service mysql start
fi

# Verify database exists
echo "Verifying database..."
if sudo mysql -e "SELECT 1 FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'ideaspace_dev';" | grep 1 > /dev/null 2>&1; then
  echo "✓ Database 'ideaspace_dev' ready"
else
  echo "Setting up database..."
  cd /workspaces/Ideaspace 
  ./setup-db.sh
fi

echo ""
echo "✓ All systems ready!"
echo ""
echo "Starting PHP Development Server..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📱 Application URL: http://localhost:8080"
echo "📊 Home Page: http://localhost:8080/?page=home"
echo "🏠 Dashboard: http://localhost:8080/?page=dashboard"
echo "💡 Ideas: http://localhost:8080/?page=ideas"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Database Details:"
echo "  Host: localhost (127.0.0.1:3306)"
echo "  Database: ideaspace_dev"
echo "  User: ideaspace_user"
echo ""
echo "Test Credentials:"
echo "  Email: harshith@example.com"  
echo "  Email: priya@example.com"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Start PHP server
cd /workspaces/Ideaspace/public
$PHP_BIN -S localhost:8080
