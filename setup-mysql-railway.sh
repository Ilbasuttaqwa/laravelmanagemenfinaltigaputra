#!/bin/bash

# Setup MySQL for Railway - Fix MySQL crash issue

echo "🚂 Setting up MySQL for Railway..."

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

echo "🔧 Setting MySQL environment variables..."

# Set MySQL environment variables
railway variables set MYSQL_ROOT_PASSWORD=railway_password_123
railway variables set MYSQL_DATABASE=managemen
railway variables set MYSQL_USER=railway
railway variables set MYSQL_PASSWORD=railway_password_123

# Set Laravel database variables
railway variables set DB_CONNECTION=mysql
railway variables set APP_ENV=production
railway variables set APP_DEBUG=false

echo "✅ MySQL environment variables set!"
echo "🔄 Restarting MySQL service..."

# Restart MySQL service
railway service restart mysql

echo "⏳ Waiting for MySQL to start..."
sleep 15

echo "🔍 Checking MySQL service status..."
railway service status

echo "📋 MySQL setup completed!"
echo "🌐 Your MySQL service should now be running without crashes."
echo ""
echo "Next steps:"
echo "1. Check Railway dashboard → MySQL service → Logs"
echo "2. Ensure service status is 'Running'"
echo "3. Deploy your Laravel application"
