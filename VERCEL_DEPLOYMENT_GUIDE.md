# 🚀 Laravel Vercel Deployment Guide

## Prerequisites
- Laravel 10.x application
- Vercel account
- GitHub repository

## Step 1: Environment Variables

Set these environment variables in your Vercel dashboard:

### Required Variables:
```
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-generated-key-here
APP_URL=https://your-app.vercel.app
DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Optional Variables:
```
LOG_LEVEL=error
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
```

## Step 2: Generate APP_KEY

Run this command locally to generate your APP_KEY:
```bash
php artisan key:generate --show
```

Copy the generated key and set it as APP_KEY in Vercel environment variables.

## Step 3: Database Setup

For production, consider using:
- **PlanetScale** (MySQL)
- **Supabase** (PostgreSQL)
- **Neon** (PostgreSQL)

Update your database configuration in Vercel environment variables accordingly.

## Step 4: Deploy to Vercel

1. Connect your GitHub repository to Vercel
2. Vercel will automatically detect the `vercel.json` configuration
3. The deployment will use the configured build process

## Step 5: Post-Deployment

After successful deployment:
1. Run database migrations (if using external database)
2. Set up your domain (if needed)
3. Configure any additional services

## Troubleshooting

### Common Issues:

1. **"No Output Directory named 'dist' found"**
   - ✅ Fixed: `outputDirectory` is set to `public` in `vercel.json`

2. **"composer: command not found"**
   - ✅ Fixed: Using `vercel-php@0.6.0` builder

3. **"APP_KEY not set"**
   - ✅ Fixed: Set APP_KEY in Vercel environment variables

4. **Database connection issues**
   - ✅ Fixed: Using SQLite for development, external DB for production

## File Structure

```
├── vercel.json          # Vercel configuration
├── vercel.php           # Entry point for Vercel
├── package.json         # NPM dependencies and scripts
├── vite.config.js       # Vite build configuration
├── .vercelignore        # Files to ignore during deployment
├── env.example          # Environment variables template
├── setup-vercel.sh      # Setup script
└── database/
    └── database.sqlite  # SQLite database file
```

## Support

If you encounter issues:
1. Check Vercel deployment logs
2. Verify environment variables are set correctly
3. Ensure all required files are present
4. Check Laravel logs in Vercel function logs
