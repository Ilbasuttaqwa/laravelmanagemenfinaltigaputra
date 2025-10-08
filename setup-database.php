<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    
    // Run migrations
    echo "Running migrations...\n";
    Artisan::call('migrate', ['--force' => true]);
    
    // Seed database
    echo "Seeding database...\n";
    Artisan::call('db:seed', ['--force' => true]);
    
    echo "Database setup completed successfully!\n";
    
    // Test login credentials
    echo "\nLogin credentials:\n";
    echo "Admin: admin@tigaputra.com / password\n";
    echo "Manager: manager@tigaputra.com / password\n";
    echo "Test: test@test.com / 123456\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database configuration.\n";
}
