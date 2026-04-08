# syntax=docker/dockerfile:1
FROM php:8.2-apache

# Install system packages and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
       libzip-dev zlib1g-dev libicu-dev \
       imagemagick libmagickwand-dev \
       libpq-dev \
       curl ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd zip intl mysqli opcache pgsql pdo pdo_pgsql

# Enable Apache mods commonly needed
RUN a2enmod rewrite headers include

# Install Imagick from PECL
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Copy application code
WORKDIR /var/www/html
COPY . /var/www/html

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Healthcheck (optional)
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
 CMD curl -fsS http://localhost/ || exit 1

# Apache listens on 80
EXPOSE 80
