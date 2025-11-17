<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\StorageService;

class ActivityModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_can_be_created_with_factory()
    {
        $activity = Activity::factory()->create();
        
        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertNotNull($activity->id);
        $this->assertNotNull($activity->title);
        $this->assertNotNull($activity->description);
    }

    public function test_activity_fillable_attributes()
    {
        $fillable = [
            'title',
            'description',
            'organization_id',
            'start_date',
            'end_date',
            'location',
            'images',
            'status',
            'created_by',
        ];

        $activity = new Activity();
        $this->assertEquals($fillable, $activity->getFillable());
    }

    public function test_activity_date_casts()
    {
        $startDate = '2024-12-01 10:00:00';
        $endDate = '2024-12-01 12:00:00';
        
        $activity = Activity::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $activity->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $activity->end_date);
        $this->assertEquals($startDate, $activity->start_date->format('Y-m-d H:i:s'));
        $this->assertEquals($endDate, $activity->end_date->format('Y-m-d H:i:s'));
    }

    public function test_activity_images_casts_to_array()
    {
        $images = ['image1.jpg', 'image2.jpg', 'image3.jpg'];
        $activity = Activity::factory()->create(['images' => $images]);

        $this->assertIsArray($activity->images);
        $this->assertEquals($images, $activity->images);
    }

    public function test_activity_status_getter_mutator()
    {
        $activity = Activity::factory()->create(['status' => 'ACTIVE']);
        
        $this->assertEquals('active', $activity->status);
        $this->assertEquals('ACTIVE', $activity->getRawOriginal('status'));
    }

    public function test_activity_status_setter_mutator()
    {
        $activity = new Activity();
        $activity->status = 'active';
        $activity->save();

        $this->assertEquals('ACTIVE', $activity->getRawOriginal('status'));
        $this->assertEquals('active', $activity->status);
    }

    public function test_activity_belongs_to_organization()
    {
        $organization = Organization::factory()->create();
        $activity = Activity::factory()->create(['organization_id' => $organization->id]);

        $this->assertInstanceOf(Organization::class, $activity->organization);
        $this->assertEquals($organization->id, $activity->organization->id);
    }

    public function test_activity_belongs_to_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $activity->creator);
        $this->assertEquals($user->id, $activity->creator->id);
    }

    public function test_activity_image_urls_attribute_with_full_urls()
    {
        $images = [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg',
        ];
        $activity = Activity::factory()->create(['images' => $images]);

        $this->assertEquals($images, $activity->image_urls);
    }

    public function test_activity_image_urls_attribute_with_storage_paths()
    {
        $images = [
            '/storage/activities/image1.jpg',
            '/storage/activities/image2.jpg',
        ];
        $activity = Activity::factory()->create(['images' => $images]);

        $expectedUrls = [
            config('app.url') . '/storage/activities/image1.jpg',
            config('app.url') . '/storage/activities/image2.jpg',
        ];

        $this->assertEquals($expectedUrls, $activity->image_urls);
    }

    public function test_activity_image_urls_attribute_with_filenames()
    {
        $images = ['image1.jpg', 'image2.jpg'];
        $activity = Activity::factory()->create(['images' => $images]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->exactly(2))
            ->method('getFileUrl')
            ->withConsecutive(['image1.jpg', 'activities'], ['image2.jpg', 'activities'])
            ->willReturnOnConsecutiveCalls(
                'https://example.com/storage/activities/image1.jpg',
                'https://example.com/storage/activities/image2.jpg'
            );

        $this->app->instance(StorageService::class, $storageService);

        $expectedUrls = [
            'https://example.com/storage/activities/image1.jpg',
            'https://example.com/storage/activities/image2.jpg',
        ];

        $this->assertEquals($expectedUrls, $activity->image_urls);
    }

    public function test_activity_image_urls_attribute_with_empty_images()
    {
        $activity = Activity::factory()->create(['images' => null]);

        $this->assertEquals([], $activity->image_urls);
    }

    public function test_activity_image_filenames_attribute_with_full_urls()
    {
        $images = [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg',
        ];
        $activity = Activity::factory()->create(['images' => $images]);

        $expectedFilenames = ['image1.jpg', 'image2.jpg'];
        $this->assertEquals($expectedFilenames, $activity->image_filenames);
    }

    public function test_activity_image_filenames_attribute_with_storage_paths()
    {
        $images = [
            '/storage/activities/image1.jpg',
            '/storage/activities/image2.jpg',
        ];
        $activity = Activity::factory()->create(['images' => $images]);

        $expectedFilenames = ['image1.jpg', 'image2.jpg'];
        $this->assertEquals($expectedFilenames, $activity->image_filenames);
    }

    public function test_activity_image_filenames_attribute_with_filenames()
    {
        $images = ['image1.jpg', 'image2.jpg'];
        $activity = Activity::factory()->create(['images' => $images]);

        $this->assertEquals($images, $activity->image_filenames);
    }

    public function test_activity_add_image()
    {
        $activity = Activity::factory()->create(['images' => ['image1.jpg']]);
        
        $activity->addImage('image2.jpg');
        
        $this->assertCount(2, $activity->images);
        $this->assertContains('image2.jpg', $activity->images);
    }

    public function test_activity_add_image_to_empty_images()
    {
        $activity = Activity::factory()->create(['images' => null]);
        
        $activity->addImage('image1.jpg');
        
        $this->assertCount(1, $activity->images);
        $this->assertEquals(['image1.jpg'], $activity->images);
    }

    public function test_activity_remove_image()
    {
        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('deleteFile')
            ->with('image2.jpg', 'activities')
            ->willReturn(true);

        $this->app->instance(StorageService::class, $storageService);

        $activity = Activity::factory()->create(['images' => ['image1.jpg', 'image2.jpg', 'image3.jpg']]);
        
        $result = $activity->removeImage('image2.jpg');
        
        $this->assertTrue($result);
        $this->assertCount(2, $activity->fresh()->images);
        $this->assertNotContains('image2.jpg', $activity->fresh()->images);
        $this->assertEquals(['image1.jpg', 'image3.jpg'], $activity->fresh()->images);
    }

    public function test_activity_remove_nonexistent_image()
    {
        $activity = Activity::factory()->create(['images' => ['image1.jpg']]);
        
        $result = $activity->removeImage('nonexistent.jpg');
        
        $this->assertFalse($result);
        $this->assertCount(1, $activity->fresh()->images);
    }

    public function test_activity_remove_image_from_empty_images()
    {
        $activity = Activity::factory()->create(['images' => null]);
        
        $result = $activity->removeImage('image1.jpg');
        
        $this->assertFalse($result);
    }

    public function test_activity_deletion_deletes_image_files()
    {
        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->exactly(2))
            ->method('deleteFile')
            ->withConsecutive(['image1.jpg', 'activities'], ['image2.jpg', 'activities']);

        $this->app->instance(StorageService::class, $storageService);

        $activity = Activity::factory()->create(['images' => ['image1.jpg', 'image2.jpg']]);
        
        $activity->delete();
    }

    public function test_activity_deletion_without_images()
    {
        // Mock the StorageService to ensure deleteFile is not called
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->never())
            ->method('deleteFile');

        $this->app->instance(StorageService::class, $storageService);

        $activity = Activity::factory()->create(['images' => null]);
        
        $activity->delete();
    }

    public function test_activity_scope_by_status()
    {
        Activity::factory()->create(['status' => 'ACTIVE']);
        Activity::factory()->create(['status' => 'ACTIVE']);
        Activity::factory()->create(['status' => 'COMPLETED']);
        Activity::factory()->create(['status' => 'CANCELLED']);

        $activeActivities = Activity::where('status', 'ACTIVE')->get();
        $this->assertCount(2, $activeActivities);

        $completedActivities = Activity::where('status', 'COMPLETED')->get();
        $this->assertCount(1, $completedActivities);

        $cancelledActivities = Activity::where('status', 'CANCELLED')->get();
        $this->assertCount(1, $cancelledActivities);
    }

    public function test_activity_scope_by_organization()
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        
        Activity::factory()->count(2)->create(['organization_id' => $organization1->id]);
        Activity::factory()->count(3)->create(['organization_id' => $organization2->id]);

        $org1Activities = Activity::where('organization_id', $organization1->id)->get();
        $this->assertCount(2, $org1Activities);

        $org2Activities = Activity::where('organization_id', $organization2->id)->get();
        $this->assertCount(3, $org2Activities);
    }

    public function test_activity_scope_by_creator()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Activity::factory()->count(2)->create(['created_by' => $user1->id]);
        Activity::factory()->count(3)->create(['created_by' => $user2->id]);

        $user1Activities = Activity::where('created_by', $user1->id)->get();
        $this->assertCount(2, $user1Activities);

        $user2Activities = Activity::where('created_by', $user2->id)->get();
        $this->assertCount(3, $user2Activities);
    }
}