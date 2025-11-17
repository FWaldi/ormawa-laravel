<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_belongs_to_organization()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $this->assertInstanceOf(Organization::class, $user->organization);
        $this->assertEquals($organization->id, $user->organization->id);
    }

    public function test_user_has_many_activities()
    {
        $user = User::factory()->create();
        $activities = Activity::factory()->count(3)->create(['created_by' => $user->id]);

        $this->assertCount(3, $user->activities);
        $this->assertEquals($activities->pluck('id'), $user->activities->pluck('id'));
    }

    public function test_user_has_many_announcements()
    {
        $user = User::factory()->create();
        $announcements = Announcement::factory()->count(2)->create(['created_by' => $user->id]);

        $this->assertCount(2, $user->announcements);
        $this->assertEquals($announcements->pluck('id'), $user->announcements->pluck('id'));
    }

    public function test_user_has_many_news()
    {
        $user = User::factory()->create();
        $news = News::factory()->count(4)->create(['created_by' => $user->id]);

        $this->assertCount(4, $user->news);
        $this->assertEquals($news->pluck('id'), $user->news->pluck('id'));
    }

    public function test_user_role_enum_values()
    {
        $validRoles = ['ADMIN', 'KEMAHASISWAAN', 'ORMAWA', 'USER'];
        
        foreach ($validRoles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->assertEquals($role, $user->role);
        }
    }

    public function test_user_fillable_fields()
    {
        $fillable = [
            'name', 'email', 'password', 'role', 'avatar', 'organization_id',
            'is_email_verified', 'email_verification_code', 'email_verification_expires',
            'password_reset_token', 'password_reset_expires', 'google_id'
        ];

        $user = new User();
        $this->assertEquals($fillable, $user->getFillable());
    }
}