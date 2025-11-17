<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AnnouncementControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function guest_can_view_announcements_index()
    {
        $announcement = Announcement::factory()->create([
            'title' => 'Test Announcement',
            'content' => 'This is a test announcement content.',
        ]);

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(200)
                ->assertSee($announcement->title)
                ->assertSee('Test Announcement');
    }

    /** @test */
    public function guest_can_view_single_announcement()
    {
        $announcement = Announcement::factory()->create([
            'title' => 'Test Announcement',
            'content' => 'This is a test announcement content.',
        ]);

        $response = $this->get(route('announcements.show', $announcement));

        $response->assertStatus(200)
                ->assertSee($announcement->title)
                ->assertSee($announcement->content);
    }

    /** @test */
    public function guest_cannot_access_create_announcement()
    {
        $response = $this->get(route('announcements.create'));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function regular_user_cannot_access_create_announcement()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('announcements.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_create_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('announcements.create'));

        $response->assertStatus(200)
                ->assertSee('Buat Pengumuman Baru');
    }

    /** @test */
    public function org_admin_can_access_create_announcement()
    {
        $user = User::factory()->create(['role' => 'org_admin']);

        $response = $this->actingAs($user)->get(route('announcements.create'));

        $response->assertStatus(200)
                ->assertSee('Buat Pengumuman Baru');
    }

    /** @test */
    public function admin_can_create_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $announcementData = [
            'title' => 'New Test Announcement',
            'content' => 'This is the content of the new announcement.',
            'category' => 'Test Category',
            'is_pinned' => true,
        ];

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), $announcementData);

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('announcements', [
            'title' => 'New Test Announcement',
            'content' => 'This is the content of the new announcement.',
            'category' => 'Test Category',
            'is_pinned' => true,
            'created_by' => $user->id,
        ]);
    }

    /** @test */
    public function admin_can_create_announcement_with_image()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->image('announcement.jpg');

        $announcementData = [
            'title' => 'Announcement with Image',
            'content' => 'This announcement has an image.',
            'image' => $file,
        ];

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), $announcementData);

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $announcement = Announcement::first();
        $this->assertNotNull($announcement->image);
        Storage::disk('public')->assertExists($announcement->image);
    }

    /** @test */
    public function guest_cannot_store_announcement()
    {
        $announcementData = [
            'title' => 'Test Announcement',
            'content' => 'Test content',
        ];

        $response = $this->post(route('announcements.store'), $announcementData);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('announcements', $announcementData);
    }

    /** @test */
    public function regular_user_cannot_store_announcement()
    {
        $user = User::factory()->create(['role' => 'user']);

        $announcementData = [
            'title' => 'Test Announcement',
            'content' => 'Test content',
        ];

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), $announcementData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('announcements', $announcementData);
    }

    /** @test */
    public function announcement_requires_title_and_content()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), [
                            'title' => '',
                            'content' => '',
                        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    /** @test */
    public function admin_can_edit_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();

        $response = $this->actingAs($user)
                        ->get(route('announcements.edit', $announcement));

        $response->assertStatus(200)
                ->assertSee($announcement->title)
                ->assertSee($announcement->content);
    }

    /** @test */
    public function guest_cannot_edit_announcement()
    {
        $announcement = Announcement::factory()->create();

        $response = $this->get(route('announcements.edit', $announcement));

        $response->assertRedirect('/login');
    }

    /** @test */
    public function regular_user_cannot_edit_announcement()
    {
        $user = User::factory()->create(['role' => 'user']);
        $announcement = Announcement::factory()->create();

        $response = $this->actingAs($user)
                        ->get(route('announcements.edit', $announcement));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create([
            'title' => 'Original Title',
            'content' => 'Original content',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'category' => 'Updated Category',
            'is_pinned' => true,
        ];

        $response = $this->actingAs($user)
                        ->put(route('announcements.update', $announcement), $updateData);

        $response->assertRedirect(route('announcements.show', $announcement))
                ->assertSessionHas('success');

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'category' => 'Updated Category',
            'is_pinned' => true,
        ]);
    }

    /** @test */
    public function admin_can_delete_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();

        $response = $this->actingAs($user)
                        ->delete(route('announcements.destroy', $announcement));

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }

    /** @test */
    public function guest_cannot_delete_announcement()
    {
        $announcement = Announcement::factory()->create();

        $response = $this->delete(route('announcements.destroy', $announcement));

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
        ]);
    }

    /** @test */
    public function regular_user_cannot_delete_announcement()
    {
        $user = User::factory()->create(['role' => 'user']);
        $announcement = Announcement::factory()->create();

        $response = $this->actingAs($user)
                        ->delete(route('announcements.destroy', $announcement));

        $response->assertStatus(403);
        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
        ]);
    }

    /** @test */
    public function pinned_announcements_appear_first()
    {
        // Create announcements in specific order
        $unpinned1 = Announcement::factory()->create([
            'title' => 'Unpinned 1',
            'is_pinned' => false,
            'created_at' => now()->subDays(3),
        ]);
        
        $pinned = Announcement::factory()->create([
            'title' => 'Pinned Announcement',
            'is_pinned' => true,
            'created_at' => now()->subDays(2),
        ]);
        
        $unpinned2 = Announcement::factory()->create([
            'title' => 'Unpinned 2',
            'is_pinned' => false,
            'created_at' => now()->subDays(1),
        ]);

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(200);
        
        // Check that pinned announcement appears first
        $content = $response->getContent();
        $pinnedPos = strpos($content, 'Pinned Announcement');
        $unpinned1Pos = strpos($content, 'Unpinned 1');
        $unpinned2Pos = strpos($content, 'Unpinned 2');
        
        $this->assertLessThan($unpinned1Pos, $pinnedPos);
        $this->assertLessThan($unpinned2Pos, $pinnedPos);
    }

    /** @test */
    public function announcement_image_upload_validation()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Test with invalid file type
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), [
                            'title' => 'Test Announcement',
                            'content' => 'Test content',
                            'image' => $file,
                        ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function announcement_image_size_validation()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Test with file larger than 2MB
        $file = UploadedFile::fake()->image('large-image.jpg')->size(3000);

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), [
                            'title' => 'Test Announcement',
                            'content' => 'Test content',
                            'image' => $file,
                        ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function html_content_is_sanitized_on_creation()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $maliciousContent = '<script>alert("XSS")</script><p>Safe content</p><img src="x" onerror="alert(\'XSS\')">';
        $announcementData = [
            'title' => 'Test Announcement',
            'content' => $maliciousContent,
        ];

        $response = $this->actingAs($user)
                        ->post(route('announcements.store'), $announcementData);

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $announcement = Announcement::first();
        
        // Check that script tags are removed
        $this->assertStringNotContainsString('<script>', $announcement->content);
        $this->assertStringNotContainsString('onerror', $announcement->content);
        
        // Check that safe content is preserved
        $this->assertStringContainsString('<p>Safe content</p>', $announcement->content);
    }

    /** @test */
    public function html_content_is_sanitized_on_update()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        
        $maliciousContent = '<script>alert("XSS")</script><p>Updated safe content</p><div style="color:red;">Styled content</div>';
        $updateData = [
            'title' => 'Updated Title',
            'content' => $maliciousContent,
        ];

        $response = $this->actingAs($user)
                        ->put(route('announcements.update', $announcement), $updateData);

        $response->assertRedirect(route('announcements.show', $announcement))
                ->assertSessionHas('success');

        $announcement->refresh();
        
        // Check that script tags are removed
        $this->assertStringNotContainsString('<script>', $announcement->content);
        
        // Check that safe content is preserved
        $this->assertStringContainsString('<p>Updated safe content</p>', $announcement->content);
        $this->assertStringContainsString('<div style="color:red;">Styled content</div>', $announcement->content);
    }

    /** @test */
    public function announcement_creation_is_rate_limited()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $announcementData = [
            'title' => 'Test Announcement',
            'content' => 'Test content',
        ];

        // Make 6 requests (limit is 5 per minute)
        $responses = [];
        for ($i = 0; $i < 6; $i++) {
            $responses[] = $this->actingAs($user)
                            ->post(route('announcements.store'), $announcementData);
        }

        // First 5 should succeed
        for ($i = 0; $i < 5; $i++) {
            $responses[$i]->assertRedirect(route('announcements.index'));
        }

        // 6th should be rate limited
        $responses[5]->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function announcement_update_is_rate_limited()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ];

        // Make 6 requests (limit is 5 per minute)
        $responses = [];
        for ($i = 0; $i < 6; $i++) {
            $responses[] = $this->actingAs($user)
                            ->put(route('announcements.update', $announcement), $updateData);
        }

        // First 5 should succeed
        for ($i = 0; $i < 5; $i++) {
            $responses[$i]->assertRedirect(route('announcements.show', $announcement));
        }

        // 6th should be rate limited
        $responses[5]->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function admin_can_view_trashed_announcements()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        $announcement->delete();

        $response = $this->actingAs($user)
                        ->get(route('announcements.trashed'));

        $response->assertStatus(200)
                ->assertSee($announcement->title);
    }

    /** @test */
    public function admin_can_restore_soft_deleted_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        $announcement->delete();

        $response = $this->actingAs($user)
                        ->post(route('announcements.restore', $announcement->id));

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $this->assertNull($announcement->fresh()->deleted_at);
    }

    /** @test */
    public function admin_can_force_delete_announcement()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        $announcement->delete();

        $response = $this->actingAs($user)
                        ->delete(route('announcements.forceDelete', $announcement->id));

        $response->assertRedirect(route('announcements.index'))
                ->assertSessionHas('success');

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }

    /** @test */
    public function soft_deleted_announcements_do_not_appear_in_index()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $announcement = Announcement::factory()->create();
        $announcement->delete();

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(200)
                ->assertDontSee($announcement->title);
    }
}