<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\User;
use App\Models\Activity;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_has_many_members()
    {
        $organization = Organization::factory()->create();
        $users = User::factory()->count(5)->create(['organization_id' => $organization->id]);

        $this->assertCount(5, $organization->members);
        $this->assertEquals($users->pluck('id'), $organization->members->pluck('id'));
    }

    public function test_organization_has_many_activities()
    {
        $organization = Organization::factory()->create();
        $activities = Activity::factory()->count(3)->create(['organization_id' => $organization->id]);

        $this->assertCount(3, $organization->activities);
        $this->assertEquals($activities->pluck('id'), $organization->activities->pluck('id'));
    }

    public function test_organization_has_many_news()
    {
        $organization = Organization::factory()->create();
        $news = News::factory()->count(4)->create(['organization_id' => $organization->id]);

        $this->assertCount(4, $organization->news);
        $this->assertEquals($news->pluck('id'), $organization->news->pluck('id'));
    }

    public function test_organization_fillable_fields()
    {
        $fillable = ['name', 'type', 'description', 'logo', 'contact', 'social_media'];

        $organization = new Organization();
        $this->assertEquals($fillable, $organization->getFillable());
    }

    public function test_organization_social_media_casts_to_array()
    {
        $socialMedia = [
            'instagram' => '@org_instagram',
            'twitter' => '@org_twitter',
            'facebook' => 'Organization Facebook'
        ];

        $organization = Organization::factory()->create(['social_media' => $socialMedia]);
        
        $this->assertIsArray($organization->social_media);
        $this->assertEquals($socialMedia, $organization->social_media);
    }
}