<?php

// Test login page loading
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test login page
$request = Illuminate\Http\Request::create('/login', 'GET');
$response = $kernel->handle($request);

echo "Login page status: " . $response->getStatusCode() . "\n";
$content = $response->getContent();

// Check if it contains expected elements
if (strpos($content, 'Masuk ke Akun') !== false) {
    echo "✓ Login page contains expected content\n";
} else {
    echo "✗ Login page missing expected content\n";
}

if (strpos($content, 'tailwindcss.com') !== false) {
    echo "✓ Login page includes Tailwind CSS\n";
} else {
    echo "✗ Login page missing Tailwind CSS\n";
}

$kernel->terminate($request, $response);