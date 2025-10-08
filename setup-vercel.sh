#!/bin/bash

# Setup script for Vercel deployment
echo "🚀 Setting up Laravel for Vercel deployment..."

# Create .env file from example
if [ ! -f .env ]; then
    cp env.example .env
    echo "✅ Created .env file from example"
fi

# Generate APP_KEY
php artisan key:generate --force

# Create database directory and file
mkdir -p database
touch database/database.sqlite

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Install npm dependencies and build
npm install
npm run build

echo "✅ Laravel setup completed for Vercel!"
echo "📝 Don't forget to set environment variables in Vercel dashboard:"
echo "   - APP_KEY"
echo "   - APP_URL"
echo "   - Database configuration"
