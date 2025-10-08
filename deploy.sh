#!/bin/bash

# Laravel Management System - Deployment Script for Netlify with Neon Database

echo "🚀 Starting deployment process for Netlify..."

# Check if environment variables are set
if [ -z "$DATABASE_URL" ]; then
    echo "❌ DATABASE_URL environment variable is not set!"
    echo "Please set your Neon database connection string in Netlify environment variables."
    exit 1
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm install

# Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate
fi

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

# Test database connection
echo "🔗 Testing database connection..."
if php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Database connection successful!"
    
    # Run migrations
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
    
    # Seed database (only if tables are empty)
    echo "🌱 Seeding database..."
    php artisan db:seed --force
else
    echo "❌ Database connection failed!"
    echo "Please check your DATABASE_URL in Netlify environment variables."
    exit 1
fi

echo "✅ Deployment completed successfully!"
echo "🌐 Application is ready for production on Netlify!"
