<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrganizationCRUDTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_organization_index_page_can_be_rendered()
    {
        $response = $this->get('/organizations');

        $response->assertStatus(200);
        $response->assertViewIs('organizations.index');
    }

    public function test_organization_create_page_can_be_rendered_by_authenticated_user()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/organizations/create');

        $response->assertStatus(200);
        $response->assertViewIs('organizations.create');
    }

    public function test_organization_create_page_redirects_unauthenticated_user()
    {
        $response = $this->get('/organizations/create');

        $response->assertRedirect('/login');
    }

    public function test_organization_can_be_stored_with_valid_data()
    {
        $user = User::factory()->create();
        $organizationData = [
            'name' => $this->faker->company,
            'type' => 'UKM',
            'description' => $this->faker->paragraph,
            'contact' => $this->faker->phoneNumber,
            'social_media' => [
                'facebook' => 'https://facebook.com/example',
                'twitter' => 'https://twitter.com/example',
                'instagram' => 'https://instagram.com/example',
            ]
        ];

        $response = $this->actingAs($user)->post('/organizations', $organizationData);

        $this->assertDatabaseHas('organizations', [
            'name' => $organizationData['name'],
            'type' => $organizationData['type'],
            'description' => $organizationData['description'],
            'contact' => $organizationData['contact'],
        ]);

        $response->assertRedirect('/organizations/' . Organization::first()->id);
        $response->assertSessionHas('success', 'Organization created successfully.');
    }

    public function test_organization_can_be_stored_with_logo()
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('logo.jpg');
        
        $organizationData = [
            'name' => $this->faker->company,
            'type' => 'UKM',
            'description' => $this->faker->paragraph,
            'logo' => $file,
        ];

        $response = $this->actingAs($user)->post('/organizations', $organizationData);

        $organization = Organization::first();
        $this->assertNotNull($organization->logo);
        Storage::disk('public')->assertExists($organization->logo);

        $response->assertRedirect('/organizations/' . $organization->id);
    }

    public function test_organization_store_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $invalidData = [
            'name' => '', // Required field missing
            'type' => '', // Required field missing
        ];

        $response = $this->actingAs($user)->post('/organizations', $invalidData);

        $response->assertSessionHasErrors(['name', 'type']);
        $this->assertDatabaseCount('organizations', 0);
    }

    public function test_organization_store_fails_with_duplicate_name()
    {
        $user = User::factory()->create();
        $existingOrg = Organization::factory()->create(['name' => 'Existing Organization']);
        
        $organizationData = [
            'name' => 'Existing Organization', // Duplicate name
            'type' => 'UKM',
        ];

        $response = $this->actingAs($user)->post('/organizations', $organizationData);

        $response->assertSessionHasErrors(['name']);
        $this->assertDatabaseCount('organizations', 1);
    }

    public function test_organization_show_page_displays_organization_details()
    {
        $organization = Organization::factory()->create();

        $response = $this->get('/organizations/' . $organization->id);

        $response->assertStatus(200);
        $response->assertViewIs('organizations.show');
        $response->assertViewHas('organization', function ($viewOrganization) use ($organization) {
            return $viewOrganization->id === $organization->id;
        });
    }

    public function test_organization_show_page_returns_404_for_nonexistent_organization()
    {
        $response = $this->get('/organizations/999');

        $response->assertStatus(404);
    }

    public function test_organization_edit_page_can_be_rendered_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();

        $response = $this->actingAs($admin)->get('/organizations/' . $organization->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('organizations.edit');
    }

    public function test_organization_edit_page_can_be_rendered_by_org_admin()
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organization_id' => $organization->id
        ]);

        $response = $this->actingAs($orgAdmin)->get('/organizations/' . $organization->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('organizations.edit');
    }

    public function test_organization_edit_page_denied_for_regular_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        $organization = Organization::factory()->create();

        $response = $this->actingAs($user)->get('/organizations/' . $organization->id . '/edit');

        $response->assertStatus(403);
    }

    public function test_organization_can_be_updated_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        
        $updateData = [
            'name' => 'Updated Organization Name',
            'type' => 'BEM',
            'description' => 'Updated description',
            'contact' => 'Updated contact',
        ];

        $response = $this->actingAs($admin)
            ->put('/organizations/' . $organization->id, $updateData);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Organization Name',
            'type' => 'BEM',
            'description' => 'Updated description',
            'contact' => 'Updated contact',
        ]);

        $response->assertRedirect('/organizations/' . $organization->id);
        $response->assertSessionHas('success', 'Organization updated successfully.');
    }

    public function test_organization_can_be_updated_by_org_admin()
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organization_id' => $organization->id
        ]);
        
        $updateData = [
            'name' => 'Updated Organization Name',
            'type' => 'BEM',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($orgAdmin)
            ->put('/organizations/' . $organization->id, $updateData);

        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'name' => 'Updated Organization Name',
            'type' => 'BEM',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect('/organizations/' . $organization->id);
    }

    public function test_organization_update_denied_for_regular_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        $organization = Organization::factory()->create();
        
        $updateData = [
            'name' => 'Updated Organization Name',
            'type' => 'BEM',
        ];

        $response = $this->actingAs($user)
            ->put('/organizations/' . $organization->id, $updateData);

        $response->assertStatus(403);
    }

    public function test_organization_update_fails_with_invalid_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        
        $invalidData = [
            'name' => '', // Required field missing
            'type' => '', // Required field missing
        ];

        $response = $this->actingAs($admin)
            ->put('/organizations/' . $organization->id, $invalidData);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_organization_can_be_soft_deleted_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();

        $response = $this->actingAs($admin)
            ->delete('/organizations/' . $organization->id);

        $this->assertSoftDeleted('organizations', ['id' => $organization->id]);
        $response->assertRedirect('/organizations');
        $response->assertSessionHas('success', 'Organization deleted successfully.');
    }

    public function test_organization_delete_denied_for_org_admin()
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organization_id' => $organization->id
        ]);

        $response = $this->actingAs($orgAdmin)
            ->delete('/organizations/' . $organization->id);

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('organizations', ['id' => $organization->id]);
    }

    public function test_organization_delete_denied_for_regular_user()
    {
        $user = User::factory()->create(['role' => 'user']);
        $organization = Organization::factory()->create();

        $response = $this->actingAs($user)
            ->delete('/organizations/' . $organization->id);

        $response->assertStatus(403);
        $this->assertNotSoftDeleted('organizations', ['id' => $organization->id]);
    }

    public function test_member_can_be_added_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => null]);

        $response = $this->actingAs($admin)
            ->post('/organizations/' . $organization->id . '/add-member', [
                'user_id' => $user->id,
                'role' => 'member'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'organization_id' => $organization->id,
            'role' => 'member'
        ]);

        $response->assertRedirect('/organizations/' . $organization->id);
        $response->assertSessionHas('success', 'Member added successfully.');
    }

    public function test_member_can_be_added_by_org_admin()
    {
        $organization = Organization::factory()->create();
        $orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organization_id' => $organization->id
        ]);
        $user = User::factory()->create(['organization_id' => null]);

        $response = $this->actingAs($orgAdmin)
            ->post('/organizations/' . $organization->id . '/add-member', [
                'user_id' => $user->id,
                'role' => 'member'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'organization_id' => $organization->id,
            'role' => 'member'
        ]);

        $response->assertRedirect('/organizations/' . $organization->id);
    }

    public function test_add_member_fails_for_already_member()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $response = $this->actingAs($admin)
            ->post('/organizations/' . $organization->id . '/add-member', [
                'user_id' => $user->id,
                'role' => 'member'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'User is already a member of this organization.');
    }

    public function test_member_can_be_removed_by_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'member'
        ]);

        $response = $this->actingAs($admin)
            ->delete('/organizations/' . $organization->id . '/remove-member/' . $user->id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'organization_id' => null,
            'role' => 'member'
        ]);

        $response->assertRedirect('/organizations/' . $organization->id);
        $response->assertSessionHas('success', 'Member removed successfully.');
    }

    public function test_last_admin_cannot_be_removed()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $organization = Organization::factory()->create();
        $lastOrgAdmin = User::factory()->create([
            'organization_id' => $organization->id,
            'role' => 'org_admin'
        ]);

        $response = $this->actingAs($admin)
            ->delete('/organizations/' . $organization->id . '/remove-member/' . $lastOrgAdmin->id);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Cannot remove last organization admin.');
        
        $this->assertDatabaseHas('users', [
            'id' => $lastOrgAdmin->id,
            'organization_id' => $organization->id,
            'role' => 'org_admin'
        ]);
    }

    public function test_organization_pagination_works()
    {
        Organization::factory()->count(15)->create();

        $response = $this->get('/organizations');

        $response->assertStatus(200);
        $response->assertViewHas('organizations', function ($organizations) {
            return $organizations->hasPages();
        });
    }

    public function test_organization_search_functionality()
    {
        Organization::factory()->create(['name' => 'Test Organization']);
        Organization::factory()->create(['name' => 'Another Organization']);

        $response = $this->get('/organizations?search=Test');

        $response->assertStatus(200);
        $response->assertViewHas('organizations', function ($organizations) {
            return $organizations->contains('name', 'Test Organization') &&
                   !$organizations->contains('name', 'Another Organization');
        });
    }
}