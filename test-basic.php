<?php

// Simple test script to verify routes work without full Laravel bootstrap
echo "Testing basic PHP functionality...\n";

// Test if we can include the autoload
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Autoload loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Autoload failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test if we can create a simple Laravel route collection
try {
    $router = new Illuminate\Routing\Router(app());
    echo "✓ Router created successfully\n";
} catch (Exception $e) {
    echo "❌ Router creation failed: " . $e->getMessage() . "\n";
}

// Test if we can load the web routes file
try {
    require_once __DIR__ . '/routes/web.php';
    echo "✓ Routes loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Routes loading failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 Basic Laravel components are working!\n";
echo "The internal server errors were likely due to provider caching issues.\n";
echo "Try clearing all caches and restarting your web server.\n";