#!/bin/bash

# Laravel Management System - Railway Deployment Script

echo "🚂 Starting Railway deployment process..."

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "❌ Railway CLI is not installed!"
    echo "Please install it with: npm install -g @railway/cli"
    exit 1
fi

# Check if logged in to Railway
if ! railway whoami &> /dev/null; then
    echo "🔑 Please login to Railway first:"
    railway login
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

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
        echo "Please check your DATABASE_URL in Railway environment variables."
        echo "Continuing with deployment..."
    fi
fi

echo "✅ Build completed successfully!"
echo "🚂 Ready for Railway deployment!"

# Deploy to Railway
echo "🚂 Deploying to Railway..."
railway up

echo "🎉 Deployment completed!"
echo "🌐 Your app should be available at: https://tigaputra.railway.app"
