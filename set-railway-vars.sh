#!/bin/bash

# Script untuk set Laravel Environment Variables di Railway
# Jalankan setelah: railway login

echo "🚂 Setting Laravel Environment Variables di Railway..."

# Check if logged in to Railway
if ! railway whoami &> /dev/null; then
    echo "❌ Please login to Railway first:"
    echo "railway login"
    exit 1
fi

echo "✅ Logged in to Railway"

# Set Application Configuration
echo "🔧 Setting Application Configuration..."
railway variables set APP_NAME="Laravel Management System"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://zealous-friendship-production.up.railway.app

# Set Database Configuration
echo "🗄️ Setting Database Configuration..."
railway variables set DB_CONNECTION=mysql
railway variables set DB_HOST=mysql.railway.internal
railway variables set DB_PORT=3306
railway variables set DB_DATABASE=railway
railway variables set DB_USERNAME=root
railway variables set DB_PASSWORD=qhtQVisSzaRiUtbNsGSczxiikubsyrAj

# Set Port Configuration
echo "🔌 Setting Port Configuration..."
railway variables set PORT=8080

# Set Session & Cache Configuration
echo "💾 Setting Session & Cache Configuration..."
railway variables set SESSION_DRIVER=file
railway variables set CACHE_DRIVER=file

# Generate and set APP_KEY
echo "🔑 Generating APP_KEY..."
APP_KEY=$(railway run php artisan key:generate --show 2>/dev/null | grep "base64:" | awk '{print $2}')
if [ ! -z "$APP_KEY" ]; then
    railway variables set APP_KEY="$APP_KEY"
    echo "✅ APP_KEY set: $APP_KEY"
else
    echo "⚠️  Could not generate APP_KEY automatically"
    echo "Please run manually: railway run php artisan key:generate --show"
fi

echo "✅ All environment variables set successfully!"
echo ""
echo "🚀 Next steps:"
echo "1. Run database migrations: railway run php artisan migrate --force"
echo "2. Seed database: railway run php artisan db:seed --force"
echo "3. Restart service: railway service restart"
echo "4. Test application: https://zealous-friendship-production.up.railway.app"
