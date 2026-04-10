#!/bin/bash
# Startup script for Railway PHP deployment

PORT=${PORT:-8000}

# Change to public directory
cd "$(dirname "$0")/public" || exit 1

# Start PHP built-in server with proper routing
php -S 0.0.0.0:$PORT
