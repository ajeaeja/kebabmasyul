# Stage 1: Build assets
FROM node:20-alpine AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production PHP-FPM & Nginx
FROM php:8.4-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip opcache

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy built assets from builder stage
COPY --from=asset-builder /app/public/build ./public/build

# Run composer install
RUN git config --global --add safe.directory /var/www/html && \
    composer config --global process-timeout 2000 && \
    (composer install --no-dev --optimize-autoloader --no-interaction || \
     composer install --no-dev --optimize-autoloader --no-interaction || \
     composer install --no-dev --optimize-autoloader --no-interaction)

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html

# Copy Nginx and Supervisor configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Copy entrypoint script
COPY docker/run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

# Expose port 80 (Render will redirect external traffic here)
EXPOSE 80

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/run.sh"]
