<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\User;
use App\Models\Activity;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;

class OrganizationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_be_created_with_factory()
    {
        $organization = Organization::factory()->create();
        
        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertNotNull($organization->id);
        $this->assertNotNull($organization->name);
        $this->assertNotNull($organization->type);
    }

    public function test_organization_fillable_attributes()
    {
        $fillable = [
            'name',
            'type',
            'description',
            'logo',
            'contact',
            'social_media',
        ];

        $organization = new Organization();
        $this->assertEquals($fillable, $organization->getFillable());
    }

    public function test_organization_social_media_casts_to_array()
    {
        $socialMedia = [
            'instagram' => 'https://instagram.com/example',
            'twitter' => 'https://twitter.com/example',
            'facebook' => 'https://facebook.com/example',
        ];

        $organization = Organization::factory()->create(['social_media' => $socialMedia]);

        $this->assertIsArray($organization->social_media);
        $this->assertEquals($socialMedia, $organization->social_media);
    }

    public function test_organization_has_many_users()
    {
        $organization = Organization::factory()->create();
        $users = User::factory()->count(3)->create(['organization_id' => $organization->id]);

        $this->assertCount(3, $organization->members);
        $this->assertEquals($users->pluck('id'), $organization->members->pluck('id'));
    }

    public function test_organization_has_many_activities()
    {
        $organization = Organization::factory()->create();
        $activities = Activity::factory()->count(2)->create(['organization_id' => $organization->id]);

        $this->assertCount(2, $organization->activities);
        $this->assertEquals($activities->pluck('id'), $organization->activities->pluck('id'));
    }

    public function test_organization_has_many_news()
    {
        $organization = Organization::factory()->create();
        $news = News::factory()->count(4)->create(['organization_id' => $organization->id]);

        $this->assertCount(4, $organization->news);
        $this->assertEquals($news->pluck('id'), $organization->news->pluck('id'));
    }

    public function test_organization_logo_url_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/logo.jpg';
        $organization = Organization::factory()->create(['logo' => $fullUrl]);

        $this->assertEquals($fullUrl, $organization->logo_url);
    }

    public function test_organization_logo_url_attribute_with_storage_path()
    {
        $storagePath = '/storage/organizations/logo.jpg';
        $organization = Organization::factory()->create(['logo' => $storagePath]);

        $expectedUrl = config('app.url') . $storagePath;
        $this->assertEquals($expectedUrl, $organization->logo_url);
    }

    public function test_organization_logo_url_attribute_with_filename()
    {
        Storage::fake('organizations');
        
        $filename = 'logo.jpg';
        $organization = Organization::factory()->create(['logo' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('getFileUrl')
            ->with($filename, 'organizations')
            ->willReturn('https://example.com/storage/organizations/' . $filename);

        $this->app->instance(StorageService::class, $storageService);

        $this->assertEquals('https://example.com/storage/organizations/' . $filename, $organization->logo_url);
    }

    public function test_organization_logo_url_attribute_with_null_logo()
    {
        $organization = Organization::factory()->create(['logo' => null]);

        $this->assertNull($organization->logo_url);
    }

    public function test_organization_logo_path_attribute_with_full_url()
    {
        $fullUrl = 'https://example.com/logo.jpg';
        $organization = Organization::factory()->create(['logo' => $fullUrl]);

        $this->assertEquals('logo.jpg', $organization->logo_path);
    }

    public function test_organization_logo_path_attribute_with_storage_path()
    {
        $storagePath = '/storage/organizations/logo.jpg';
        $organization = Organization::factory()->create(['logo' => $storagePath]);

        $this->assertEquals('logo.jpg', $organization->logo_path);
    }

    public function test_organization_logo_path_attribute_with_filename()
    {
        $filename = 'logo.jpg';
        $organization = Organization::factory()->create(['logo' => $filename]);

        $this->assertEquals($filename, $organization->logo_path);
    }

    public function test_organization_logo_path_attribute_with_null_logo()
    {
        $organization = Organization::factory()->create(['logo' => null]);

        $this->assertNull($organization->logo_path);
    }

    public function test_organization_soft_deletes()
    {
        $organization = Organization::factory()->create();
        $organizationId = $organization->id;

        $organization->delete();

        $this->assertSoftDeleted('organizations', ['id' => $organizationId]);
        $this->assertNotNull($organization->deleted_at);
    }

    public function test_organization_deletion_deletes_logo_file()
    {
        Storage::fake('organizations');
        
        $filename = 'logo.jpg';
        $organization = Organization::factory()->create(['logo' => $filename]);

        // Mock the StorageService
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->once())
            ->method('deleteFile')
            ->with($filename, 'organizations');

        $this->app->instance(StorageService::class, $storageService);

        $organization->delete();
    }

    public function test_organization_deletion_without_logo()
    {
        $organization = Organization::factory()->create(['logo' => null]);

        // Mock the StorageService to ensure deleteFile is not called
        $storageService = $this->createMock(StorageService::class);
        $storageService->expects($this->never())
            ->method('deleteFile');

        $this->app->instance(StorageService::class, $storageService);

        $organization->delete();
    }

    public function test_organization_scope_by_type()
    {
        Organization::factory()->create(['type' => 'UKM']);
        Organization::factory()->create(['type' => 'UKM']);
        Organization::factory()->create(['type' => 'BEM']);
        Organization::factory()->create(['type' => 'DEMA']);

        $ukmOrganizations = Organization::where('type', 'UKM')->get();
        $this->assertCount(2, $ukmOrganizations);

        $bemOrganizations = Organization::where('type', 'BEM')->get();
        $this->assertCount(1, $bemOrganizations);

        $demaOrganizations = Organization::where('type', 'DEMA')->get();
        $this->assertCount(1, $demaOrganizations);
    }

    public function test_organization_with_trashed_can_be_retrieved()
    {
        $organization = Organization::factory()->create();
        $organizationId = $organization->id;

        $organization->delete();

        $trashedOrganization = Organization::withTrashed()->find($organizationId);
        $this->assertNotNull($trashedOrganization);
        $this->assertNotNull($trashedOrganization->deleted_at);

        $nonTrashedOrganization = Organization::find($organizationId);
        $this->assertNull($nonTrashedOrganization);
    }
}