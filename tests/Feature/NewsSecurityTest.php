<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Organization;
use App\Models\News;
use App\Services\FileUploadService;

class NewsSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test XSS prevention in news content
     */
    public function test_xss_prevention_in_news_content(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        $maliciousContent = '<script>alert("XSS")</script><p>Valid content</p>';
        
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => $maliciousContent,
            'organization_id' => $organization->id,
            'is_published' => true,
        ]);

        $response->assertRedirect();
        
        $news = News::first();
        $this->assertStringNotContainsString('<script>', $news->content);
        $this->assertStringContainsString('<p>Valid content</p>', $news->content);
    }

    /**
     * Test file upload security - malicious file types
     */
    public function test_malicious_file_upload_blocked(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        // Test PHP file upload
        $maliciousFile = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');
        
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'organization_id' => $organization->id,
            'image' => $maliciousFile,
            'is_published' => true,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('news', 0);
    }

    /**
     * Test file upload with fake image containing malicious content
     */
    public function test_fake_image_with_malicious_content_blocked(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        // Create a fake image file with malicious content
        $maliciousImage = UploadedFile::fake()->createWithContent('malicious.jpg', '<?php echo "hack"; ?>');
        
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'organization_id' => $organization->id,
            'image' => $maliciousImage,
            'is_published' => true,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('news', 0);
    }

    /**
     * Test file size limit enforcement
     */
    public function test_file_size_limit_enforced(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        // Create an oversized image file
        $oversizedFile = UploadedFile::fake()->create('large.jpg', 5000); // 5MB
        
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'organization_id' => $organization->id,
            'image' => $oversizedFile,
            'is_published' => true,
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('news', 0);
    }

    /**
     * Test valid image upload works correctly
     */
    public function test_valid_image_upload_works(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        $validImage = UploadedFile::fake()->image('valid.jpg', 800, 600);
        
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => 'Test content',
            'organization_id' => $organization->id,
            'image' => $validImage,
            'is_published' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('news', 1);
        
        $news = News::first();
        $this->assertNotNull($news->image);
        $this->assertTrue(Storage::disk('public')->exists($news->image));
    }

    /**
     * Test rate limiting on news creation
     */
    public function test_rate_limiting_on_news_creation(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        $newsData = [
            'title' => 'Test News',
            'content' => 'Test content with sufficient length',
            'organization_id' => $organization->id,
            'is_published' => true,
        ];

        // Make 6 requests (limit is 5 per minute)
        $responses = [];
        for ($i = 0; $i < 6; $i++) {
            $responses[] = $this->actingAs($user)->post('/news', $newsData);
        }

        // First 5 should succeed
        for ($i = 0; $i < 5; $i++) {
            $responses[$i]->assertRedirect();
        }
        
        // 6th should be rate limited
        $responses[5]->assertStatus(429);
    }

    /**
     * test authorization bypass attempts
     */
    public function test_authorization_bypass_attempts(): void
    {
        $regularUser = User::factory()->create(['is_admin' => false]);
        $adminUser = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        // Create news as admin
        $news = News::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $adminUser->id,
        ]);

        // Try to edit as different user
        $response = $this->actingAs($regularUser)->put("/news/{$news->id}", [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
            'organization_id' => $organization->id,
            'is_published' => true,
        ]);

        $response->assertStatus(403);
        
        // Verify news wasn't changed
        $news->refresh();
        $this->assertNotEquals('Hacked Title', $news->title);
    }

    /**
     * Test content length validation
     */
    public function test_content_length_validation(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $organization = Organization::factory()->create();
        
        // Test too short content
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => 'Too short',
            'organization_id' => $organization->id,
            'is_published' => true,
        ]);

        $response->assertSessionHasErrors('content');
        
        // Test too long content
        $longContent = str_repeat('a', 50001);
        $response = $this->actingAs($user)->post('/news', [
            'title' => 'Test News',
            'content' => $longContent,
            'organization_id' => $organization->id,
            'is_published' => true,
        ]);

        $response->assertSessionHasErrors('content');
    }

    /**
     * Test CSP headers are present
     */
    public function test_csp_headers_present(): void
    {
        $response = $this->get('/news');
        
        $response->assertHeader('Content-Security-Policy');
        
        $cspHeader = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $cspHeader);
        $this->assertStringContainsString("script-src 'self'", $cspHeader);
        $this->assertStringContainsString("frame-src 'none'", $cspHeader);
    }

    /**
     * Test FileUploadService directly
     */
    public function test_file_upload_service_security(): void
    {
        // Test with malicious file
        $maliciousFile = UploadedFile::fake()->createWithContent('test.php', '<?php echo "test"; ?>');
        $result = FileUploadService::secureUpload($maliciousFile);
        
        $this->assertNull($result);
        
        // Test with valid image
        $validImage = UploadedFile::fake()->image('test.jpg');
        $result = FileUploadService::secureUpload($validImage);
        
        $this->assertNotNull($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertTrue(Storage::disk('public')->exists($result['path']));
    }
}