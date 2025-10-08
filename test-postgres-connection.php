<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "🐘 Testing PostgreSQL Connection to Railway...\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $connection = DB::connection();
    $pdo = $connection->getPdo();
    echo "✅ Database connection successful!\n";
    
    // Get connection info
    $driver = $connection->getDriverName();
    $database = $connection->getDatabaseName();
    echo "   Driver: $driver\n";
    echo "   Database: $database\n\n";
    
    // Test basic query
    echo "2. Testing basic query...\n";
    $result = DB::select('SELECT version() as version');
    $version = $result[0]->version;
    echo "✅ Query successful!\n";
    echo "   PostgreSQL Version: $version\n\n";
    
    // Test if migrations table exists
    echo "3. Checking migrations table...\n";
    try {
        $migrations = DB::select("SELECT COUNT(*) as count FROM migrations");
        echo "✅ Migrations table exists!\n";
        echo "   Migration count: " . $migrations[0]->count . "\n\n";
    } catch (Exception $e) {
        echo "⚠️  Migrations table doesn't exist yet (this is normal for fresh setup)\n\n";
    }
    
    // Test if users table exists
    echo "4. Checking users table...\n";
    try {
        $users = DB::select("SELECT COUNT(*) as count FROM users");
        echo "✅ Users table exists!\n";
        echo "   User count: " . $users[0]->count . "\n\n";
    } catch (Exception $e) {
        echo "⚠️  Users table doesn't exist yet (run migrations first)\n\n";
    }
    
    echo "🎉 PostgreSQL connection test completed successfully!\n";
    echo "\n📋 Next steps:\n";
    echo "1. Run migrations: php artisan migrate\n";
    echo "2. Seed database: php artisan db:seed\n";
    echo "3. Test login functionality\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "🔧 Troubleshooting:\n";
    echo "1. Check environment variables in Railway\n";
    echo "2. Verify PostgreSQL service is running\n";
    echo "3. Check network connectivity\n";
    echo "4. Verify database credentials\n";
}
