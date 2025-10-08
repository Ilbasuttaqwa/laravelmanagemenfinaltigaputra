#!/bin/bash

echo "🚂 Setting up Laravel for Railway deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}Step 1: Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Composer dependencies installed!${NC}"
else
    echo -e "${RED}❌ Failed to install Composer dependencies.${NC}"
    exit 1
fi

echo -e "${BLUE}Step 2: Installing NPM dependencies...${NC}"
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ NPM dependencies installed!${NC}"
else
    echo -e "${RED}❌ Failed to install NPM dependencies.${NC}"
    exit 1
fi

echo -e "${BLUE}Step 3: Building assets...${NC}"
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Assets built successfully!${NC}"
else
    echo -e "${RED}❌ Failed to build assets.${NC}"
    exit 1
fi

echo -e "${BLUE}Step 4: Running database migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database migrations completed!${NC}"
else
    echo -e "${YELLOW}⚠️  Database migrations failed. This might be expected if database is not ready yet.${NC}"
fi

echo -e "${BLUE}Step 5: Seeding database...${NC}"
php artisan db:seed --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database seeded successfully!${NC}"
else
    echo -e "${YELLOW}⚠️  Database seeding failed. This might be expected if database is not ready yet.${NC}"
fi

echo -e "${BLUE}Step 6: Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✅ Configuration cached!${NC}"

echo -e "${GREEN}🎉 Railway setup completed!${NC}"
echo -e "${YELLOW}📋 Make sure to set these environment variables in Railway:${NC}"
echo -e "${YELLOW}   APP_KEY=base64:0TjCf2l05M8JK71JqyWys8Y/4NlALzCS3hQxwIIitHs=${NC}"
echo -e "${YELLOW}   APP_ENV=production${NC}"
echo -e "${YELLOW}   APP_DEBUG=false${NC}"
echo -e "${YELLOW}   APP_URL=https://your-app.railway.app${NC}"
echo -e "${YELLOW}   DB_CONNECTION=mysql${NC}"
echo -e "${YELLOW}   DB_HOST=\${{MySQL.HOST}}${NC}"
echo -e "${YELLOW}   DB_PORT=\${{MySQL.PORT}}${NC}"
echo -e "${YELLOW}   DB_DATABASE=\${{MySQL.DATABASE}}${NC}"
echo -e "${YELLOW}   DB_USERNAME=\${{MySQL.USER}}${NC}"
echo -e "${YELLOW}   DB_PASSWORD=\${{MySQL.PASSWORD}}${NC}"
