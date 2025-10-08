#!/bin/bash

# Complete Railway Setup Script
# Jalankan script ini untuk setup lengkap Laravel di Railway

echo "🚂 Complete Railway Setup untuk Laravel Management System"
echo "=================================================="

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "📦 Installing Railway CLI..."
    npm install -g @railway/cli
fi

echo "✅ Railway CLI ready"

# Check if logged in
if ! railway whoami &> /dev/null; then
    echo "🔑 Please login to Railway:"
    echo "railway login"
    echo ""
    echo "After login, run this script again"
    exit 1
fi

echo "✅ Logged in to Railway"

# Set environment variables
echo ""
echo "🔧 Setting Environment Variables..."
railway variables set APP_NAME="Laravel Management System"
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false
railway variables set APP_URL=https://zealous-friendship-production.up.railway.app
railway variables set DB_CONNECTION=mysql
railway variables set DB_HOST=mysql.railway.internal
railway variables set DB_PORT=3306
railway variables set DB_DATABASE=railway
railway variables set DB_USERNAME=root
railway variables set DB_PASSWORD=qhtQVisSzaRiUtbNsGSczxiikubsyrAj
railway variables set PORT=8080
railway variables set SESSION_DRIVER=file
railway variables set CACHE_DRIVER=file

echo "✅ Environment variables set"

# Generate APP_KEY
echo ""
echo "🔑 Generating APP_KEY..."
APP_KEY=$(railway run php artisan key:generate --show 2>/dev/null | grep "base64:" | awk '{print $2}')
if [ ! -z "$APP_KEY" ]; then
    railway variables set APP_KEY="$APP_KEY"
    echo "✅ APP_KEY generated and set"
else
    echo "⚠️  Could not generate APP_KEY automatically"
fi

# Setup database
echo ""
echo "🗄️ Setting up database..."
railway run php artisan migrate --force
railway run php artisan db:seed --force

echo "✅ Database setup completed"

# Restart service
echo ""
echo "🚀 Restarting service..."
railway service restart

echo ""
echo "🎉 SETUP COMPLETED!"
echo "=================================================="
echo "🌐 Your Laravel application is now accessible at:"
echo "   https://zealous-friendship-production.up.railway.app"
echo ""
echo "👤 Default login credentials:"
echo "   Manager: manager@example.com / password"
echo "   Admin: admin@example.com / password"
echo ""
echo "✅ All features should now work:"
echo "   - Employee Management"
echo "   - Attendance System"
echo "   - Calendar Attendance"
echo "   - Monthly Reports"
echo "   - Salary Reports"
echo "=================================================="
