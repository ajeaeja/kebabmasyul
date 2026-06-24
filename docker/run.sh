#!/bin/sh

# Optimization: Cache config, routes, and views for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Execute migrations automatically
php artisan migrate --force

# Start supervisor (which manages Nginx and PHP-FPM)
exec /usr/bin/supervisord -c /etc/supervisord.conf
