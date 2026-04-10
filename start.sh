#!/bin/bash
# Startup script for Railway PHP deployment

PORT=${PORT:-8080}

# Start PHP built-in server with proper routing from the root
# The router script is in public/router.php
php -S 0.0.0.0:$PORT -t public public/router.php
