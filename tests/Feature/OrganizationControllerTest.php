<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $orgAdmin;
    protected User $regularUser;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->organization = Organization::factory()->create();
        $this->orgAdmin = User::factory()->create([
            'role' => 'org_admin',
            'organization_id' => $this->organization->id
        ]);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    public function test_index_displays_organizations()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('organizations.index'));

        $response->assertStatus(200)
            ->assertViewIs('organizations.index')
            ->assertViewHas('organizations');
    }

    public function test_create_displays_form_for_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('organizations.create'));

        $response->assertStatus(200)
            ->assertViewIs('organizations.create');
    }

    public function test_create_redirects_for_non_admin()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('organizations.create'));

        $response->assertRedirect();
    }

    public function test_store_creates_organization_with_valid_data()
    {
        Storage::fake('public');
        
        $data = [
            'name' => 'Test Organization',
            'type' => 'Student Organization',
            'description' => 'A test organization',
            'contact' => 'test@example.com',
            'social_media' => [
                'facebook' => 'https://facebook.com/test',
                'twitter' => 'https://twitter.com/test',
            ],
            'logo' => UploadedFile::fake()->image('logo.jpg'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('organizations.store'), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
            'type' => 'Student Organization',
            'description' => 'A test organization',
            'contact' => 'test@example.com',
        ]);

        $organization = Organization::where('name', 'Test Organization')->first();
        $this->assertNotNull($organization->logo);
        Storage::disk('public')->assertExists($organization->logo);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('organizations.store'), []);

        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_store_validates_unique_name()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('organizations.store'), [
                'name' => $this->organization->name,
                'type' => 'Student Organization',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_show_displays_organization()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('organizations.show', $this->organization->id));

        $response->assertStatus(200)
            ->assertViewIs('organizations.show')
            ->assertViewHas('organization')
            ->assertSee($this->organization->name);
    }

    public function test_edit_displays_form_for_org_admin()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->get(route('organizations.edit', $this->organization->id));

        $response->assertStatus(200)
            ->assertViewIs('organizations.edit')
            ->assertViewHas('organization');
    }

    public function test_edit_displays_form_for_admin()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('organizations.edit', $this->organization->id));

        $response->assertStatus(200)
            ->assertViewIs('organizations.edit');
    }

    public function test_edit_forbidden_for_non_member()
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('organizations.edit', $this->organization->id));

        $response->assertStatus(403);
    }

    public function test_update_modifies_organization()
    {
        $data = [
            'name' => 'Updated Organization',
            'type' => 'Academic Club',
            'description' => 'Updated description',
            'contact' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->orgAdmin)
            ->put(route('organizations.update', $this->organization->id), $data);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('organizations', [
            'id' => $this->organization->id,
            'name' => 'Updated Organization',
            'type' => 'Academic Club',
            'description' => 'Updated description',
            'contact' => 'updated@example.com',
        ]);
    }

    public function test_update_forbidden_for_non_member()
    {
        $response = $this->actingAs($this->regularUser)
            ->put(route('organizations.update', $this->organization->id), [
                'name' => 'Updated Organization',
                'type' => 'Academic Club',
            ]);

        $response->assertStatus(403);
    }

    public function test_update_validates_unique_name_excluding_self()
    {
        $otherOrg = Organization::factory()->create();

        $response = $this->actingAs($this->orgAdmin)
            ->put(route('organizations.update', $this->organization->id), [
                'name' => $otherOrg->name,
                'type' => 'Academic Club',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_destroy_deletes_organization_for_admin()
    {
        Storage::fake('public');
        $this->organization->update(['logo' => 'logos/test.jpg']);
        Storage::disk('public')->put('logos/test.jpg', 'fake content');

        $response = $this->actingAs($this->admin)
            ->delete(route('organizations.destroy', $this->organization->id));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('organizations', [
            'id' => $this->organization->id,
        ]);

        Storage::disk('public')->assertMissing('logos/test.jpg');
    }

    public function test_destroy_forbidden_for_non_admin()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->delete(route('organizations.destroy', $this->organization->id));

        $response->assertStatus(403);
    }

    public function test_add_member_adds_user_to_organization()
    {
        $newUser = User::factory()->create(['organization_id' => null]);

        $response = $this->actingAs($this->orgAdmin)
            ->post(route('organizations.addMember', $this->organization->id), [
                'user_id' => $newUser->id,
                'role' => 'member',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $newUser->id,
            'organization_id' => $this->organization->id,
            'role' => 'member',
        ]);
    }

    public function test_add_member_forbidden_for_non_member()
    {
        $newUser = User::factory()->create();

        $response = $this->actingAs($this->regularUser)
            ->post(route('organizations.addMember', $this->organization->id), [
                'user_id' => $newUser->id,
                'role' => 'member',
            ]);

        $response->assertStatus(403);
    }

    public function test_add_member_prevents_duplicate_membership()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->post(route('organizations.addMember', $this->organization->id), [
                'user_id' => $this->orgAdmin->id,
                'role' => 'member',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_remove_member_removes_user_from_organization()
    {
        $member = User::factory()->create([
            'organization_id' => $this->organization->id,
            'role' => 'member'
        ]);

        $response = $this->actingAs($this->orgAdmin)
            ->delete(route('organizations.removeMember', [$this->organization->id, $member->id]));

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'organization_id' => null,
            'role' => 'member',
        ]);
    }

    public function test_remove_member_forbidden_for_non_member()
    {
        $response = $this->actingAs($this->regularUser)
            ->delete(route('organizations.removeMember', [$this->organization->id, $this->orgAdmin->id]));

        $response->assertStatus(403);
    }

    public function test_remove_member_prevents_removing_last_admin()
    {
        $response = $this->actingAs($this->orgAdmin)
            ->delete(route('organizations.removeMember', [$this->organization->id, $this->orgAdmin->id]));

        $response->assertRedirect()
            ->assertSessionHas('error');
    }
}