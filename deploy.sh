#!/bin/bash

# Laravel Management System - Deployment Script

echo "🚀 Starting deployment process..."

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm install

# Generate application key if not exists
echo "🔑 Generating application key..."
php artisan key:generate

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache

# Cache routes
echo "🛣️ Caching routes..."
php artisan route:cache

# Cache views
echo "👁️ Caching views..."
php artisan view:cache

# Build assets
echo "🎨 Building assets..."
npm run build

# Run migrations (if database is configured)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database (if needed)
echo "🌱 Seeding database..."
php artisan db:seed --force

echo "✅ Deployment completed successfully!"
echo "🌐 Application is ready for production!"
