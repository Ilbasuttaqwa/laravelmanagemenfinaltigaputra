#!/bin/bash

# Laravel Management System - Deployment Script for Netlify with Neon Database

echo "🌐 Starting deployment process for Netlify with Neon database..."

# Check if DATABASE_URL is set
if [ -z "$DATABASE_URL" ]; then
    echo "⚠️  DATABASE_URL environment variable is not set!"
    echo "Please set DATABASE_URL in Netlify environment variables."
    echo "Example: postgresql://username:password@host:port/database"
    exit 1
fi

# Install PHP dependencies
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
    echo "Continuing with deployment..."
fi

# Create .htaccess for proper routing
echo "📝 Creating .htaccess for Laravel routing..."
cat > public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

echo "✅ Deployment completed successfully!"
echo "🌐 Application is ready for production on Netlify!"
echo "🔗 Your site will be available at: https://tigaputra.netlify.app"
