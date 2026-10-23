
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Apache
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!/var/www/html/public!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Laravel permissions
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Apache listens on Render's PORT
CMD ["bash", "-c", "sed -i 's/Listen 80/Listen ${PORT:-10000}/' /etc/apache2/ports.conf && sed -i 's/:80>/:${PORT:-10000}>/' /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
