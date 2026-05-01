# Stage 1: Composer dependencies
FROM composer:2.8 AS composer-stage

# Stage 2: Node assets
FROM node:22-alpine AS node-stage
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 3: Final production image
FROM php:8.2-fpm-alpine

# Install system packages
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    postgresql-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        opcache \
        zip \
        intl

# Install Composer
COPY --from=composer-stage /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create necessary directories
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# Copy composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# Copy the rest of the application
COPY . .

# Copy frontend assets from node stage
COPY --from=node-stage /app/public/build /var/www/html/public/build

# ============================================
# PERMANENT FIX: Create .env file
# ============================================

# Create .env from .env.docker if it exists, otherwise create minimal .env
RUN if [ -f .env.docker ]; then \
        cp .env.docker .env; \
    elif [ -f .env.example ]; then \
        cp .env.example .env; \
    else \
        echo "APP_NAME=Laravel" > .env && \
        echo "APP_ENV=production" >> .env && \
        echo "APP_DEBUG=false" >> .env && \
        echo "APP_KEY=" >> .env && \
        echo "APP_URL=http://localhost" >> .env && \
        echo "DB_CONNECTION=sqlite" >> .env; \
    fi

# Generate application key (force overwrite if exists)
RUN php artisan key:generate --force

# ============================================
# End of permanent fix
# ============================================

# Run composer scripts after full copy
RUN composer run-script post-autoload-dump

# Cache configurations
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan event:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Create storage link
RUN php artisan storage:link

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and prepare entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]