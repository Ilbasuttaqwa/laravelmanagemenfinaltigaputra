#!/bin/bash

# Generate APP_KEY for Laravel application

echo "🔑 Generating Laravel APP_KEY..."

# Generate new application key
php artisan key:generate --show

echo ""
echo "✅ APP_KEY generated successfully!"
echo "📋 Copy the key above and paste it to Netlify environment variables as APP_KEY"
echo ""
echo "🌐 Next steps:"
echo "1. Copy the generated key"
echo "2. Go to Netlify Dashboard → Site Settings → Environment Variables"
echo "3. Add APP_KEY variable with the generated value"
echo "4. Redeploy your site"
