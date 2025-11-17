<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $admin;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create(['is_admin' => false]);
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->organization = Organization::factory()->create();
    }

    public function test_index_displays_activities()
    {
        Activity::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('activities.index'));

        $response->assertStatus(200)
            ->assertViewIs('activities.index')
            ->assertViewHas('activities');
    }

    public function test_create_displays_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('activities.create'));

        $response->assertStatus(200)
            ->assertViewIs('activities.create')
            ->assertViewHas('organizations');
    }

    public function test_store_creates_activity()
    {
        Storage::fake('public');

        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $this->organization->id,
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDay()->addHour()->format('Y-m-d\TH:i'),
            'location' => $this->faker->address,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('activities.store'), $activityData);

        $response->assertRedirect(route('activities.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activities', [
            'title' => $activityData['title'],
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_store_creates_activity_with_images()
    {
        Storage::fake('public');

        $files = [
            UploadedFile::fake()->image('test1.jpg'),
            UploadedFile::fake()->image('test2.png'),
        ];

        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $this->organization->id,
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDay()->addHour()->format('Y-m-d\TH:i'),
            'location' => $this->faker->address,
            'status' => 'published',
            'images' => $files,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('activities.store'), $activityData);

        $response->assertRedirect(route('activities.index'))
            ->assertSessionHas('success');

        $activity = Activity::first();
        $this->assertCount(2, $activity->images);
        
        // Check files were stored
        foreach ($activity->images as $image) {
            Storage::disk('public')->assertExists($image);
        }
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('activities.store'), []);

        $response->assertSessionHasErrors(['title', 'description', 'organization_id', 'start_date', 'end_date', 'location', 'status']);
    }

    public function test_store_validates_date_logic()
    {
        $activityData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'organization_id' => $this->organization->id,
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->format('Y-m-d\TH:i'), // Before start date
            'location' => $this->faker->address,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('activities.store'), $activityData);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_show_displays_activity()
    {
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->get(route('activities.show', $activity));

        $response->assertStatus(200)
            ->assertViewIs('activities.show')
            ->assertViewHas('activity', function ($viewActivity) use ($activity) {
                return $viewActivity->id === $activity->id;
            });
    }

    public function test_edit_displays_form_for_creator()
    {
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('activities.edit', $activity));

        $response->assertStatus(200)
            ->assertViewIs('activities.edit')
            ->assertViewHas('activity', $activity)
            ->assertViewHas('organizations');
    }

    public function test_edit_denied_for_non_creator()
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('activities.edit', $activity));

        $response->assertStatus(403);
    }

    public function test_edit_allowed_for_admin()
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('activities.edit', $activity));

        $response->assertStatus(200);
    }

    public function test_update_modifies_activity()
    {
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'organization_id' => $this->organization->id,
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDay()->addHour()->format('Y-m-d\TH:i'),
            'location' => 'Updated Location',
            'status' => 'completed',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('activities.update', $activity), $updateData);

        $response->assertRedirect(route('activities.show', $activity))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'title' => 'Updated Title',
            'status' => 'COMPLETED',
        ]);
    }

    public function test_update_denied_for_non_creator()
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('activities.update', $activity), [
                'title' => 'Updated Title',
            ]);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_activity()
    {
        Storage::fake('public');

        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'images' => ['activities/test.jpg'],
        ]);

        Storage::disk('public')->put('activities/test.jpg', 'test content');

        $response = $this->actingAs($this->user)
            ->delete(route('activities.destroy', $activity));

        $response->assertRedirect(route('activities.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        Storage::disk('public')->assertMissing('activities/test.jpg');
    }

    public function test_destroy_denied_for_non_creator()
    {
        $otherUser = User::factory()->create();
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('activities.destroy', $activity));

        $response->assertStatus(403);
    }

    public function test_update_status_changes_activity_status()
    {
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'DRAFT',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('activities.updateStatus', $activity), [
                'status' => 'published',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_remove_image_deletes_image_file()
    {
        Storage::fake('public');

        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'images' => ['activities/image1.jpg', 'activities/image2.jpg'],
        ]);

        Storage::disk('public')->put('activities/image1.jpg', 'test content 1');
        Storage::disk('public')->put('activities/image2.jpg', 'test content 2');

        $response = $this->actingAs($this->user)
            ->post(route('activities.removeImage', $activity), [
                'image_index' => 0,
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $activity->refresh();
        $this->assertEquals(['activities/image2.jpg'], $activity->images);
        Storage::disk('public')->assertMissing('activities/image1.jpg');
        Storage::disk('public')->assertExists('activities/image2.jpg');
    }

    public function test_calendar_displays_calendar_view()
    {
        Activity::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'PUBLISHED',
        ]);

        $response = $this->get(route('activities.calendar'));

        $response->assertStatus(200)
            ->assertViewIs('activities.calendar')
            ->assertViewHas('activities');
    }

    public function test_unauthenticated_users_can_view_activities()
    {
        $activity = Activity::factory()->create([
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'PUBLISHED',
        ]);

        $response = $this->get(route('activities.index'));
        $response->assertStatus(200);

        $response = $this->get(route('activities.show', $activity));
        $response->assertStatus(200);

        $response = $this->get(route('activities.calendar'));
        $response->assertStatus(200);
    }

    public function test_unauthenticated_users_cannot_create_activities()
    {
        $response = $this->get(route('activities.create'));
        $response->assertRedirect('/login');

        $response = $this->post(route('activities.store'), []);
        $response->assertRedirect('/login');
    }
}