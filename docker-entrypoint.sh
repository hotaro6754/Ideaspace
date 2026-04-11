#!/bin/bash

# Docker startup script for Railway
# Runs migrations on first deployment if needed

# We use set -e to exit on command failure, but we must be careful
# with commands that are expected to fail during initialization.
set -e

# Source the database config
export DB_HOST=${DB_HOST:-localhost}
export DB_NAME=${DB_NAME:-railway}
export DB_USER=${DB_USER:-root}
export DB_PASSWORD=${DB_PASSWORD:-}
export DB_PORT=${DB_PORT:-3306}

# Check if mysql client is installed
if ! command -v mysql &> /dev/null; then
    echo "[Init] ⚠️  mysql client not found. Skipping database initialization."
else
    # Wait for MySQL to be ready (max 30 seconds)
    echo "[Init] Waiting for MySQL to be ready..."
    max_attempts=30
    attempt=0
    connected=false

    while [ $attempt -lt $max_attempts ]; do
        # Use if statement to prevent set -e from exiting on failure
        if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" -e "SELECT 1" >/dev/null 2>&1; then
            echo "[Init] ✅ MySQL is ready"
            connected=true
            break
        fi

        attempt=$((attempt + 1))
        echo "[Init] Attempt $attempt/$max_attempts: MySQL not ready, waiting..."
        sleep 1
    done

    if [ "$connected" = false ]; then
        echo "[Init] ⚠️  MySQL not available - will continue anyway"
        echo "[Init] Make sure environment variables are correct:"
        echo "[Init]   DB_HOST=$DB_HOST"
        echo "[Init]   DB_PORT=$DB_PORT"
    else
        # Check if tables exist
        echo "[Init] Checking database schema..."
        # Again, use a subshell or temporary variable to safely handle potential errors with set -e
        table_count=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" -se "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema='$DB_NAME';" 2>/dev/null || echo "0")

        if [ "$table_count" -eq "0" ]; then
            echo "[Init] Creating database schema..."
            # Load the schema
            if [ -f "/var/www/html/DATABASE_SCHEMA.sql" ]; then
                mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" -P "$DB_PORT" "$DB_NAME" < /var/www/html/DATABASE_SCHEMA.sql
                echo "[Init] ✅ Schema created successfully"
            else
                echo "[Init] ⚠️  DATABASE_SCHEMA.sql not found, skipping"
            fi
        else
            echo "[Init] ✅ Database schema already exists ($table_count tables)"
        fi
    fi
fi

echo "[Init] Starting Apache..."
# Use exec to replace the shell process with Apache
exec apache2-foreground
