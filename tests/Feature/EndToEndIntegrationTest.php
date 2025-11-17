<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EndToEndIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_complete_user_registration_and_organization_creation_workflow()
    {
        // Step 1: User registration
        $registrationData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $registrationData);
        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);

        // Step 2: User login
        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        $response->assertRedirect('/dashboard');

        // Step 3: Create organization
        $organizationData = [
            'name' => 'Test Organization',
            'type' => 'UKM',
            'description' => 'A test organization for testing purposes',
            'contact' => 'test@example.com',
            'social_media' => [
                'facebook' => 'https://facebook.com/testorg',
                'twitter' => 'https://twitter.com/testorg',
            ]
        ];

        $response = $this->actingAs($user)->post('/organizations', $organizationData);
        $response->assertRedirect('/organizations/' . Organization::first()->id);

        $organization = Organization::where('name', 'Test Organization')->first();
        $this->assertNotNull($organization);

        // Step 4: Verify user is associated with organization
        $user->refresh();
        $this->assertEquals($organization->id, $user->organization_id);
    }

    public function test_admin_user_management_workflow()
    {
        // Step 1: Create admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Step 2: Create regular users
        $users = User::factory()->count(3)->create(['role' => 'user']);

        // Step 3: Create organization
        $organization = Organization::factory()->create();

        // Step 4: Admin adds users to organization
        foreach ($users as $index => $user) {
            $response = $this->actingAs($admin)
                ->post('/organizations/' . $organization->id . '/add-member', [
                    'user_id' => $user->id,
                    'role' => $index === 0 ? 'org_admin' : 'member'
                ]);
            $response->assertRedirect('/organizations/' . $organization->id);
        }

        // Step 5: Verify users are added to organization
        $orgAdmin = $users->first();
        $orgAdmin->refresh();
        $this->assertEquals($organization->id, $orgAdmin->organization_id);
        $this->assertEquals('org_admin', $orgAdmin->role);

        $members = $users->skip(1);
        foreach ($members as $member) {
            $member->refresh();
            $this->assertEquals($organization->id, $member->organization_id);
            $this->assertEquals('member', $member->role);
        }
    }

    public function test_activity_creation_and_management_workflow()
    {
        // Step 1: Setup users and organization
        $orgAdmin = User::factory()->create(['role' => 'org_admin']);
        $organization = Organization::factory()->create();
        $orgAdmin->update(['organization_id' => $organization->id]);

        // Step 2: Create activity with images
        $images = [
            UploadedFile::fake()->image('activity1.jpg'),
            UploadedFile::fake()->image('activity2.jpg'),
        ];

        $activityData = [
            'title' => 'Tech Workshop 2024',
            'description' => 'A comprehensive workshop on modern web development technologies',
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(8)->format('Y-m-d H:i:s'),
            'location' => 'Computer Lab, Building A',
            'status' => 'draft',
            'images' => $images,
        ];

        $response = $this->actingAs($orgAdmin)->post('/activities', $activityData);
        $response->assertRedirect('/activities');

        $activity = Activity::where('title', 'Tech Workshop 2024')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($orgAdmin->id, $activity->created_by);
        $this->assertCount(2, $activity->images);

        // Step 3: Update activity status to published
        $response = $this->actingAs($orgAdmin)
            ->post('/activities/' . $activity->id . '/update-status', [
                'status' => 'published'
            ]);
        $response->assertRedirect();

        $activity->refresh();
        $this->assertEquals('published', $activity->status);

        // Step 4: Verify activity appears in calendar
        $response = $this->get('/activities/calendar');
        $response->assertStatus(200);
        $response->assertSee('Tech Workshop 2024');
    }

    public function test_announcement_creation_and_display_workflow()
    {
        // Step 1: Setup user
        $user = User::factory()->create();

        // Step 2: Create pinned announcement
        $announcementData = [
            'title' => 'Important System Maintenance',
            'content' => 'The system will be under maintenance this weekend. Please save your work.',
            'category' => 'system',
            'is_pinned' => true,
        ];

        $response = $this->actingAs($user)->post('/announcements', $announcementData);
        $response->assertRedirect('/announcements');

        $announcement = \App\Models\Announcement::where('title', 'Important System Maintenance')->first();
        $this->assertNotNull($announcement);
        $this->assertTrue($announcement->is_pinned);

        // Step 3: Create regular announcement
        $regularAnnouncementData = [
            'title' => 'Welcome New Members',
            'content' => 'We welcome all new members to our organization.',
            'category' => 'general',
            'is_pinned' => false,
        ];

        $response = $this->actingAs($user)->post('/announcements', $regularAnnouncementData);
        $response->assertRedirect('/announcements');

        // Step 4: Verify announcements appear in correct order
        $response = $this->get('/announcements');
        $response->assertStatus(200);
        
        // Pinned announcement should appear first
        $response->assertSeeInOrder(['Important System Maintenance', 'Welcome New Members']);
    }

    public function test_news_creation_and_publication_workflow()
    {
        // Step 1: Setup user and organization
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        // Step 2: Create news article
        $newsData = [
            'title' => 'Annual Report 2024',
            'content' => 'Our organization has achieved remarkable milestones this year...',
            'organization_id' => $organization->id,
            'is_published' => true,
            'published_at' => now(),
        ];

        $response = $this->actingAs($user)->post('/news', $newsData);
        $response->assertRedirect('/news');

        $news = News::where('title', 'Annual Report 2024')->first();
        $this->assertNotNull($news);
        $this->assertTrue($news->is_published);
        $this->assertEquals($user->id, $news->created_by);

        // Step 3: Create draft news
        $draftNewsData = [
            'title' => 'Upcoming Event Preview',
            'content' => 'Preview of our upcoming major event...',
            'organization_id' => $organization->id,
            'is_published' => false,
        ];

        $response = $this->actingAs($user)->post('/news', $draftNewsData);
        $response->assertRedirect('/news');

        // Step 4: Verify only published news appears publicly
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertSee('Annual Report 2024');
        $response->assertDontSee('Upcoming Event Preview');
    }

    public function test_file_upload_and_management_workflow()
    {
        // Step 1: Setup user and organization
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        // Step 2: Upload multiple files for activity
        $files = [
            UploadedFile::fake()->image('poster.jpg'),
            UploadedFile::fake()->create('document.pdf', 1024),
            UploadedFile::fake()->create('schedule.xlsx', 512),
        ];

        $activityData = [
            'title' => 'Conference 2024',
            'description' => 'Annual conference with multiple sessions',
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(15)->format('Y-m-d H:i:s'),
            'location' => 'Main Auditorium',
            'status' => 'published',
            'images' => $files,
        ];

        $response = $this->actingAs($user)->post('/activities', $activityData);
        $response->assertRedirect('/activities');

        $activity = Activity::where('title', 'Conference 2024')->first();
        $this->assertNotNull($activity);
        $this->assertCount(3, $activity->images);

        // Step 3: Remove one file
        $response = $this->actingAs($user)
            ->post('/activities/' . $activity->id . '/remove-image', [
                'image_index' => 1 // Remove document.pdf
            ]);
        $response->assertRedirect();

        $activity->refresh();
        $this->assertCount(2, $activity->images);

        // Step 4: Verify remaining files are accessible
        foreach ($activity->images as $image) {
            Storage::disk('public')->assertExists($image);
        }
    }

    public function test_user_role_based_access_control_workflow()
    {
        // Step 1: Create users with different roles
        $admin = User::factory()->create(['role' => 'admin']);
        $orgAdmin = User::factory()->create(['role' => 'org_admin']);
        $regularUser = User::factory()->create(['role' => 'user']);
        $organization = Organization::factory()->create();
        
        $orgAdmin->update(['organization_id' => $organization->id]);
        $regularUser->update(['organization_id' => $organization->id]);

        // Step 2: Create activity as org admin
        $activity = Activity::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $orgAdmin->id
        ]);

        // Step 3: Test access permissions
        // Admin can edit any activity
        $response = $this->actingAs($admin)->get('/activities/' . $activity->id . '/edit');
        $response->assertStatus(200);

        // Org admin can edit their own activity
        $response = $this->actingAs($orgAdmin)->get('/activities/' . $activity->id . '/edit');
        $response->assertStatus(200);

        // Regular user cannot edit activity
        $response = $this->actingAs($regularUser)->get('/activities/' . $activity->id . '/edit');
        $response->assertStatus(403);

        // Step 4: Test organization management
        // Admin can delete organization
        $response = $this->actingAs($admin)->delete('/organizations/' . $organization->id);
        $response->assertRedirect('/organizations');

        // Org admin cannot delete organization
        $newOrganization = Organization::factory()->create();
        $response = $this->actingAs($orgAdmin)->delete('/organizations/' . $newOrganization->id);
        $response->assertStatus(403);
    }

    public function test_search_and_filtering_workflow()
    {
        // Step 1: Create diverse test data
        $organizations = Organization::factory()->count(5)->create();
        $activities = Activity::factory()->count(10)->create();
        $announcements = Announcement::factory()->count(8)->create();
        $news = News::factory()->count(6)->create(['is_published' => true]);

        // Step 2: Test organization search
        $searchTerm = $organizations->first()->name;
        $response = $this->get('/organizations?search=' . urlencode($searchTerm));
        $response->assertStatus(200);
        $response->assertSee($searchTerm);

        // Step 3: Test activity filtering by date
        $today = now()->format('Y-m-d');
        $response = $this->get('/activities?date=' . $today);
        $response->assertStatus(200);

        // Step 4: Test announcement category filtering
        $response = $this->get('/announcements?category=academic');
        $response->assertStatus(200);

        // Step 5: Test news organization filtering
        $orgId = $news->first()->organization_id;
        $response = $this->get('/news?organization=' . $orgId);
        $response->assertStatus(200);
    }

    public function test_complete_user_journey_workflow()
    {
        // Step 1: User registration
        $userData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $userData);
        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'jane@example.com')->first();

        // Step 2: Login and explore dashboard
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        // Step 3: Browse organizations
        $response = $this->actingAs($user)->get('/organizations');
        $response->assertStatus(200);

        // Step 4: Browse activities
        $response = $this->actingAs($user)->get('/activities');
        $response->assertStatus(200);

        // Step 5: View calendar
        $response = $this->actingAs($user)->get('/activities/calendar');
        $response->assertStatus(200);

        // Step 6: Read announcements
        $response = $this->actingAs($user)->get('/announcements');
        $response->assertStatus(200);

        // Step 7: Read news
        $response = $this->actingAs($user)->get('/news');
        $response->assertStatus(200);

        // Step 8: Logout
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');

        // Step 9: Verify logout
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_error_handling_and_validation_workflow()
    {
        // Step 1: Test registration validation
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);

        // Step 2: Test login with invalid credentials
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);

        // Step 3: Test organization creation validation
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/organizations', [
            'name' => '',
            'type' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'type']);

        // Step 4: Test activity creation validation
        $organization = Organization::factory()->create();
        $response = $this->actingAs($user)->post('/activities', [
            'title' => '',
            'description' => '',
            'organization_id' => $organization->id,
            'start_date' => now()->subDays(1)->format('Y-m-d H:i:s'), // Past date
            'end_date' => now()->subDays(2)->format('Y-m-d H:i:s'), // Before start date
            'location' => '',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['title', 'description', 'start_date', 'end_date', 'location', 'status']);
    }
}