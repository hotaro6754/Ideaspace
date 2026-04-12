FROM php:8.3-cli

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install MySQL client
RUN apt-get update && apt-get install -y default-mysql-client curl && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Create logs and uploads directories
RUN mkdir -p /app/logs /app/uploads && chmod 777 /app/logs /app/uploads

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=10s --timeout=3s --start-period=5s --retries=3 \
  CMD curl -f http://127.0.0.1:8080/public/health.php || exit 1

# Start PHP built-in server
CMD php -S 0.0.0.0:${PORT:-8080} -t public public/router.php
