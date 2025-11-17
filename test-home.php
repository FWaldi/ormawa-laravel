<?php

// Test home page loading
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test home page
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "Home page status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();

// Check if it contains expected elements
if (strpos($content, 'Selamat Datang') !== false) {
    echo "✓ Home page contains expected content\n";
} else {
    echo "✗ Home page missing expected content\n";
}

if (strpos($content, 'build/assets/app-') !== false) {
    echo "✓ Home page includes built assets\n";
} else {
    echo "✗ Home page missing built assets\n";
}

$kernel->terminate($request, $response);