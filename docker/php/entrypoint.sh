#!/bin/sh

# Navigate to the working directory
cd /var/www/html

chmod -R 777 /var/www/html/logs

# Run composer install if vendor directory is not found
if [ ! -d "vendor" ]; then
  echo "Running composer install..."
  composer install --no-dev --optimize-autoloader
fi

if [ ! -f "data/chat.sqlite" ]; then
  echo "Add sqlite db"
  touch data/chat.sqlite
  chown www-data:www-data data -R
  chmod 777 data -R
  vendor/bin/doctrine-migrations migrate
fi

# Start PHP-FPM
echo "Starting PHP-FPM..."
exec php-fpm
