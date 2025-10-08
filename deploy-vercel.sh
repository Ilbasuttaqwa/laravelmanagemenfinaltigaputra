#!/bin/bash

# Laravel Management System - Vercel Deployment Script

echo "🚀 Starting Vercel deployment process..."

# Check if Vercel CLI is installed
if ! command -v vercel &> /dev/null; then
    echo "❌ Vercel CLI is not installed!"
    echo "Please install it with: npm i -g vercel"
    exit 1
fi

# Check if environment variables are set
if [ -z "$DATABASE_URL" ]; then
    echo "⚠️  DATABASE_URL environment variable is not set!"
    echo "Please set your Neon database connection string in Vercel environment variables."
    echo "Continuing with deployment..."
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Install Node dependencies
echo "📦 Installing Node dependencies..."
echo "🔍 Node version: $(node --version)"
echo "🔍 NPM version: $(npm --version)"

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

# Test database connection (if DATABASE_URL is set)
if [ ! -z "$DATABASE_URL" ]; then
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
        echo "Please check your DATABASE_URL in Vercel environment variables."
        echo "Continuing with deployment..."
    fi
fi

echo "✅ Build completed successfully!"
echo "🌐 Ready for Vercel deployment!"

# Deploy to Vercel
echo "🚀 Deploying to Vercel..."
vercel --prod

echo "🎉 Deployment completed!"
