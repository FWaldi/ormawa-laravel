<?php

echo "=== PHP/LARAVEL DIAGNOSTICS ===\n\n";

// Check PHP version
echo "PHP Version: " . PHP_VERSION . "\n";

// Check required extensions
$required_extensions = ['mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'cURL', 'fileinfo'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (empty($missing_extensions)) {
    echo "✅ All required PHP extensions are loaded\n";
} else {
    echo "❌ Missing PHP extensions: " . implode(', ', $missing_extensions) . "\n";
}

// Check if composer dependencies are installed
echo "\n🔍 Checking Composer dependencies...\n";
$composerPath = __DIR__ . '/composer.json';
if (file_exists($composerPath)) {
    $composer = json_decode(file_get_contents($composerPath), true);
    $require = $composer['require'] ?? [];
    
    $vendorPath = __DIR__ . '/vendor';
    if (!is_dir($vendorPath)) {
        echo "❌ Vendor directory not found - run 'composer install'\n";
        exit(1);
    }
    
    echo "✅ Composer dependencies appear to be installed\n";
    
    // Check key dependencies
    $key_deps = ['laravel/framework', 'laravel/sanctum'];
    foreach ($key_deps as $dep) {
        if (isset($require[$dep])) {
            echo "✅ {$dep}: " . $require[$dep] . "\n";
        } else {
            echo "❌ {$dep}: Not found\n";
        }
    }
} else {
    echo "❌ composer.json not found\n";
}

// Try to bootstrap Laravel
echo "\n🔍 Testing Laravel bootstrap...\n";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel bootstrap successful\n";
    
    // Check if kernel can be resolved
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "✅ HTTP Kernel resolved\n";
    
    // Test a simple request
    $request = Illuminate\Http\Request::create('/');
    echo "✅ Request object created\n";
    
    // Test view resolution
    try {
        $view = $app['view']->make('home');
        echo "✅ View 'home' resolved\n";
    } catch (Exception $e) {
        echo "❌ View resolution failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

// Check file permissions
echo "\n🔍 Checking file permissions...\n";
$paths_to_check = [
    'storage' => __DIR__ . '/storage',
    'bootstrap/cache' => __DIR__ . '/bootstrap/cache',
    'resources/views' => __DIR__ . '/resources/views',
];

foreach ($paths_to_check as $name => $path) {
    if (file_exists($path)) {
        if (is_writable($path)) {
            echo "✅ {$name} is writable\n";
        } else {
            echo "❌ {$name} is not writable\n";
        }
    } else {
        if ($name === 'bootstrap/cache') {
            echo "⚠️  {$name} doesn't exist (will be created)\n";
        } else {
            echo "❌ {$name} doesn't exist\n";
        }
    }
}

// Check .env file
echo "\n🔍 Checking environment...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "✅ .env file exists\n";
    
    $envContent = file_get_contents($envFile);
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "✅ APP_KEY is set\n";
    } else {
        echo "❌ APP_KEY is missing\n";
    }
    
    if (strpos($envContent, 'APP_DEBUG=true') !== false) {
        echo "✅ Debug mode is ON\n";
    } else {
        echo "⚠️  Debug mode is OFF\n";
    }
} else {
    echo "❌ .env file missing\n";
}

echo "\n=== DIAGNOSTICS COMPLETE ===\n";
echo "\n📋 RECOMMENDATIONS:\n";
echo "1. If any PHP extensions are missing, install them\n";
echo "2. If vendor directory is missing, run 'composer install'\n";
echo "3. If permissions are wrong, fix them with chmod/chown\n";
echo "4. Try running: php artisan serve --host=127.0.0.1 --port=8080\n";
echo "5. Check if any firewall is blocking the port\n";
echo "6. Try accessing the server immediately after starting it\n";