#!/bin/sh

# Navigate to the working directory
cd /var/www/html

# Run composer install if vendor directory is not found
if [ ! -d "vendor" ]; then
  echo "Running composer install..."
  composer install --no-dev --optimize-autoloader
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
