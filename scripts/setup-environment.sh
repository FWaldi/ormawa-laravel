#!/bin/bash

# Environment Setup Script for cPanel

echo "Setting up environment for cPanel deployment..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "Creating .env file from template..."
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

# Generate application key
echo "Generating application key..."
php artisan key:generate --force

# Create storage directories
echo "Creating storage directories..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/testing
mkdir -p storage/logs
mkdir -p storage/app/uploads
mkdir -p storage/app/public

# Create cache table if using database cache
echo "Setting up cache infrastructure..."
php artisan cache:clear

# Create session table
echo "Setting up session infrastructure..."
php artisan session:table

# Create queue table
echo "Setting up queue infrastructure..."
php artisan queue:table

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Warm up cache
echo "Warming up cache..."
php artisan cache:warm --url=https://yourdomain.com

echo "Environment setup completed!"
echo ""
echo "IMPORTANT: Update the following in your .env file:"
echo "- APP_URL (set to your actual domain)"
echo "- DB_* (set to your database credentials)"
echo "- MAIL_* (set to your email configuration)"
echo "- SESSION_DOMAIN (set to your domain)"