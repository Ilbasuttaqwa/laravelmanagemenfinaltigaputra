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
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:YOUR_APP_KEY_WILL_BE_GENERATED_AUTOMATICALLY" ]; then
    echo "🔑 Generating application key..."
    APP_KEY=$(php artisan key:generate --show)
    echo "Generated APP_KEY: $APP_KEY"
    
    # Set APP_KEY in Railway if Railway CLI is available
    if command -v railway &> /dev/null && railway whoami &> /dev/null; then
        echo "🔧 Setting APP_KEY in Railway..."
        railway variables set APP_KEY="$APP_KEY"
    fi
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

# Test database connection (if MySQL variables are set)
if [ ! -z "$MYSQLHOST" ] && [ ! -z "$MYSQLUSER" ] && [ ! -z "$MYSQLPASSWORD" ]; then
    echo "🔗 Testing database connection..."
    
    # Wait for MySQL to be ready
    echo "⏳ Waiting for MySQL to be ready..."
    sleep 10
    
    # Test connection with retry
    for i in {1..5}; do
        if php artisan migrate:status > /dev/null 2>&1; then
            echo "✅ Database connection successful!"
            
            # Run migrations
            echo "🗄️ Running database migrations..."
            php artisan migrate --force
            
            # Seed database (only if tables are empty)
            echo "🌱 Seeding database..."
            php artisan db:seed --force
            break
        else
            echo "⏳ Attempt $i/5: Database not ready yet, waiting..."
            sleep 5
        fi
    done
    
    if [ $i -eq 5 ]; then
        echo "❌ Database connection failed after 5 attempts!"
        echo "MySQL service might be crashed. Please check Railway dashboard:"
        echo "1. Go to MySQL service → Logs"
        echo "2. Check for error messages"
        echo "3. Restart MySQL service if needed"
        echo "4. Ensure MySQL environment variables are set correctly"
        echo "Continuing with deployment..."
    fi
else
    echo "⚠️  MySQL environment variables not set!"
    echo "Please add MySQL service to your Railway project."
    echo "Continuing with deployment..."
fi

echo "✅ Build completed successfully!"
echo "🚂 Ready for Railway deployment!"

# Deploy to Railway
echo "🚂 Deploying to Railway..."
railway up

echo "🎉 Deployment completed!"
echo "🌐 Your app should be available at: https://tigaputra.railway.app"
