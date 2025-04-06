FROM php:fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libmcrypt-dev \
    libssl-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory (optional)
WORKDIR /app
