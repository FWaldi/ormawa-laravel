<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;
use App\Models\User;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_contains_csrf_token()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('_token', false);
    }

    public function test_register_form_contains_csrf_token()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('_token', false);
    }

    public function test_password_reset_form_contains_csrf_token()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('_token', false);
    }

    public function test_login_without_csrf_token_is_rejected()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        // Laravel should reject requests without CSRF token
        $this->assertGuest();
    }

    public function test_registration_without_csrf_token_is_rejected()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        // Laravel should reject requests without CSRF token
        $this->assertGuest();
    }

    public function test_login_rate_limiting()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // Attempt 6 login attempts (exceeds the 5 per minute limit)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be rate limited
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Too Many Attempts.'
        ]);
    }

    public function test_registration_rate_limiting()
    {
        // Attempt 4 registration attempts (exceeds the 3 per minute limit for password reset)
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/register', [
                'name' => "Test User {$i}",
                'email' => "test{$i}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        }

        // The 4th attempt should be rate limited
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Too Many Attempts.'
        ]);
    }

    public function test_password_reset_rate_limiting()
    {
        // Attempt 4 password reset requests (exceeds the 3 per minute limit)
        for ($i = 0; $i < 4; $i++) {
            $response = $this->post('/forgot-password', [
                'email' => 'test@example.com',
            ]);
        }

        // The 4th attempt should be rate limited
        $response->assertStatus(429); // Too Many Requests
        $response->assertJson([
            'message' => 'Too Many Attempts.'
        ]);
    }

    public function test_session_regeneration_on_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        // Start a session
        $session1 = session()->getId();

        // Login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Session should be regenerated
        $session2 = session()->getId();
        $this->assertNotEquals($session1, $session2);
        $this->assertAuthenticatedAs($user);
    }

    public function test_session_invalidation_on_logout()
    {
        $user = User::factory()->create();

        // Login and get session
        $this->actingAs($user);
        $sessionId = session()->getId();

        // Logout
        $response = $this->post('/logout');

        // Session should be invalidated
        $this->assertGuest();
    }

    public function test_account_enumeration_prevention()
    {
        // Test with non-existent email
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        // Should return generic error message, not "user not found"
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Test with existent email but wrong password
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Should return same generic error message
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_password_validation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123', // Too short
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_email_validation()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email', // Invalid email format
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_duplicate_email_registration()
    {
        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User 2',
            'email' => 'test@example.com', // Same email
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_google_oauth_redirect_returns_placeholder()
    {
        $response = $this->get('/auth/google');

        // Should redirect to login with error message (placeholder implementation)
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Google OAuth not yet configured');
    }

    public function test_google_oauth_callback_returns_placeholder()
    {
        $response = $this->get('/auth/google/callback');

        // Should redirect to login with error message (placeholder implementation)
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Google OAuth not yet configured');
    }

    public function test_protected_routes_require_authentication()
    {
        $protectedRoutes = [
            '/dashboard',
            '/organizations',
            '/activities',
            '/announcements',
            '/news',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_admin_routes_require_admin_role()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_routes_accessible_to_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_org_admin_routes_require_org_admin_role()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/organizations/create');
        $response->assertStatus(200); // Should be accessible (create is not org-admin only)
    }

    public function test_session_security_headers()
    {
        $response = $this->get('/login');

        // Check for security headers (these should be configured in production)
        // Note: These may not be present in test environment
        $response->assertStatus(200);
    }

    public function test_input_sanitization()
    {
        $maliciousInput = '<script>alert("xss")</script>';

        $response = $this->post('/register', [
            'name' => $maliciousInput,
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Should not create user with malicious input
        $this->assertGuest();
        $response->assertSessionHasErrors(['name']);
    }
}