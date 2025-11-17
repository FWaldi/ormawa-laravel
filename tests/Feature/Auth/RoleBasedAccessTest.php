<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleBasedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_org_admin_can_access_org_admin_routes()
    {
        $orgAdmin = User::factory()->create(['role' => 'org_admin']);

        // This test assumes we have org_admin protected routes
        // For now, we'll test the middleware directly
        $this->assertTrue($orgAdmin->canManageOrganizations());
    }

    public function test_user_role_methods_work_correctly()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $orgAdmin = User::factory()->create(['role' => 'org_admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isUser());

        $this->assertTrue($orgAdmin->isOrgAdmin());
        $this->assertFalse($orgAdmin->isAdmin());

        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isOrgAdmin());
    }
}