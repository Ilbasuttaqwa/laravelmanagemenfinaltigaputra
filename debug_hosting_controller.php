<?php
/**
 * Script untuk debug SalaryReportController di hosting
 * Script ini akan memeriksa apakah file hosting sudah menggunakan logika terbaru
 */

echo "=== DEBUGGING HOSTING SALARYREPORTCONTROLLER ===\n";

// Bootstrap Laravel
echo "1. Bootstrapping Laravel...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    echo "   ✓ Laravel bootstrapped successfully\n";
} catch (Exception $e) {
    echo "   ❌ Bootstrap error: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if SalaryReportController file exists
echo "\n2. Checking SalaryReportController file...\n";
$controllerPath = 'app/Http/Controllers/SalaryReportController.php';
if (file_exists($controllerPath)) {
    echo "   ✓ File exists: {$controllerPath}\n";
    
    // Read the file content
    $content = file_get_contents($controllerPath);
    
    // Check for hasFilter logic
    if (strpos($content, 'hasFilter') !== false) {
        echo "   ✓ hasFilter logic found in file\n";
    } else {
        echo "   ❌ hasFilter logic NOT found in file\n";
        echo "   ⚠ File hosting masih menggunakan logika lama!\n";
    }
    
    // Check for the specific logic
    if (strpos($content, 'if (!$hasFilter)') !== false) {
        echo "   ✓ Reset logic found in file\n";
    } else {
        echo "   ❌ Reset logic NOT found in file\n";
    }
    
    // Check for collect() logic
    if (strpos($content, 'collect()') !== false) {
        echo "   ✓ Empty collection logic found in file\n";
    } else {
        echo "   ❌ Empty collection logic NOT found in file\n";
    }
    
    // Show file size and last modified
    $fileSize = filesize($controllerPath);
    $lastModified = date('Y-m-d H:i:s', filemtime($controllerPath));
    echo "   File size: {$fileSize} bytes\n";
    echo "   Last modified: {$lastModified}\n";
    
} else {
    echo "   ❌ File not found: {$controllerPath}\n";
}

// Check if the controller class exists and can be instantiated
echo "\n3. Checking controller class...\n";
try {
    $controller = new \App\Http\Controllers\SalaryReportController();
    echo "   ✓ Controller class can be instantiated\n";
    
    // Check if the index method exists
    if (method_exists($controller, 'index')) {
        echo "   ✓ index method exists\n";
    } else {
        echo "   ❌ index method not found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error instantiating controller: " . $e->getMessage() . "\n";
}

// Test the actual logic by simulating a request
echo "\n4. Testing controller logic...\n";
try {
    // Create a mock request without any filters
    $request = new \Illuminate\Http\Request();
    
    // Get the controller instance
    $controller = new \App\Http\Controllers\SalaryReportController();
    
    // Use reflection to call the index method
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('index');
    $method->setAccessible(true);
    
    // Call the method
    $response = $method->invoke($controller, $request);
    
    // Check if it's a view response
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
        $reports = $data['reports'] ?? null;
        
        if ($reports instanceof \Illuminate\Support\Collection) {
            $count = $reports->count();
            echo "   ✓ Controller returns collection with {$count} items\n";
            
            if ($count === 0) {
                echo "   ✓ Reset logic working correctly (empty collection)\n";
            } else {
                echo "   ❌ Reset logic NOT working (collection has {$count} items)\n";
                echo "   ⚠ This confirms the hosting still uses old logic!\n";
            }
        } else {
            echo "   ⚠ Controller returns: " . gettype($reports) . "\n";
        }
    } else {
        echo "   ⚠ Controller returns: " . get_class($response) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing controller: " . $e->getMessage() . "\n";
}

// Check cache status
echo "\n5. Checking cache status...\n";
try {
    $configCache = 'bootstrap/cache/config.php';
    $routeCache = 'bootstrap/cache/routes-v7.php';
    
    if (file_exists($configCache)) {
        $configTime = date('Y-m-d H:i:s', filemtime($configCache));
        echo "   Config cache exists (modified: {$configTime})\n";
    } else {
        echo "   ✓ Config cache cleared\n";
    }
    
    if (file_exists($routeCache)) {
        $routeTime = date('Y-m-d H:i:s', filemtime($routeCache));
        echo "   Route cache exists (modified: {$routeTime})\n";
    } else {
        echo "   ✓ Route cache cleared\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠ Error checking cache: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
echo "If you see 'hasFilter logic NOT found' or 'Reset logic NOT working',\n";
echo "then the hosting file needs to be updated with the latest code.\n";
