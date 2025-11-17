<?php

namespace Tests\Unit;

use App\Models\File;
use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\News;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\StorageService;

class FileModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_can_be_created_with_factory()
    {
        $file = File::factory()->create();
        
        $this->assertInstanceOf(File::class, $file);
        $this->assertNotNull($file->id);
        $this->assertNotNull($file->filename);
        $this->assertNotNull($file->original_name);
    }

    public function test_file_fillable_attributes()
    {
        $fillable = [
            'filename',
            'original_name',
            'mime_type',
            'size',
            'path',
            'disk',
            'context',
            'context_id',
            'uploaded_by',
        ];

        $file = new File();
        $this->assertEquals($fillable, $file->getFillable());
    }

    public function test_file_belongs_to_uploader()
    {
        $user = User::factory()->create();
        $file = File::factory()->create(['uploaded_by' => $user->id]);

        $this->assertInstanceOf(User::class, $file->uploader);
        $this->assertEquals($user->id, $file->uploader->id);
    }

    public function test_file_belongs_to_organization()
    {
        $organization = Organization::factory()->create();
        $file = File::factory()->create([
            'context' => 'organization',
            'context_id' => $organization->id
        ]);

        $this->assertInstanceOf(Organization::class, $file->organization);
        $this->assertEquals($organization->id, $file->organization->id);
    }

    public function test_file_belongs_to_activity()
    {
        $activity = Activity::factory()->create();
        $file = File::factory()->create([
            'context' => 'activity',
            'context_id' => $activity->id
        ]);

        $this->assertInstanceOf(Activity::class, $file->activity);
        $this->assertEquals($activity->id, $file->activity->id);
    }

    public function test_file_belongs_to_news()
    {
        $news = News::factory()->create();
        $file = File::factory()->create([
            'context' => 'news',
            'context_id' => $news->id
        ]);

        $this->assertInstanceOf(News::class, $file->news);
        $this->assertEquals($news->id, $file->news->id);
    }

    public function test_file_belongs_to_announcement()
    {
        $announcement = Announcement::factory()->create();
        $file = File::factory()->create([
            'context' => 'announcement',
            'context_id' => $announcement->id
        ]);

        $this->assertInstanceOf(Announcement::class, $file->announcement);
        $this->assertEquals($announcement->id, $file->announcement->id);
    }

    public function test_file_url_attribute()
    {
        $file = File::factory()->create([
            'filename' => 'test.jpg',
            'disk' => 'public'
        ]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('getFileUrl')
            ->with('test.jpg', 'public')
            ->willReturn('https://example.com/storage/test.jpg');

        $this->app->instance(StorageService::class, $storageService);

        $this->assertEquals('https://example.com/storage/test.jpg', $file->url);
    }

    public function test_file_human_size_attribute()
    {
        $file = File::factory()->create(['size' => 1024]);
        $this->assertEquals('1 KB', $file->human_size);

        $file = File::factory()->create(['size' => 1048576]);
        $this->assertEquals('1 MB', $file->human_size);

        $file = File::factory()->create(['size' => 1073741824]);
        $this->assertEquals('1 GB', $file->human_size);

        $file = File::factory()->create(['size' => 500]);
        $this->assertEquals('500 B', $file->human_size);

        $file = File::factory()->create(['size' => 1536]);
        $this->assertEquals('1.5 KB', $file->human_size);
    }

    public function test_file_is_image_method()
    {
        $imageFile = File::factory()->create(['mime_type' => 'image/jpeg']);
        $this->assertTrue($imageFile->isImage());

        $pngFile = File::factory()->create(['mime_type' => 'image/png']);
        $this->assertTrue($pngFile->isImage());

        $pdfFile = File::factory()->create(['mime_type' => 'application/pdf']);
        $this->assertFalse($pdfFile->isImage());

        $textFile = File::factory()->create(['mime_type' => 'text/plain']);
        $this->assertFalse($textFile->isImage());
    }

    public function test_file_is_document_method()
    {
        $pdfFile = File::factory()->create(['mime_type' => 'application/pdf']);
        $this->assertTrue($pdfFile->isDocument());

        $docFile = File::factory()->create(['mime_type' => 'application/msword']);
        $this->assertTrue($docFile->isDocument());

        $docxFile = File::factory()->create(['mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        $this->assertTrue($docxFile->isDocument());

        $xlsFile = File::factory()->create(['mime_type' => 'application/vnd.ms-excel']);
        $this->assertTrue($xlsFile->isDocument());

        $xlsxFile = File::factory()->create(['mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
        $this->assertTrue($xlsxFile->isDocument());

        $pptFile = File::factory()->create(['mime_type' => 'application/vnd.ms-powerpoint']);
        $this->assertTrue($pptFile->isDocument());

        $pptxFile = File::factory()->create(['mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation']);
        $this->assertTrue($pptxFile->isDocument());

        $textFile = File::factory()->create(['mime_type' => 'text/plain']);
        $this->assertTrue($textFile->isDocument());

        $csvFile = File::factory()->create(['mime_type' => 'text/csv']);
        $this->assertTrue($csvFile->isDocument());

        $imageFile = File::factory()->create(['mime_type' => 'image/jpeg']);
        $this->assertFalse($imageFile->isDocument());
    }

    public function test_file_extension_attribute()
    {
        $file = File::factory()->create(['original_name' => 'document.pdf']);
        $this->assertEquals('pdf', $file->extension);

        $file = File::factory()->create(['original_name' => 'image.JPG']);
        $this->assertEquals('JPG', $file->extension);

        $file = File::factory()->create(['original_name' => 'spreadsheet.xlsx']);
        $this->assertEquals('xlsx', $file->extension);

        $file = File::factory()->create(['original_name' => 'archive.tar.gz']);
        $this->assertEquals('gz', $file->extension);

        $file = File::factory()->create(['original_name' => 'noextension']);
        $this->assertEquals('', $file->extension);
    }

    public function test_file_scope_for_context()
    {
        File::factory()->create(['context' => 'organization']);
        File::factory()->create(['context' => 'organization']);
        File::factory()->create(['context' => 'activity']);
        File::factory()->create(['context' => 'news']);

        $orgFiles = File::forContext('organization')->get();
        $this->assertCount(2, $orgFiles);

        $activityFiles = File::forContext('activity')->get();
        $this->assertCount(1, $activityFiles);

        $newsFiles = File::forContext('news')->get();
        $this->assertCount(1, $newsFiles);
    }

    public function test_file_scope_for_context_with_id()
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        
        File::factory()->create([
            'context' => 'organization',
            'context_id' => $organization1->id
        ]);
        File::factory()->create([
            'context' => 'organization',
            'context_id' => $organization1->id
        ]);
        File::factory()->create([
            'context' => 'organization',
            'context_id' => $organization2->id
        ]);

        $org1Files = File::forContext('organization', $organization1->id)->get();
        $this->assertCount(2, $org1Files);

        $org2Files = File::forContext('organization', $organization2->id)->get();
        $this->assertCount(1, $org2Files);
    }

    public function test_file_scope_images()
    {
        File::factory()->create(['mime_type' => 'image/jpeg']);
        File::factory()->create(['mime_type' => 'image/png']);
        File::factory()->create(['mime_type' => 'image/gif']);
        File::factory()->create(['mime_type' => 'application/pdf']);
        File::factory()->create(['mime_type' => 'text/plain']);

        $imageFiles = File::images()->get();
        $this->assertCount(3, $imageFiles);

        foreach ($imageFiles as $file) {
            $this->assertTrue(str_starts_with($file->mime_type, 'image/'));
        }
    }

    public function test_file_scope_documents()
    {
        File::factory()->create(['mime_type' => 'application/pdf']);
        File::factory()->create(['mime_type' => 'application/msword']);
        File::factory()->create(['mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
        File::factory()->create(['mime_type' => 'image/jpeg']);
        File::factory()->create(['mime_type' => 'video/mp4']);

        $documentFiles = File::documents()->get();
        $this->assertCount(3, $documentFiles);

        foreach ($documentFiles as $file) {
            $this->assertTrue($file->isDocument());
        }
    }

    public function test_file_scope_by_uploader()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        File::factory()->count(2)->create(['uploaded_by' => $user1->id]);
        File::factory()->count(3)->create(['uploaded_by' => $user2->id]);

        $user1Files = File::where('uploaded_by', $user1->id)->get();
        $this->assertCount(2, $user1Files);

        $user2Files = File::where('uploaded_by', $user2->id)->get();
        $this->assertCount(3, $user2Files);
    }

    public function test_file_scope_by_disk()
    {
        File::factory()->create(['disk' => 'public']);
        File::factory()->create(['disk' => 'public']);
        File::factory()->create(['disk' => 'local']);
        File::factory()->create(['disk' => 's3']);

        $publicFiles = File::where('disk', 'public')->get();
        $this->assertCount(2, $publicFiles);

        $localFiles = File::where('disk', 'local')->get();
        $this->assertCount(1, $localFiles);

        $s3Files = File::where('disk', 's3')->get();
        $this->assertCount(1, $s3Files);
    }

    public function test_file_without_uploader()
    {
        $file = File::factory()->create(['uploaded_by' => null]);

        $this->assertNull($file->uploader);
    }

    public function test_file_relationships_with_wrong_context()
    {
        $organization = Organization::factory()->create();
        $file = File::factory()->create([
            'context' => 'activity', // Wrong context
            'context_id' => $organization->id
        ]);

        $this->assertNull($file->organization);
    }
}