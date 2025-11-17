<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\Announcement;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_factory()
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
    }

    public function test_user_fillable_attributes()
    {
        $fillable = [
            'name',
            'email', 
            'password',
            'role',
            'avatar',
            'organization_id',
            'is_email_verified',
            'email_verification_code',
            'email_verification_expires',
            'password_reset_token',
            'password_reset_expires',
            'google_id',
        ];

        $user = new User();
        $this->assertEquals($fillable, $user->getFillable());
    }

    public function test_user_hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $user = new User();
        $this->assertEquals($hidden, $user->getHidden());
    }

    public function test_user_role_enum_cases()
    {
        $this->assertEquals('admin', UserRole::ADMIN->value);
        $this->assertEquals('org_admin', UserRole::ORG_ADMIN->value);
        $this->assertEquals('user', UserRole::USER->value);
    }

    public function test_user_is_admin_method()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $orgAdmin = User::factory()->create(['role' => UserRole::ORG_ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($orgAdmin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_is_org_admin_method()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $orgAdmin = User::factory()->create(['role' => UserRole::ORG_ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->assertFalse($admin->isOrgAdmin());
        $this->assertTrue($orgAdmin->isOrgAdmin());
        $this->assertFalse($user->isOrgAdmin());
    }

    public function test_user_is_org_admin_for_organization_method()
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        
        $orgAdmin = User::factory()->create([
            'role' => UserRole::ORG_ADMIN,
            'organization_id' => $organization->id
        ]);
        
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->assertTrue($orgAdmin->isOrgAdminForOrganization($organization->id));
        $this->assertFalse($orgAdmin->isOrgAdminForOrganization($otherOrganization->id));
        $this->assertFalse($admin->isOrgAdminForOrganization($organization->id));
        $this->assertFalse($user->isOrgAdminForOrganization($organization->id));
    }

    public function test_user_is_user_method()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $orgAdmin = User::factory()->create(['role' => UserRole::ORG_ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->assertFalse($admin->isUser());
        $this->assertFalse($orgAdmin->isUser());
        $this->assertTrue($user->isUser());
    }

    public function test_user_can_manage_organizations_method()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $orgAdmin = User::factory()->create(['role' => UserRole::ORG_ADMIN]);
        $user = User::factory()->create(['role' => UserRole::USER]);

        $this->assertTrue($admin->canManageOrganizations());
        $this->assertTrue($orgAdmin->canManageOrganizations());
        $this->assertFalse($user->canManageOrganizations());
    }

    public function test_user_password_is_hashed()
    {
        $password = 'password123';
        $user = User::factory()->create(['password' => $password]);

        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    public function test_user_email_verification_casts()
    {
        $user = User::factory()->create([
            'is_email_verified' => true,
            'email_verification_expires' => '2024-12-31 23:59:59',
            'password_reset_expires' => '2024-12-31 23:59:59',
        ]);

        $this->assertIsBool($user->is_email_verified);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verification_expires);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->password_reset_expires);
    }

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

    public function test_user_without_organization()
    {
        $user = User::factory()->create(['organization_id' => null]);

        $this->assertNull($user->organization);
    }

    public function test_user_scope_by_role()
    {
        User::factory()->create(['role' => UserRole::ADMIN]);
        User::factory()->create(['role' => UserRole::ADMIN]);
        User::factory()->create(['role' => UserRole::ORG_ADMIN]);
        User::factory()->create(['role' => UserRole::USER]);

        $admins = User::where('role', UserRole::ADMIN)->get();
        $this->assertCount(2, $admins);

        $orgAdmins = User::where('role', UserRole::ORG_ADMIN)->get();
        $this->assertCount(1, $orgAdmins);

        $users = User::where('role', UserRole::USER)->get();
        $this->assertCount(1, $users);
    }
}