<?php

namespace Tests\Unit;

use App\Models\News;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_belongs_to_organization()
    {
        $organization = Organization::factory()->create();
        $news = News::factory()->create(['organization_id' => $organization->id]);

        $this->assertInstanceOf(Organization::class, $news->organization);
        $this->assertEquals($organization->id, $news->organization->id);
    }

    public function test_news_belongs_to_creator()
    {
        $user = User::factory()->create();
        $news = News::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $news->creator);
        $this->assertEquals($user->id, $news->creator->id);
    }

    public function test_news_fillable_fields()
    {
        $fillable = [
            'title', 'content', 'image', 'organization_id', 
            'is_published', 'published_at', 'created_by'
        ];

        $news = new News();
        $this->assertEquals($fillable, $news->getFillable());
    }

    public function test_news_is_published_casts_to_boolean()
    {
        $news = News::factory()->create(['is_published' => true]);
        $this->assertIsBool($news->is_published);
        $this->assertTrue($news->is_published);

        $news = News::factory()->create(['is_published' => false]);
        $this->assertIsBool($news->is_published);
        $this->assertFalse($news->is_published);
    }

    public function test_news_published_at_casts_to_datetime()
    {
        $publishedAt = now();

        $news = News::factory()->create([
            'is_published' => true,
            'published_at' => $publishedAt
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $news->published_at);
        $this->assertEquals($publishedAt->format('Y-m-d H:i:s'), $news->published_at->format('Y-m-d H:i:s'));
    }

    public function test_news_can_have_null_organization()
    {
        $user = User::factory()->create();
        $news = News::factory()->create([
            'organization_id' => null,
            'created_by' => $user->id
        ]);

        $this->assertNull($news->organization_id);
        $this->assertNull($news->organization);
    }
}