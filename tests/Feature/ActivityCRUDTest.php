<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityCRUDTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_activity_index_page_can_be_rendered()
    {
        $response = $this->get('/activities');

        $response->assertStatus(200);
        $response->assertViewIs('activities.index');
        $response->assertViewHas('activities');
    }

    public function test_activity_create_page_can_be_rendered_by_authenticated_user()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/activities/create');

        $response->assertStatus(200);
        $response->assertViewIs('activities.create');
        $response->assertViewHas('organizations');
    }

    public function test_activity_create_page_redirects_unauthenticated_user()
    {
        $response = $this->get('/activities/create');

        $response->assertRedirect('/login');
    }

    public function test_activity_can_be_stored_with_valid_data()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => $this->faker->address,
            'status' => 'draft',
        ];

        $response = $this->actingAs($user)->post('/activities', $activityData);

        $this->assertDatabaseHas('activities', [
            'title' => $activityData['title'],
            'description' => $activityData['description'],
            'organization_id' => $organization->id,
            'created_by' => $user->id,
            'status' => 'draft',
        ]);

        $response->assertRedirect('/activities');
        $response->assertSessionHas('success', 'Activity created successfully!');
    }

    public function test_activity_can_be_stored_with_images()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        
        $files = [
            UploadedFile::fake()->image('activity1.jpg'),
            UploadedFile::fake()->image('activity2.jpg'),
        ];
        
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => $this->faker->address,
            'status' => 'draft',
            'images' => $files,
        ];

        $response = $this->actingAs($user)->post('/activities', $activityData);

        $activity = Activity::first();
        $this->assertNotNull($activity->images);
        $this->assertCount(2, $activity->images);

        foreach ($activity->images as $image) {
            Storage::disk('public')->assertExists($image);
        }

        $response->assertRedirect('/activities');
    }

    public function test_activity_store_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        
        $invalidData = [
            'title' => '', // Required field missing
            'description' => '', // Required field missing
            'organization_id' => 999, // Non-existent organization
            'start_date' => now()->subDays(1)->format('Y-m-d H:i:s'), // Past date
            'end_date' => now()->subDays(2)->format('Y-m-d H:i:s'), // Before start date
            'location' => '', // Required field missing
            'status' => 'invalid_status', // Invalid status
        ];

        $response = $this->actingAs($user)->post('/activities', $invalidData);

        $response->assertSessionHasErrors([
            'title', 'description', 'organization_id', 
            'start_date', 'end_date', 'location', 'status'
        ]);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_activity_store_fails_with_too_many_images()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        
        $files = [];
        for ($i = 0; $i < 6; $i++) { // More than max 5
            $files[] = UploadedFile::fake()->image("activity{$i}.jpg");
        }
        
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => $this->faker->address,
            'status' => 'draft',
            'images' => $files,
        ];

        $response = $this->actingAs($user)->post('/activities', $activityData);

        $response->assertSessionHasErrors(['images']);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_activity_show_page_displays_activity_details()
    {
        $activity = Activity::factory()->create();

        $response = $this->get('/activities/' . $activity->id);

        $response->assertStatus(200);
        $response->assertViewIs('activities.show');
        $response->assertViewHas('activity', function ($viewActivity) use ($activity) {
            return $viewActivity->id === $activity->id;
        });
    }

    public function test_activity_show_page_returns_404_for_nonexistent_activity()
    {
        $response = $this->get('/activities/999');

        $response->assertStatus(404);
    }

    public function test_activity_edit_page_can_be_rendered_by_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->get('/activities/' . $activity->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('activities.edit');
        $response->assertViewHas('activity', $activity);
        $response->assertViewHas('organizations');
    }

    public function test_activity_edit_page_can_be_rendered_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::factory()->create();

        $response = $this->actingAs($admin)->get('/activities/' . $activity->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('activities.edit');
    }

    public function test_activity_edit_page_denied_for_non_creator_non_admin()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($user)->get('/activities/' . $activity->id . '/edit');

        $response->assertStatus(403);
    }

    public function test_activity_can_be_updated_by_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id]);
        $newOrganization = Organization::factory()->create();
        
        $updateData = [
            'title' => 'Updated Activity Title',
            'description' => 'Updated description',
            'organization_id' => $newOrganization->id,
            'start_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'location' => 'Updated location',
            'status' => 'published',
        ];

        $response = $this->actingAs($user)
            ->put('/activities/' . $activity->id, $updateData);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Updated Activity Title',
            'description' => 'Updated description',
            'organization_id' => $newOrganization->id,
            'location' => 'Updated location',
            'status' => 'published',
        ]);

        $response->assertRedirect('/activities/' . $activity->id);
        $response->assertSessionHas('success', 'Activity updated successfully!');
    }

    public function test_activity_can_be_updated_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::factory()->create();
        $newOrganization = Organization::factory()->create();
        
        $updateData = [
            'title' => 'Updated Activity Title',
            'description' => 'Updated description',
            'organization_id' => $newOrganization->id,
            'start_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'location' => 'Updated location',
            'status' => 'published',
        ];

        $response = $this->actingAs($admin)
            ->put('/activities/' . $activity->id, $updateData);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Updated Activity Title',
            'description' => 'Updated description',
            'organization_id' => $newOrganization->id,
            'location' => 'Updated location',
            'status' => 'published',
        ]);

        $response->assertRedirect('/activities/' . $activity->id);
    }

    public function test_activity_update_denied_for_non_creator_non_admin()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $otherUser->id]);
        
        $updateData = [
            'title' => 'Updated Activity Title',
            'description' => 'Updated description',
            'organization_id' => $activity->organization_id,
            'start_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'location' => 'Updated location',
            'status' => 'published',
        ];

        $response = $this->actingAs($user)
            ->put('/activities/' . $activity->id, $updateData);

        $response->assertStatus(403);
    }

    public function test_activity_can_be_deleted_by_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)
            ->delete('/activities/' . $activity->id);

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        $response->assertRedirect('/activities');
        $response->assertSessionHas('success', 'Activity deleted successfully!');
    }

    public function test_activity_can_be_deleted_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::factory()->create();

        $response = $this->actingAs($admin)
            ->delete('/activities/' . $activity->id);

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        $response->assertRedirect('/activities');
    }

    public function test_activity_delete_denied_for_non_creator_non_admin()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $otherUser->id]);

        $response = $this->actingAs($user)
            ->delete('/activities/' . $activity->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
    }

    public function test_activity_delete_removes_images()
    {
        $user = User::factory()->create();
        $files = [
            UploadedFile::fake()->image('activity1.jpg'),
            UploadedFile::fake()->image('activity2.jpg'),
        ];
        
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => Organization::factory()->create()->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => $this->faker->address,
            'status' => 'draft',
            'images' => $files,
        ];

        $this->actingAs($user)->post('/activities', $activityData);
        $activity = Activity::first();

        // Verify images exist
        foreach ($activity->images as $image) {
            Storage::disk('public')->assertExists($image);
        }

        // Delete activity
        $this->actingAs($user)->delete('/activities/' . $activity->id);

        // Verify images are deleted
        foreach ($activity->images as $image) {
            Storage::disk('public')->assertMissing($image);
        }
    }

    public function test_activity_status_can_be_updated_by_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id, 'status' => 'draft']);

        $response = $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/update-status', [
                'status' => 'published'
            ]);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => 'published'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Activity status updated successfully!');
    }

    public function test_activity_status_can_be_updated_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $activity = Activity::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($admin)
            ->post('/activities/' . $activity->id . '/update-status', [
                'status' => 'published'
            ]);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => 'published'
        ]);

        $response->assertRedirect();
    }

    public function test_activity_status_update_denied_for_non_creator_non_admin()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $otherUser->id, 'status' => 'draft']);

        $response = $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/update-status', [
                'status' => 'published'
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => 'draft'
        ]);
    }

    public function test_activity_image_can_be_removed_by_creator()
    {
        $user = User::factory()->create();
        $files = [UploadedFile::fake()->image('activity.jpg')];
        
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => Organization::factory()->create()->id,
            'start_date' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'location' => $this->faker->address,
            'status' => 'draft',
            'images' => $files,
        ];

        $this->actingAs($user)->post('/activities', $activityData);
        $activity = Activity::first();
        $imagePath = $activity->images[0];

        // Verify image exists
        Storage::disk('public')->assertExists($imagePath);

        // Remove image
        $response = $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/remove-image', [
                'image_index' => 0
            ]);

        // Verify image is removed from database and storage
        $activity->refresh();
        $this->assertEmpty($activity->images);
        Storage::disk('public')->assertMissing($imagePath);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Image removed successfully!');
    }

    public function test_activity_calendar_page_displays_published_activities()
    {
        Activity::factory()->create(['status' => 'published']);
        Activity::factory()->create(['status' => 'draft']);
        Activity::factory()->create(['status' => 'cancelled']);

        $response = $this->get('/activities/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('activities.calendar');
        $response->assertViewHas('activities', function ($activities) {
            return $activities->every('status', 'published');
        });
    }

    public function test_activities_are_ordered_by_start_date()
    {
        $activity1 = Activity::factory()->create([
            'start_date' => now()->addDays(3),
            'status' => 'published'
        ]);
        $activity2 = Activity::factory()->create([
            'start_date' => now()->addDays(1),
            'status' => 'published'
        ]);
        $activity3 = Activity::factory()->create([
            'start_date' => now()->addDays(2),
            'status' => 'published'
        ]);

        $response = $this->get('/activities/calendar');

        $response->assertViewHas('activities', function ($activities) use ($activity2, $activity3, $activity1) {
            return $activities->get(0)->id === $activity2->id &&
                   $activities->get(1)->id === $activity3->id &&
                   $activities->get(2)->id === $activity1->id;
        });
    }

    public function test_activity_pagination_works()
    {
        Activity::factory()->count(15)->create();

        $response = $this->get('/activities');

        $response->assertStatus(200);
        $response->assertViewHas('activities', function ($activities) {
            return $activities->hasPages();
        });
    }
}