<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    /**
     * Test database configuration exists
     */
    public function test_database_configuration_exists(): void
    {
        // Check if .env file exists
        $this->assertFileExists(
            base_path('.env'),
            '.env file should exist for database configuration'
        );
    }

    /**
     * Test database connection parameters are configured
     */
    public function test_database_connection_parameters(): void
    {
        $env_file = base_path('.env');
        $env_content = file_get_contents($env_file);
        
        // Check if MySQL connection is configured
        $this->assertStringContainsString(
            'DB_CONNECTION=mysql',
            $env_content,
            'Database connection should be configured for MySQL'
        );
        
        // Check if database name is set
        $this->assertStringContainsString(
            'DB_DATABASE=ormawa_unp',
            $env_content,
            'Database name should be set to ormawa_unp'
        );
        
        // Check if host is configured
        $this->assertStringContainsString(
            'DB_HOST=127.0.0.1',
            $env_content,
            'Database host should be configured'
        );
    }

    /**
     * Test Laravel foundation structure
     */
    public function test_laravel_foundation_structure(): void
    {
        // Check essential directories
        $this->assertDirectoryExists(
            base_path('app'),
            'App directory should exist'
        );
        
        $this->assertDirectoryExists(
            base_path('config'),
            'Config directory should exist'
        );
        
        $this->assertDirectoryExists(
            base_path('routes'),
            'Routes directory should exist'
        );
        
        $this->assertDirectoryExists(
            base_path('database'),
            'Database directory should exist'
        );
        
        // Check essential files
        $this->assertFileExists(
            base_path('artisan'),
            'Artisan CLI tool should exist'
        );
        
        $this->assertFileExists(
            base_path('composer.json'),
            'Composer configuration should exist'
        );
    }

    /**
     * Test controllers are created
     */
    public function test_controllers_are_created(): void
    {
        $controllers = [
            'AuthController.php',
            'OrganizationController.php',
            'ActivityController.php',
            'AnnouncementController.php',
            'NewsController.php'
        ];
        
        foreach ($controllers as $controller) {
            $this->assertFileExists(
                base_path('app/Http/Controllers/' . $controller),
                "{$controller} should exist"
            );
        }
    }

    /**
     * Test middleware are created
     */
    public function test_middleware_are_created(): void
    {
        $middleware = [
            'AuthMiddleware.php',
            'AdminMiddleware.php'
        ];
        
        foreach ($middleware as $middleware_file) {
            $this->assertFileExists(
                base_path('app/Http/Middleware/' . $middleware_file),
                "{$middleware_file} should exist"
            );
        }
    }

    /**
     * Test routes are configured
     */
    public function test_routes_are_configured(): void
    {
        $routes_file = base_path('routes/web.php');
        $this->assertFileExists($routes_file, 'Web routes file should exist');
        
        $routes_content = file_get_contents($routes_file);
        
        // Check for essential routes
        $this->assertStringContainsString(
            'Route::get(\'/\'',
            $routes_content,
            'Root route should be defined'
        );
        
        $this->assertStringContainsString(
            'Route::get(\'/login\'',
            $routes_content,
            'Login route should be defined'
        );
        
        $this->assertStringContainsString(
            'AuthController::class',
            $routes_content,
            'Auth controller should be referenced in routes'
        );
    }
}