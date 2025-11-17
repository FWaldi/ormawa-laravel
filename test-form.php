<?php

// Test login form submission
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test login form submission with invalid credentials
$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => 'test@example.com',
    'password' => 'wrongpassword',
    '_token' => 'test-token' // This will fail CSRF but let's see the response
]);

try {
    $response = $kernel->handle($request);
    echo "Login form status: " . $response->getStatusCode() . "\n";

    if ($response->getStatusCode() == 419) {
        echo "✓ CSRF protection is working\n";
    } elseif ($response->getStatusCode() == 302) {
        echo "✓ Form processed (redirect)\n";
    } else {
        echo "? Form response: " . $response->getStatusCode() . "\n";
    }
} catch (Exception $e) {
    echo "Form submission test: " . $e->getMessage() . "\n";
}

$kernel->terminate($request, $response);