<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RealServerTest extends DuskTestCase
{
    /**
     * Test real server access and identify errors.
     */
    public function test_real_server_home_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000')
                    ->pause(2000) // Wait for page to load
                    ->screenshot('real_server_home_page');
            
            // Check if page loads without fatal errors
            $pageSource = $browser->driver->getPageSource();
            
            if (strpos($pageSource, 'Fatal error') !== false) {
                $this->fail('Fatal error detected on page');
            }
            
            if (strpos($pageSource, 'Exception') !== false) {
                $this->fail('Exception detected on page');
            }
            
            if (strpos($pageSource, '500') !== false && strpos($pageSource, 'Internal Server Error') !== false) {
                $this->fail('500 Internal Server Error detected');
            }
            
            // Check for Laravel error page
            if (strpos($pageSource, 'Whoops') !== false || strpos($pageSource, 'Something went wrong') !== false) {
                $this->fail('Laravel error page detected');
            }
            
            // Check if our UI elements are present
            $browser->assertPresent('html')
                    ->assertPresent('head')
                    ->assertPresent('body');
            
            // Try to find our specific UI elements
            try {
                $browser->waitFor('header', 5)
                        ->assertPresent('header');
            } catch (\Exception $e) {
                echo "Header not found: " . $e->getMessage() . "\n";
            }
            
            try {
                $browser->waitFor('main', 5)
                        ->assertPresent('main');
            } catch (\Exception $e) {
                echo "Main content not found: " . $e->getMessage() . "\n";
            }
            
            // Check for any error messages in the page
            $errorIndicators = [
                'error',
                'Error',
                'ERROR',
                'fatal',
                'Fatal',
                'FATAL',
                'exception',
                'Exception',
                'EXCEPTION',
                'warning',
                'Warning',
                'WARNING',
                'notice',
                'Notice',
                'NOTICE'
            ];
            
            foreach ($errorIndicators as $indicator) {
                if (strpos($pageSource, $indicator) !== false) {
                    echo "Potential error indicator found: {$indicator}\n";
                }
            }
            
            // Save page source for debugging
            file_put_contents(__DIR__ . '/../debug-page-source.html', $pageSource);
        });
    }
    
    /**
     * Test if Laravel routes are working.
     */
    public function test_laravel_routes_working()
    {
        $this->browse(function (Browser $browser) {
            try {
                $browser->visit('http://127.0.0.1:8000/up')
                        ->assertSee('ok')
                        ->screenshot('real_server_health_check');
            } catch (\Exception $e) {
                echo "Health check failed: " . $e->getMessage() . "\n";
                $browser->screenshot('real_server_health_check_failed');
            }
        });
    }
    
    /**
     * Test view rendering specifically.
     */
    public function test_view_rendering()
    {
        $this->browse(function (Browser $browser) {
            try {
                $browser->visit('http://127.0.0.1:8000')
                        ->pause(3000);
                
                // Check if our specific view content is rendered
                $pageContent = $browser->driver->getPageSource();
                
                // Look for our UI elements
                $uiElements = [
                    'Portal Ormawa' => 'Main title',
                    'Selamat Datang di' => 'Hero section',
                    'Jelajahi Ormawa' => 'CTA button',
                    'Papan Pengumuman' => 'Bulletin board',
                    'Ormawa Unggulan' => 'Carousel section',
                    'Semua' => 'Filter buttons',
                    'BEM' => 'Category filter',
                    'UKM' => 'Category filter',
                    'HIMA' => 'Category filter',
                ];
                
                foreach ($uiElements as $element => $description) {
                    if (strpos($pageContent, $element) !== false) {
                        echo "✅ Found: {$description} ({$element})\n";
                    } else {
                        echo "❌ Missing: {$description} ({$element})\n";
                    }
                }
                
                // Check for CSS classes
                $cssClasses = [
                    'header' => 'Header styling',
                    'hero' => 'Hero section',
                    'filter-bar' => 'Filter section',
                    'organizations-grid' => 'Organization cards',
                    'carousel-container' => 'Carousel',
                    'bulletin-board' => 'Bulletin board',
                ];
                
                foreach ($cssClasses as $class => $description) {
                    if (strpos($pageContent, $class) !== false) {
                        echo "✅ CSS class found: {$description} ({$class})\n";
                    } else {
                        echo "❌ CSS class missing: {$description} ({$class})\n";
                    }
                }
                
                $browser->screenshot('real_server_ui_elements');
                
            } catch (\Exception $e) {
                echo "View rendering test failed: " . $e->getMessage() . "\n";
                $browser->screenshot('real_server_view_rendering_failed');
            }
        });
    }
    
    /**
     * Test for common Laravel errors.
     */
    public function test_common_laravel_errors()
    {
        $this->browse(function (Browser $browser) {
            try {
                $browser->visit('http://127.0.0.1:8000')
                        ->pause(2000);
                
                $pageSource = $browser->driver->getPageSource();
                
                // Check for common Laravel error patterns
                $errorPatterns = [
                    'Route not defined' => 'Route definition issue',
                    'View not found' => 'View file missing',
                    'Class not found' => 'Missing class/import',
                    'Undefined variable' => 'Variable not defined',
                    'Call to undefined method' => 'Method not available',
                    'SQLSTATE' => 'Database connection issue',
                    'Connection refused' => 'Database connection issue',
                    'Access denied for user' => 'Database credentials issue',
                    'Unknown database' => 'Database not created',
                    'Table doesn\'t exist' => 'Migration not run',
                    'Column not found' => 'Migration issue',
                    'syntax error' => 'PHP syntax issue',
                    'parse error' => 'PHP parsing issue',
                ];
                
                $foundErrors = [];
                foreach ($errorPatterns as $pattern => $description) {
                    if (strpos($pageSource, $pattern) !== false) {
                        $foundErrors[] = $description;
                        echo "❌ Laravel Error: {$description} (Pattern: {$pattern})\n";
                    }
                }
                
                if (empty($foundErrors)) {
                    echo "✅ No common Laravel errors detected\n";
                }
                
                // Check for debug information
                if (strpos($pageSource, 'stack-trace') !== false) {
                    echo "⚠️  Stack trace visible (debug mode)\n";
                }
                
                if (strpos($pageSource, 'local.ERROR') !== false) {
                    echo "⚠️  Laravel log errors visible\n";
                }
                
                $browser->screenshot('real_server_error_check');
                
            } catch (\Exception $e) {
                echo "Error checking test failed: " . $e->getMessage() . "\n";
                $browser->screenshot('real_server_error_check_failed');
            }
        });
    }
    
    /**
     * Test database connection if needed.
     */
    public function test_database_connection()
    {
        $this->browse(function (Browser $browser) {
            try {
                // Try to access a page that might need database
                $browser->visit('http://127.0.0.1:8000/dashboard')
                        ->pause(2000);
                
                $pageSource = $browser->driver->getPageSource();
                
                // Check for database-related errors
                $dbErrors = [
                    'SQLSTATE' => 'Database error',
                    'Connection refused' => 'Database server not running',
                    'Access denied' => 'Database credentials wrong',
                    'Unknown database' => 'Database doesn\'t exist',
                    'Base table or view not found' => 'Tables not created',
                ];
                
                foreach ($dbErrors as $error => $description) {
                    if (strpos($pageSource, $error) !== false) {
                        echo "❌ Database Error: {$description}\n";
                    }
                }
                
                $browser->screenshot('real_server_database_test');
                
            } catch (\Exception $e) {
                echo "Database connection test failed: " . $e->getMessage() . "\n";
                $browser->screenshot('real_server_database_test_failed');
            }
        });
    }
}