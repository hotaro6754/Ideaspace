FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libmariadb-dev-compat \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli

WORKDIR /app

COPY . .

# Use shell form for CMD to allow environment variable expansion
CMD php -S 0.0.0.0:${PORT:-8080} -t public public/router.php
