#!/bin/bash

# cPanel File Permissions Setup Script

echo "Setting up cPanel file permissions..."

# Set proper ownership (replace username with actual cPanel username)
USERNAME="your_cpanel_username"
chown -R $USERNAME:$USERNAME .

# Set directory permissions
echo "Setting directory permissions..."
find . -type d -exec chmod 755 {} \;

# Set file permissions
echo "Setting file permissions..."
find . -type f -exec chmod 644 {} \;

# Special permissions for storage directory
echo "Setting storage directory permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Special permissions for public directory
echo "Setting public directory permissions..."
chmod -R 755 public/

# Make artisan executable
echo "Setting artisan permissions..."
chmod 755 artisan

# Secure sensitive files
echo "Securing sensitive files..."
chmod 600 .env
chmod 600 config/database.php
chmod 600 config/mail.php
chmod 600 config/services.php

# Set proper permissions for cache directories
echo "Setting cache permissions..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs

chmod -R 755 storage/framework/
chmod -R 755 storage/logs/

# Clear and rebuild caches
echo "Clearing and rebuilding caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "File permissions setup completed!"
echo "IMPORTANT: Replace 'your_cpanel_username' with your actual cPanel username"