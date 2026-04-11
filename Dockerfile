FROM php:8.3-apache

# CRITICAL: Fix Apache MPM conflict (must be FIRST)
# Disable conflicting MPM modules and enable prefork (required for PHP)
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Install MySQL extensions and client
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN apt-get update && apt-get install -y default-mysql-client && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Create uploads directory
RUN mkdir -p /var/www/html/uploads && chown www-data:www-data /var/www/html/uploads

# Configure Apache DocumentRoot to point to public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV PORT=8080
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache to listen on PORT environment variable
COPY apache-port.conf /etc/apache2/conf-available/
RUN a2enconf apache-port

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
  CMD curl -f http://localhost:${PORT:-8080}/health.php || exit 1

# Expose port
EXPOSE 8080

# Start with entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
