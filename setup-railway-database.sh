#!/bin/bash

# Script untuk setup database dan run migrations
# Jalankan setelah set environment variables

echo "🗄️ Setting up Laravel Database di Railway..."

# Check if logged in to Railway
if ! railway whoami &> /dev/null; then
    echo "❌ Please login to Railway first:"
    echo "railway login"
    exit 1
fi

echo "✅ Logged in to Railway"

# Test database connection
echo "🔗 Testing database connection..."
railway run php artisan migrate:status

if [ $? -eq 0 ]; then
    echo "✅ Database connection successful!"
    
    # Run migrations
    echo "🗄️ Running database migrations..."
    railway run php artisan migrate --force
    
    if [ $? -eq 0 ]; then
        echo "✅ Migrations completed successfully!"
        
        # Seed database
        echo "🌱 Seeding database..."
        railway run php artisan db:seed --force
        
        if [ $? -eq 0 ]; then
            echo "✅ Database seeded successfully!"
        else
            echo "⚠️  Database seeding failed, but migrations completed"
        fi
    else
        echo "❌ Migrations failed!"
        exit 1
    fi
else
    echo "❌ Database connection failed!"
    echo "Please check your database environment variables"
    exit 1
fi

echo ""
echo "🎉 Database setup completed!"
echo "🚀 Restarting Laravel service..."
railway service restart

echo ""
echo "✅ Setup completed! Your Laravel application should now be accessible at:"
echo "🌐 https://zealous-friendship-production.up.railway.app"
echo ""
echo "Default login credentials:"
echo "Manager: manager@example.com / password"
echo "Admin: admin@example.com / password"
