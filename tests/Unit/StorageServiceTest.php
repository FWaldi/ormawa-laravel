<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\StorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StorageServiceTest extends TestCase
{
    use RefreshDatabase;

    private StorageService $storageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storageService = new StorageService();
    }

    /**
     * Test successful file upload.
     */
    public function test_successful_file_upload(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-image.jpg', 500, 500);

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNotNull($result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('filename', $result);
        $this->assertArrayHasKey('original_name', $result);
        $this->assertArrayHasKey('mime_type', $result);
        $this->assertArrayHasKey('size', $result);
        $this->assertArrayHasKey('url', $result);

        $this->assertEquals('test-image.jpg', $result['original_name']);
        $this->assertEquals('image/jpeg', $result['mime_type']);
        $this->assertEquals('organizations', $result['disk']);

        Storage::disk('organizations')->assertExists($result['path']);
    }

    /**
     * Test file upload with oversized file.
     */
    public function test_file_upload_with_oversized_file(): void
    {
        $file = UploadedFile::fake()->image('huge-image.jpg')->size(6000); // 6MB

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNull($result);
    }

    /**
     * Test file upload with invalid MIME type.
     */
    public function test_file_upload_with_invalid_mime_type(): void
    {
        $file = UploadedFile::fake()->create('malicious.exe', 1000, 'application/x-executable');

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNull($result);
    }

    /**
     * Test file upload with MIME type and extension mismatch.
     */
    public function test_file_upload_with_mime_extension_mismatch(): void
    {
        // Create a file with .jpg extension but PDF MIME type
        $file = UploadedFile::fake()->create('fake-image.jpg', 1000, 'application/pdf');

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNull($result);
    }

    /**
     * Test document upload (PDF).
     */
    public function test_document_upload(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->create('document.pdf', 2000, 'application/pdf');

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNotNull($result);
        $this->assertEquals('document.pdf', $result['original_name']);
        $this->assertEquals('application/pdf', $result['mime_type']);
        $this->assertEquals('documents', $result['category']);

        Storage::disk('organizations')->assertExists($result['path']);
    }

    /**
     * Test file deletion.
     */
    public function test_file_deletion(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('test-delete.jpg', 500, 500);

        // Upload file first
        $uploadResult = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNotNull($uploadResult);

        // Delete file
        $deleted = $this->storageService->deleteFile(
            $uploadResult['filename'],
            'organizations'
        );

        $this->assertTrue($deleted);
        Storage::disk('organizations')->assertMissing($uploadResult['path']);
    }

    /**
     * Test deletion of non-existent file.
     */
    public function test_deletion_of_non_existent_file(): void
    {
        $deleted = $this->storageService->deleteFile(
            'non-existent-file.jpg',
            'organizations'
        );

        $this->assertFalse($deleted);
    }

    /**
     * Test getting file URL for public disk.
     */
    public function test_get_file_url_public_disk(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('public-image.jpg', 500, 500);

        // Upload file to public disk
        $path = $file->store('images', 'public');

        $url = $this->storageService->getFileUrl(
            basename($path),
            'public'
        );

        $this->assertNotNull($url);
        $this->assertStringContains('storage', $url);
    }

    /**
     * Test getting file URL for private disk.
     */
    public function test_get_file_url_private_disk(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('private-image.jpg', 500, 500);

        // Upload file to private disk
        $path = $file->store('images', 'organizations');

        $url = $this->storageService->getFileUrl(
            basename($path),
            'organizations'
        );

        $this->assertNotNull($url);
        // For private disks, should return a route-based URL
        $this->assertStringContains('files/show', $url);
    }

    /**
     * Test getting file URL for non-existent file.
     */
    public function test_get_file_url_non_existent_file(): void
    {
        $url = $this->storageService->getFileUrl(
            'non-existent-file.jpg',
            'organizations'
        );

        $this->assertNull($url);
    }

    /**
     * Test getting file information.
     */
    public function test_get_file_info(): void
    {
        Storage::fake('organizations');

        $file = UploadedFile::fake()->image('info-test.jpg', 500, 500);

        // Upload file first
        $uploadResult = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNotNull($uploadResult);

        // Get file info
        $fileInfo = $this->storageService->getFileInfo(
            $uploadResult['filename'],
            'organizations'
        );

        $this->assertNotNull($fileInfo);
        $this->assertEquals($uploadResult['filename'], $fileInfo['filename']);
        $this->assertEquals($uploadResult['original_name'], $fileInfo['original_name']);
        $this->assertArrayHasKey('size', $fileInfo);
        $this->assertArrayHasKey('last_modified', $fileInfo);
        $this->assertArrayHasKey('url', $fileInfo);
    }

    /**
     * Test getting file info for non-existent file.
     */
    public function test_get_file_info_non_existent_file(): void
    {
        $fileInfo = $this->storageService->getFileInfo(
            'non-existent-file.jpg',
            'organizations'
        );

        $this->assertNull($fileInfo);
    }

    /**
     * Test disk usage statistics.
     */
    public function test_disk_usage_statistics(): void
    {
        Storage::fake('organizations');

        // Create some test files
        UploadedFile::fake()->image('image1.jpg', 500, 500)->store('test1', 'organizations');
        UploadedFile::fake()->image('image2.jpg', 800, 600)->store('test2', 'organizations');
        UploadedFile::fake()->create('document.pdf', 2000)->store('test3', 'organizations');

        $usage = $this->storageService->getDiskUsage('organizations');

        $this->assertArrayHasKey('disk', $usage);
        $this->assertArrayHasKey('total_size', $usage);
        $this->assertArrayHasKey('total_size_human', $usage);
        $this->assertArrayHasKey('file_count', $usage);
        $this->assertArrayHasKey('files_by_type', $usage);

        $this->assertEquals('organizations', $usage['disk']);
        $this->assertEquals(3, $usage['file_count']);
        $this->assertGreaterThan(0, $usage['total_size']);
        $this->assertArrayHasKey('jpg', $usage['files_by_type']);
        $this->assertArrayHasKey('pdf', $usage['files_by_type']);
        $this->assertEquals(2, $usage['files_by_type']['jpg']);
        $this->assertEquals(1, $usage['files_by_type']['pdf']);
    }

    /**
     * Test disk usage for empty disk.
     */
    public function test_disk_usage_empty_disk(): void
    {
        Storage::fake('organizations');

        $usage = $this->storageService->getDiskUsage('organizations');

        $this->assertEquals(0, $usage['total_size']);
        $this->assertEquals('0 B', $usage['total_size_human']);
        $this->assertEquals(0, $usage['file_count']);
        $this->assertEmpty($usage['files_by_type']);
    }

    /**
     * Test orphaned files cleanup.
     */
    public function test_orphaned_files_cleanup(): void
    {
        Storage::fake('organizations');

        // Create a file with metadata
        $file = UploadedFile::fake()->image('orphaned.jpg', 500, 500);
        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            1
        );

        $this->assertNotNull($result);

        // Simulate orphaned file by removing metadata
        $metadataFile = storage_path("app/metadata/organizations_metadata.json");
        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];
            unset($metadata[$result['filename']]);
            file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
        }

        // Run cleanup (should remove the orphaned file)
        $cleanedCount = $this->storageService->cleanupOrphanedFiles('organizations', 0); // 0 days to clean immediately

        $this->assertGreaterThan(0, $cleanedCount);
        Storage::disk('organizations')->assertMissing($result['path']);
    }

    /**
     * Test secure filename generation.
     */
    public function test_secure_filename_generation(): void
    {
        $file = UploadedFile::fake()->image('test-image.jpg', 500, 500);

        $result = $this->storageService->uploadFile(
            $file,
            'organizations',
            'organization',
            123
        );

        $this->assertNotNull($result);

        $filename = $result['filename'];
        
        // Should contain context, context_id, timestamp, and random string
        $this->assertStringContains('organization_123', $filename);
        $this->assertStringContains('_', $filename);
        $this->assertStringEndsWith('.jpg', $filename);
        
        // Should not contain the original filename directly
        $this->assertStringNotContainsString('test-image', $filename);
    }
}