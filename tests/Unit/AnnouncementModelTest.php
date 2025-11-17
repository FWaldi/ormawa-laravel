<?php

namespace Tests\Unit;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\StorageService;

class AnnouncementModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_can_be_created_with_factory()
    {
        $announcement = Announcement::factory()->create();
        
        $this->assertInstanceOf(Announcement::class, $announcement);
        $this->assertNotNull($announcement->id);
        $this->assertNotNull($announcement->title);
        $this->assertNotNull($announcement->content);
    }

    public function test_announcement_fillable_attributes()
    {
        $fillable = [
            'title',
            'content',
            'category',
            'image',
            'is_pinned',
            'created_by',
        ];

        $announcement = new Announcement();
        $this->assertEquals($fillable, $announcement->getFillable());
    }

    public function test_announcement_is_pinned_casts_to_boolean()
    {
        $announcement = Announcement::factory()->create(['is_pinned' => true]);
        $this->assertIsBool($announcement->is_pinned);
        $this->assertTrue($announcement->is_pinned);

        $announcement = Announcement::factory()->create(['is_pinned' => false]);
        $this->assertIsBool($announcement->is_pinned);
        $this->assertFalse($announcement->is_pinned);
    }

    public function test_announcement_belongs_to_creator()
    {
        $user = User::factory()->create();
        $announcement = Announcement::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $announcement->creator);
        $this->assertEquals($user->id, $announcement->creator->id);
    }

    public function test_announcement_image_url_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $fullUrl]);

        $this->assertEquals($fullUrl, $announcement->image_url);
    }

    public function test_announcement_image_url_attribute_with_storage_path()
    {
        $storagePath = '/storage/announcements/announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $storagePath]);

        $expectedUrl = config('app.url') . $storagePath;
        $this->assertEquals($expectedUrl, $announcement->image_url);
    }

    public function test_announcement_image_url_attribute_with_filename()
    {
        $filename = 'announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('getFileUrl')
            ->with($filename, 'announcements')
            ->willReturn('https://example.com/storage/announcements/' . $filename);

        $this->app->instance(StorageService::class, $storageService);

        $this->assertEquals('https://example.com/storage/announcements/' . $filename, $announcement->image_url);
    }

    public function test_announcement_image_url_attribute_with_null_image()
    {
        $announcement = Announcement::factory()->create(['image' => null]);

        $this->assertNull($announcement->image_url);
    }

    public function test_announcement_image_path_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $fullUrl]);

        $this->assertEquals('announcement.jpg', $announcement->image_path);
    }

    public function test_announcement_image_path_attribute_with_storage_path()
    {
        $storagePath = '/storage/announcements/announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $storagePath]);

        $this->assertEquals('announcement.jpg', $announcement->image_path);
    }

    public function test_announcement_image_path_attribute_with_filename()
    {
        $filename = 'announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $filename]);

        $this->assertEquals($filename, $announcement->image_path);
    }

    public function test_announcement_image_path_attribute_with_null_image()
    {
        $announcement = Announcement::factory()->create(['image' => null]);

        $this->assertNull($announcement->image_path);
    }

    public function test_announcement_soft_deletes()
    {
        $announcement = Announcement::factory()->create();
        $announcementId = $announcement->id;

        $announcement->delete();

        $this->assertSoftDeleted('announcements', ['id' => $announcementId]);
        $this->assertNotNull($announcement->deleted_at);
    }

    public function test_announcement_deletion_deletes_image_file()
    {
        $filename = 'announcement.jpg';
        $announcement = Announcement::factory()->create(['image' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('deleteFile')
            ->with($filename, 'announcements');

        $this->app->instance(StorageService::class, $storageService);

        $announcement->delete();
    }

    public function test_announcement_deletion_without_image()
    {
        $announcement = Announcement::factory()->create(['image' => null]);

        // Mock the StorageService to ensure deleteFile is not called
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->never())
            ->method('deleteFile');

        $this->app->instance(StorageService::class, $storageService);

        $announcement->delete();
    }

    public function test_announcement_scope_by_category()
    {
        Announcement::factory()->create(['category' => 'academic']);
        Announcement::factory()->create(['category' => 'academic']);
        Announcement::factory()->create(['category' => 'events']);
        Announcement::factory()->create(['category' => 'general']);

        $academicAnnouncements = Announcement::where('category', 'academic')->get();
        $this->assertCount(2, $academicAnnouncements);

        $eventsAnnouncements = Announcement::where('category', 'events')->get();
        $this->assertCount(1, $eventsAnnouncements);

        $generalAnnouncements = Announcement::where('category', 'general')->get();
        $this->assertCount(1, $generalAnnouncements);
    }

    public function test_announcement_scope_pinned()
    {
        Announcement::factory()->create(['is_pinned' => true]);
        Announcement::factory()->create(['is_pinned' => true]);
        Announcement::factory()->create(['is_pinned' => false]);
        Announcement::factory()->create(['is_pinned' => false]);

        $pinnedAnnouncements = Announcement::where('is_pinned', true)->get();
        $this->assertCount(2, $pinnedAnnouncements);

        $unpinnedAnnouncements = Announcement::where('is_pinned', false)->get();
        $this->assertCount(2, $unpinnedAnnouncements);
    }

    public function test_announcement_scope_by_creator()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Announcement::factory()->count(2)->create(['created_by' => $user1->id]);
        Announcement::factory()->count(3)->create(['created_by' => $user2->id]);

        $user1Announcements = Announcement::where('created_by', $user1->id)->get();
        $this->assertCount(2, $user1Announcements);

        $user2Announcements = Announcement::where('created_by', $user2->id)->get();
        $this->assertCount(3, $user2Announcements);
    }

    public function test_announcement_with_trashed_can_be_retrieved()
    {
        $announcement = Announcement::factory()->create();
        $announcementId = $announcement->id;

        $announcement->delete();

        $trashedAnnouncement = Announcement::withTrashed()->find($announcementId);
        $this->assertNotNull($trashedAnnouncement);
        $this->assertNotNull($trashedAnnouncement->deleted_at);

        $nonTrashedAnnouncement = Announcement::find($announcementId);
        $this->assertNull($nonTrashedAnnouncement);
    }

    public function test_announcement_only_trashed_scope()
    {
        $announcement1 = Announcement::factory()->create();
        $announcement2 = Announcement::factory()->create();
        $announcement3 = Announcement::factory()->create();

        $announcement2->delete();

        $trashedOnly = Announcement::onlyTrashed()->get();
        $this->assertCount(1, $trashedOnly);
        $this->assertEquals($announcement2->id, $trashedOnly->first()->id);
    }

    public function test_announcement_restore()
    {
        $announcement = Announcement::factory()->create();
        $announcementId = $announcement->id;

        $announcement->delete();
        $this->assertSoftDeleted('announcements', ['id' => $announcementId]);

        $announcement->restore();
        $this->assertNotSoftDeleted('announcements', ['id' => $announcementId]);
    }

    public function test_announcement_force_delete()
    {
        $announcement = Announcement::factory()->create();
        $announcementId = $announcement->id;

        $announcement->delete();
        $announcement->forceDelete();

        $this->assertDatabaseMissing('announcements', ['id' => $announcementId]);
    }
}