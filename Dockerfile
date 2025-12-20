FROM php:8.2-apache

# Install required system libraries for SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    pkg-config \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (optional, useful for clean URLs)
RUN a2enmod rewrite

# Copy your app into the container
COPY ./app /var/www/html

# Set working directory
WORKDIR /var/www/html