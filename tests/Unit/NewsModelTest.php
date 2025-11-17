<?php

namespace Tests\Unit;

use App\Models\News;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\StorageService;

class NewsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_can_be_created_with_factory()
    {
        $news = News::factory()->create();
        
        $this->assertInstanceOf(News::class, $news);
        $this->assertNotNull($news->id);
        $this->assertNotNull($news->title);
        $this->assertNotNull($news->content);
    }

    public function test_news_fillable_attributes()
    {
        $fillable = [
            'title',
            'content',
            'image',
            'organization_id',
            'is_published',
            'published_at',
            'created_by',
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
        $publishedAt = '2024-12-01 10:00:00';
        $news = News::factory()->create(['published_at' => $publishedAt]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $news->published_at);
        $this->assertEquals($publishedAt, $news->published_at->format('Y-m-d H:i:s'));
    }

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

    public function test_news_image_url_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/news.jpg';
        $news = News::factory()->create(['image' => $fullUrl]);

        $this->assertEquals($fullUrl, $news->image_url);
    }

    public function test_news_image_url_attribute_with_storage_path()
    {
        $storagePath = '/storage/news/news.jpg';
        $news = News::factory()->create(['image' => $storagePath]);

        $expectedUrl = config('app.url') . $storagePath;
        $this->assertEquals($expectedUrl, $news->image_url);
    }

    public function test_news_image_url_attribute_with_filename()
    {
        $filename = 'news.jpg';
        $news = News::factory()->create(['image' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('getFileUrl')
            ->with($filename, 'news')
            ->willReturn('https://example.com/storage/news/' . $filename);

        $this->app->instance(StorageService::class, $storageService);

        $this->assertEquals('https://example.com/storage/news/' . $filename, $news->image_url);
    }

    public function test_news_image_url_attribute_with_null_image()
    {
        $news = News::factory()->create(['image' => null]);

        $this->assertNull($news->image_url);
    }

    public function test_news_image_path_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/news.jpg';
        $news = News::factory()->create(['image' => $fullUrl]);

        $this->assertEquals('news.jpg', $news->image_path);
    }

    public function test_news_image_path_attribute_with_storage_path()
    {
        $storagePath = '/storage/news/news.jpg';
        $news = News::factory()->create(['image' => $storagePath]);

        $this->assertEquals('news.jpg', $news->image_path);
    }

    public function test_news_image_path_attribute_with_filename()
    {
        $filename = 'news.jpg';
        $news = News::factory()->create(['image' => $filename]);

        $this->assertEquals($filename, $news->image_path);
    }

    public function test_news_image_path_attribute_with_null_image()
    {
        $news = News::factory()->create(['image' => null]);

        $this->assertNull($news->image_path);
    }

    public function test_news_deletion_deletes_image_file()
    {
        $filename = 'news.jpg';
        $news = News::factory()->create(['image' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('deleteFile')
            ->with($filename, 'news');

        $this->app->instance(StorageService::class, $storageService);

        $news->delete();
    }

    public function test_news_deletion_without_image()
    {
        $news = News::factory()->create(['image' => null]);

        // Mock the StorageService to ensure deleteFile is not called
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->never())
            ->method('deleteFile');

        $this->app->instance(StorageService::class, $storageService);

        $news->delete();
    }

    public function test_news_scope_published()
    {
        News::factory()->create(['is_published' => true]);
        News::factory()->create(['is_published' => true]);
        News::factory()->create(['is_published' => false]);
        News::factory()->create(['is_published' => false]);

        $publishedNews = News::where('is_published', true)->get();
        $this->assertCount(2, $publishedNews);

        $unpublishedNews = News::where('is_published', false)->get();
        $this->assertCount(2, $unpublishedNews);
    }

    public function test_news_scope_by_organization()
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        
        News::factory()->count(2)->create(['organization_id' => $organization1->id]);
        News::factory()->count(3)->create(['organization_id' => $organization2->id]);

        $org1News = News::where('organization_id', $organization1->id)->get();
        $this->assertCount(2, $org1News);

        $org2News = News::where('organization_id', $organization2->id)->get();
        $this->assertCount(3, $org2News);
    }

    public function test_news_scope_by_creator()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        News::factory()->count(2)->create(['created_by' => $user1->id]);
        News::factory()->count(3)->create(['created_by' => $user2->id]);

        $user1News = News::where('created_by', $user1->id)->get();
        $this->assertCount(2, $user1News);

        $user2News = News::where('created_by', $user2->id)->get();
        $this->assertCount(3, $user2News);
    }

    public function test_news_scope_published_before()
    {
        $date1 = now()->subDays(10);
        $date2 = now()->subDays(5);
        $date3 = now()->addDays(5);
        
        News::factory()->create(['published_at' => $date1]);
        News::factory()->create(['published_at' => $date2]);
        News::factory()->create(['published_at' => $date3]);

        $newsBeforeNow = News::where('published_at', '<=', now())->get();
        $this->assertCount(2, $newsBeforeNow);

        $newsBeforeDate2 = News::where('published_at', '<=', $date2)->get();
        $this->assertCount(2, $newsBeforeDate2);

        $newsBeforeDate1 = News::where('published_at', '<=', $date1)->get();
        $this->assertCount(1, $newsBeforeDate1);
    }

    public function test_news_scope_published_after()
    {
        $date1 = now()->subDays(10);
        $date2 = now()->subDays(5);
        $date3 = now()->addDays(5);
        
        News::factory()->create(['published_at' => $date1]);
        News::factory()->create(['published_at' => $date2]);
        News::factory()->create(['published_at' => $date3]);

        $newsAfterNow = News::where('published_at', '>=', now())->get();
        $this->assertCount(1, $newsAfterNow);

        $newsAfterDate2 = News::where('published_at', '>=', $date2)->get();
        $this->assertCount(2, $newsAfterDate2);

        $newsAfterDate1 = News::where('published_at', '>=', $date1)->get();
        $this->assertCount(3, $newsAfterDate1);
    }

    public function test_news_without_organization()
    {
        $news = News::factory()->create(['organization_id' => null]);

        $this->assertNull($news->organization);
    }

    public function test_news_without_creator()
    {
        $news = News::factory()->create(['created_by' => null]);

        $this->assertNull($news->creator);
    }

    public function test_news_ordering_by_published_at()
    {
        $date1 = now()->subDays(10);
        $date2 = now()->subDays(5);
        $date3 = now();
        
        $news1 = News::factory()->create(['published_at' => $date1]);
        $news2 = News::factory()->create(['published_at' => $date2]);
        $news3 = News::factory()->create(['published_at' => $date3]);

        $orderedNews = News::orderBy('published_at', 'desc')->get();
        
        $this->assertEquals($news3->id, $orderedNews->first()->id);
        $this->assertEquals($news2->id, $orderedNews->skip(1)->first()->id);
        $this->assertEquals($news1->id, $orderedNews->last()->id);
    }
}