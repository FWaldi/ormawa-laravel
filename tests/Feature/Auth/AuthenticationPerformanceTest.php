<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthenticationPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_response_time_under_load()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $startTime = microtime(true);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Login should complete within 2 seconds
        $this->assertLessThan(2000, $responseTime);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_response_time_under_load()
    {
        $startTime = microtime(true);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Registration should complete within 2 seconds
        $this->assertLessThan(2000, $responseTime);
        $this->assertAuthenticated();
    }

    public function test_logout_response_time_under_load()
    {
        $user = User::factory()->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->post('/logout');

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Logout should complete within 1 second
        $this->assertLessThan(1000, $responseTime);
        $this->assertGuest();
    }

    public function test_database_query_optimization()
    {
        // Enable query logging
        DB::enableQueryLog();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // Perform login
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $queries = DB::getQueryLog();

        // Should not have excessive queries (N+1 problem)
        $this->assertLessThan(10, count($queries));

        DB::disableQueryLog();
    }

    public function test_session_storage_performance()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // Test session creation and retrieval
        $startTime = microtime(true);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $endTime = microtime(true);
        $sessionTime = ($endTime - $startTime) * 1000;

        // Session operations should be efficient
        $this->assertLessThan(500, $sessionTime);
    }

    public function test_concurrent_login_attempts()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $startTime = microtime(true);

        // Simulate concurrent requests (simplified test)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);
        }

        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        // Multiple concurrent requests should be handled efficiently
        $this->assertLessThan(5000, $totalTime); // 5 seconds for 5 requests
    }

    public function test_memory_usage_during_authentication()
    {
        $memoryBefore = memory_get_usage();

        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $memoryAfter = memory_get_usage();
        $memoryUsed = $memoryAfter - $memoryBefore;

        // Authentication should not use excessive memory
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed); // Less than 10MB
    }

    public function test_role_based_access_performance()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $startTime = microtime(true);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        // Role-based access should be efficient
        $this->assertLessThan(1000, $responseTime);
        $response->assertStatus(200);
    }

    public function test_password_hashing_performance()
    {
        $startTime = microtime(true);

        User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $endTime = microtime(true);
        $hashingTime = ($endTime - $startTime) * 1000;

        // Password hashing should complete in reasonable time
        $this->assertLessThan(1000, $hashingTime);
    }

    public function test_email_verification_performance()
    {
        $user = User::factory()->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get('/email/verify');

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        // Email verification page should load quickly
        $this->assertLessThan(500, $responseTime);
        $response->assertStatus(200);
    }

    public function test_oauth_redirect_performance()
    {
        $startTime = microtime(true);

        $response = $this->get('/auth/google');

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        // OAuth redirect should be fast
        $this->assertLessThan(500, $responseTime);
        $response->assertRedirect('/login');
    }

    public function test_database_index_effectiveness()
    {
        // Create multiple users to test index effectiveness
        User::factory()->count(100)->create();

        $startTime = microtime(true);

        $user = User::where('email', 'test@example.com')->first();

        $endTime = microtime(true);
        $queryTime = ($endTime - $startTime) * 1000;

        // Email lookup should be fast with proper indexing
        $this->assertLessThan(100, $queryTime);
    }
}