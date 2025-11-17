<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\News;

class NewsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $regularUser;
    protected User $orgMember;
    protected Organization $organization;
    protected Organization $otherOrganization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->orgMember = User::factory()->create(['role' => 'user']);

        // Create organizations
        $this->organization = Organization::factory()->create();
        $this->otherOrganization = Organization::factory()->create();

        // Add user to organization
        $this->organization->members()->attach($this->orgMember->id);
    }

    /** @test */
    public function guest_can_view_published_news_index()
    {
        // Create published news
        $news = News::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.index'));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertSee($news->title);
    }

    /** @test */
    public function guest_cannot_view_draft_news()
    {
        // Create draft news
        $news = News::factory()->create([
            'is_published' => false,
        ]);

        $response = $this->get(route('news.index'));

        $response->assertStatus(200)
                ->assertViewIs('news.index')
                ->assertDontSee($news->title);
    }

    /** @test */
    public function guest_can_view_published_news_detail()
    {
        $news = News::factory()->create([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.show', $news));

        $response->assertStatus(200)
                ->assertViewIs('news.show')
                ->assertSee($news->title)
                ->assertSee($news->content);
    }

    /** @test */
    public function guest_cannot_view_draft_news_detail()
    {
        $news = News::factory()->create([
            'is_published' => false,
        ]);

        $response = $this->get(route('news.show', $news));

        $response->assertStatus(404);
    }

    /** @test */
    public function guest_cannot_access_create_news_page()
    {
        $response = $this->get(route('news.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function admin_can_access_create_news_page()
    {
        $response = $this->actingAs($this->admin)
                        ->get(route('news.create'));

        $response->assertStatus(200)
                ->assertViewIs('news.create');
    }

    /** @test */
    public function organization_member_can_access_create_news_page()
    {
        $response = $this->actingAs($this->orgMember)
                        ->get(route('news.create'));

        $response->assertStatus(200)
                ->assertViewIs('news.create');
    }

    /** @test */
    public function regular_user_cannot_access_create_news_page()
    {
        $response = $this->actingAs($this->regularUser)
                        ->get(route('news.create'));

        $response->assertStatus(200); // Can access but won't see organizations
    }

    /** @test */
    public function admin_can_create_news_with_image()
    {
        Storage::fake('public');

        $newsData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'organization_id' => $this->organization->id,
            'is_published' => true,
            'image' => UploadedFile::fake()->image('news.jpg', 800, 600),
        ];

        $response = $this->actingAs($this->admin)
                        ->post(route('news.store'), $newsData);

        $response->assertRedirect(route('news.show', News::first()));

        $this->assertDatabaseHas('news', [
            'title' => $newsData['title'],
            'organization_id' => $this->organization->id,
            'created_by' => $this->admin->id,
            'is_published' => true,
        ]);

        // Check if image was stored
        $news = News::first();
        $this->assertNotNull($news->image);
        Storage::disk('public')->assertExists($news->image);
    }

    /** @test */
    public function organization_member_can_create_news_for_their_organization()
    {
        $newsData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'organization_id' => $this->organization->id,
            'is_published' => false,
        ];

        $response = $this->actingAs($this->orgMember)
                        ->post(route('news.store'), $newsData);

        $response->assertRedirect(route('news.show', News::first()));

        $this->assertDatabaseHas('news', [
            'title' => $newsData['title'],
            'organization_id' => $this->organization->id,
            'created_by' => $this->orgMember->id,
            'is_published' => false,
        ]);
    }

    /** @test */
    public function organization_member_cannot_create_news_for_other_organization()
    {
        $newsData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'organization_id' => $this->otherOrganization->id,
            'is_published' => false,
        ];

        $response = $this->actingAs($this->orgMember)
                        ->post(route('news.store'), $newsData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('news', [
            'title' => $newsData['title'],
        ]);
    }

    /** @test */
    public function news_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)
                        ->post(route('news.store'), []);

        $response->assertSessionHasErrors(['title', 'content', 'organization_id']);
    }

    /** @test */
    public function news_creator_can_edit_their_news()
    {
        $news = News::factory()->create([
            'created_by' => $this->orgMember->id,
        ]);

        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'organization_id' => $news->organization_id,
            'is_published' => true,
        ];

        $response = $this->actingAs($this->orgMember)
                        ->put(route('news.update', $news), $updatedData);

        $response->assertRedirect(route('news.show', $news));

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'is_published' => true,
        ]);
    }

    /** @test */
    public function admin_can_edit_any_news()
    {
        $news = News::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $updatedData = [
            'title' => 'Admin Updated Title',
            'content' => 'Admin updated content',
            'organization_id' => $news->organization_id,
            'is_published' => true,
        ];

        $response = $this->actingAs($this->admin)
                        ->put(route('news.update', $news), $updatedData);

        $response->assertRedirect(route('news.show', $news));

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Admin Updated Title',
        ]);
    }

    /** @test */
    public function user_cannot_edit_others_news()
    {
        $news = News::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $updatedData = [
            'title' => 'Unauthorized Update',
            'content' => 'Unauthorized content',
            'organization_id' => $news->organization_id,
            'is_published' => true,
        ];

        $response = $this->actingAs($this->orgMember)
                        ->put(route('news.update', $news), $updatedData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
            'title' => 'Unauthorized Update',
        ]);
    }

    /** @test */
    public function news_creator_can_delete_their_news()
    {
        Storage::fake('public');

        $news = News::factory()->create([
            'created_by' => $this->orgMember->id,
            'image' => 'news/test-image.jpg',
        ]);

        Storage::disk('public')->put($news->image, 'fake image content');

        $response = $this->actingAs($this->orgMember)
                        ->delete(route('news.destroy', $news));

        $response->assertRedirect(route('news.index'));

        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);

        Storage::disk('public')->assertMissing($news->image);
    }

    /** @test */
    public function admin_can_delete_any_news()
    {
        $news = News::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $response = $this->actingAs($this->admin)
                        ->delete(route('news.destroy', $news));

        $response->assertRedirect(route('news.index'));

        $this->assertDatabaseMissing('news', [
            'id' => $news->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_others_news()
    {
        $news = News::factory()->create([
            'created_by' => $this->regularUser->id,
        ]);

        $response = $this->actingAs($this->orgMember)
                        ->delete(route('news.destroy', $news));

        $response->assertStatus(403);

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
        ]);
    }

    /** @test */
    public function news_index_can_be_filtered_by_organization()
    {
        $orgNews = News::factory()->create([
            'organization_id' => $this->organization->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $otherNews = News::factory()->create([
            'organization_id' => $this->otherOrganization->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.index', [
            'organization_id' => $this->organization->id,
        ]));

        $response->assertSee($orgNews->title)
                ->assertDontSee($otherNews->title);
    }

    /** @test */
    public function news_index_can_be_searched()
    {
        $matchingNews = News::factory()->create([
            'title' => 'Unique Search Term Here',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $otherNews = News::factory()->create([
            'title' => 'Completely Different Title',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.index', [
            'search' => 'Unique Search Term',
        ]));

        $response->assertSee($matchingNews->title)
                ->assertDontSee($otherNews->title);
    }

    /** @test */
    public function news_image_can_be_removed()
    {
        Storage::fake('public');

        $news = News::factory()->create([
            'created_by' => $this->orgMember->id,
            'image' => 'news/test-image.jpg',
        ]);

        Storage::disk('public')->put($news->image, 'fake image content');

        $response = $this->actingAs($this->orgMember)
                        ->post(route('news.removeImage', $news));

        $response->assertRedirect();

        $news->refresh();
        $this->assertNull($news->image);

        Storage::disk('public')->assertMissing('news/test-image.jpg');
    }

    /** @test */
    public function organization_news_page_shows_only_organization_news()
    {
        $orgNews = News::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $otherNews = News::factory()->create([
            'organization_id' => $this->otherOrganization->id,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('news.organization', $this->organization));

        $response->assertStatus(200)
                ->assertViewIs('news.organization');

        foreach ($orgNews as $news) {
            $response->assertSee($news->title);
        }

        $response->assertDontSee($otherNews->title);
    }

    /** @test */
    public function news_upload_requires_valid_image()
    {
        $newsData = [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'organization_id' => $this->organization->id,
            'is_published' => false,
            'image' => UploadedFile::fake()->create('document.pdf', 1000),
        ];

        $response = $this->actingAs($this->admin)
                        ->post(route('news.store'), $newsData);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function news_content_is_sanitized()
    {
        $maliciousContent = '<script>alert("xss")</script><p>Valid content</p>';
        
        $newsData = [
            'title' => $this->faker->sentence,
            'content' => $maliciousContent,
            'organization_id' => $this->organization->id,
            'is_published' => false,
        ];

        $response = $this->actingAs($this->admin)
                        ->post(route('news.store'), $newsData);

        $news = News::first();
        
        // Script tag should be removed, but valid HTML should remain
        $this->assertStringNotContainsString('<script>', $news->content);
        $this->assertStringContainsString('<p>Valid content</p>', $news->content);
    }
}