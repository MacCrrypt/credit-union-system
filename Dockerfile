FROM richarvey/nginx-php-fpm:latest

# Copy the application code
COPY . /var/www/html

# Set working directory
WORKDIR /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node.js
RUN apt-get update && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Cache Laravel configs
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Copy nginx configuration
COPY nginx.conf /etc/nginx/sites-enabled/default

# Expose port
EXPOSE 80