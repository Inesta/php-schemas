# PHP Schema.org Library - Development Environment
FROM php:8.3-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    zip \
    unzip \
    make \
    bash \
    openssh-client \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-install \
    pcntl \
    && docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Copy application files
COPY . .

# Run composer autoload and scripts
RUN composer dump-autoload --optimize

# Create a non-root user
RUN addgroup -g 1000 developer && \
    adduser -u 1000 -G developer -s /bin/bash -D developer

# Change ownership of the working directory
RUN chown -R developer:developer /app

# Switch to the non-root user
USER developer

# Set default command
CMD ["bash"]