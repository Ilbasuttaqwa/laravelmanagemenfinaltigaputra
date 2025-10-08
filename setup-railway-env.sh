#!/bin/bash

# Setup Railway Environment Variables - Automatic Deployment Configuration

echo "🚂 Setting up Railway environment variables for automatic deployment..."

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

echo "🔧 Setting up environment variables..."

# Set Laravel Application Variables
railway variables set APP_NAME="Laravel Management System"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://zealous-friendship-production.up.railway.app
railway variables set PORT=8080

# Set Session and Cache Configuration
railway variables set SESSION_DRIVER=file
railway variables set CACHE_DRIVER=file
railway variables set LOG_LEVEL=error

# Set Database Connection Variables (will be linked to MySQL service)
railway variables set DB_CONNECTION=mysql

echo "🔑 Generating APP_KEY..."

# Generate APP_KEY
APP_KEY=$(railway run php artisan key:generate --show)
echo "Generated APP_KEY: $APP_KEY"

# Set APP_KEY in Railway
railway variables set APP_KEY="$APP_KEY"

echo "🗄️ Setting up database connection..."

# Test database connection
echo "🔗 Testing database connection..."
if railway run php artisan migrate:status > /dev/null 2>&1; then
    echo "✅ Database connection successful!"
    
    # Run migrations
    echo "🗄️ Running database migrations..."
    railway run php artisan migrate --force
    
    # Seed database
    echo "🌱 Seeding database..."
    railway run php artisan db:seed --force
    
    echo "✅ Database setup completed!"
else
    echo "❌ Database connection failed!"
    echo "Please ensure MySQL service is running and environment variables are set correctly."
    echo "MySQL service variables should be automatically linked."
fi

echo "🔄 Restarting Laravel service..."

# Restart Laravel service to apply new variables
railway service restart

echo "✅ Environment setup completed!"
echo ""
echo "📋 Environment variables set:"
echo "- APP_NAME: Laravel Management System"
echo "- APP_ENV: production"
echo "- APP_DEBUG: false"
echo "- APP_URL: https://zealous-friendship-production.up.railway.app"
echo "- APP_KEY: Generated automatically"
echo "- PORT: 8080"
echo "- Database: Connected to MySQL service"
echo ""
echo "🌐 Your application should now be accessible at:"
echo "https://zealous-friendship-production.up.railway.app"
echo ""
echo "🎉 Setup completed! Next deployment will use these configurations automatically."
