<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\News;
use App\Models\Announcement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $orgAdmin;
    private User $regularUser;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->orgAdmin = User::factory()->create(['is_admin' => false]);
        $this->regularUser = User::factory()->create(['is_admin' => false]);

        // Create test organization
        $this->organization = Organization::factory()->create();
        
        // Add org admin to organization
        $this->organization->members()->attach($this->orgAdmin->id, ['role' => 'admin']);
    }

    /**
     * Test successful file upload for organization by admin.
     */
    public function test_organization_file_upload_by_admin(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-logo.jpg', 500, 500);

        $response = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'test-logo.jpg',
            'context' => 'organization',
            'context_id' => $this->organization->id,
            'uploaded_by' => $this->admin->id
        ]);

        Storage::disk('organizations')->assertExists($response->json('data.path'));
    }

    /**
     * Test successful file upload for organization by organization admin.
     */
    public function test_organization_file_upload_by_org_admin(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-logo.jpg', 500, 500);

        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'test-logo.jpg',
            'context' => 'organization',
            'context_id' => $this->organization->id,
            'uploaded_by' => $this->orgAdmin->id
        ]);
    }

    /**
     * Test file upload failure for unauthorized user.
     */
    public function test_organization_file_upload_by_unauthorized_user(): void
    {
        $file = UploadedFile::fake()->image('test-logo.jpg', 500, 500);

        $response = $this->actingAs($this->regularUser)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You do not have permission to upload files for this context'
            ]);
    }

    /**
     * Test file upload with invalid file type.
     */
    public function test_file_upload_with_invalid_type(): void
    {
        $file = UploadedFile::fake()->create('malicious.exe', 1000);

        $response = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ]);
    }

    /**
     * Test file upload with oversized file.
     */
    public function test_file_upload_with_oversized_file(): void
    {
        $file = UploadedFile::fake()->image('huge-image.jpg')->size(6000); // 6MB

        $response = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ]);
    }

    /**
     * Test activity file upload.
     */
    public function test_activity_file_upload(): void
    {
        Storage::fake('activities');

        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $file = UploadedFile::fake()->image('activity-image.jpg', 800, 600);

        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('upload.activities'), [
                'file' => $file,
                'context_id' => $activity->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'activity-image.jpg',
            'context' => 'activity',
            'context_id' => $activity->id,
            'uploaded_by' => $this->orgAdmin->id
        ]);
    }

    /**
     * Test news file upload.
     */
    public function test_news_file_upload(): void
    {
        Storage::fake('news');

        $news = News::factory()->create([
            'organization_id' => $this->organization->id
        ]);

        $file = UploadedFile::fake()->image('news-image.jpg', 1200, 800);

        $response = $this->actingAs($this->orgAdmin)
            ->postJson(route('upload.news'), [
                'file' => $file,
                'context_id' => $news->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'news-image.jpg',
            'context' => 'news',
            'context_id' => $news->id,
            'uploaded_by' => $this->orgAdmin->id
        ]);
    }

    /**
     * Test announcement file upload.
     */
    public function test_announcement_file_upload(): void
    {
        Storage::fake('announcements');

        $announcement = Announcement::factory()->create();

        $file = UploadedFile::fake()->image('announcement-banner.jpg', 1200, 400);

        $response = $this->actingAs($this->admin)
            ->postJson(route('upload.announcements'), [
                'file' => $file,
                'context_id' => $announcement->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'announcement-banner.jpg',
            'context' => 'announcement',
            'context_id' => $announcement->id,
            'uploaded_by' => $this->admin->id
        ]);
    }

    /**
     * Test file deletion by owner.
     */
    public function test_file_deletion_by_owner(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-delete.jpg', 500, 500);

        // Upload file first
        $uploadResponse = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $filename = $uploadResponse->json('data.filename');

        // Delete file
        $response = $this->actingAs($this->admin)
            ->deleteJson(route('upload.delete', ['disk' => 'organizations']), [
                'filename' => $filename,
                'context' => 'organization',
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);

        Storage::disk('organizations')->assertMissing($filename);
    }

    /**
     * Test file deletion by unauthorized user.
     */
    public function test_file_deletion_by_unauthorized_user(): void
    {
        $file = UploadedFile::fake()->image('test-protected.jpg', 500, 500);

        // Upload file by admin
        $uploadResponse = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $filename = $uploadResponse->json('data.filename');

        // Try to delete by regular user
        $response = $this->actingAs($this->regularUser)
            ->deleteJson(route('upload.delete', ['disk' => 'organizations']), [
                'filename' => $filename,
                'context' => 'organization',
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'You do not have permission to delete this file'
            ]);
    }

    /**
     * Test file URL generation.
     */
    public function test_file_url_generation(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-url.jpg', 500, 500);

        // Upload file first
        $uploadResponse = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $filename = $uploadResponse->json('data.filename');

        // Get file URL
        $response = $this->actingAs($this->admin)
            ->getJson(route('upload.url', ['disk' => 'organizations']), [
                'filename' => $filename,
                'context' => 'organization',
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertArrayHasKey('url', $response->json('data'));
    }

    /**
     * Test document upload (PDF).
     */
    public function test_document_upload(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->create('document.pdf', 2000, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->postJson(route('upload.organizations'), [
                'file' => $file,
                'context_id' => $this->organization->id
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'File uploaded successfully'
            ]);

        $this->assertDatabaseHas('files', [
            'original_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'context' => 'organization',
            'context_id' => $this->organization->id
        ]);
    }

    /**
     * Test file upload without authentication.
     */
    public function test_file_upload_without_authentication(): void
    {
        $file = UploadedFile::fake()->image('test-unauth.jpg', 500, 500);

        $response = $this->postJson(route('upload.organizations'), [
            'file' => $file,
            'context_id' => $this->organization->id
        ]);

        $response->assertStatus(401);
    }
}