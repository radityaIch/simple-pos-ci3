#!/bin/bash

# Docker startup script for Simple POS CI3

echo "Starting Simple POS CI3 application..."

# Wait for database to be ready
echo "Waiting for database connection..."
while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    echo "Waiting for database..."
    sleep 2
done

echo "Database is ready!"

# Set proper permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/application/cache
chmod -R 777 /var/www/html/application/logs

# Create and set permissions for session directory
mkdir -p /tmp/ci_sessions
chown www-data:www-data /tmp/ci_sessions
chmod 777 /tmp/ci_sessions

echo "Permissions set successfully!"

# Start Apache
echo "Starting Apache server..."
exec apache2-foreground