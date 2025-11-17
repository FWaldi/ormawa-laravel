<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\News;
use App\Models\Announcement;

class ForeignKeyConstraintTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_user_foreign_key_constraint()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);

        // Attempting to delete user should fail when organization exists
        $this->expectException(\Illuminate\Database\QueryException::class);
        $user->delete();
    }

    public function test_organization_user_cascade_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);

        // Delete user with cascade
        $user->delete();

        // Organization should be deleted due to cascade
        $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
    }

    public function test_activity_organization_foreign_key_constraint()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        $activity = Activity::factory()->create(['organization_id' => $organization->id]);

        // Attempting to delete organization should fail when activity exists
        $this->expectException(\Illuminate\Database\QueryException::class);
        $organization->delete();
    }

    public function test_activity_organization_cascade_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        $activity = Activity::factory()->create(['organization_id' => $organization->id]);

        // Delete organization with cascade
        $organization->delete();

        // Activity should be deleted due to cascade
        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_news_organization_set_null_on_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        $news = News::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id
        ]);

        // Delete organization
        $organization->delete();

        // News organization_id should be set to null
        $news->refresh();
        $this->assertNull($news->organization_id);
        $this->assertDatabaseHas('news', ['id' => $news->id, 'organization_id' => null]);
    }

    public function test_announcement_user_cascade_delete()
    {
        $user = User::factory()->create();
        $announcement = Announcement::factory()->create(['created_by' => $user->id]);

        // Delete user with cascade
        $user->delete();

        // Announcement should be deleted due to cascade
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_news_user_cascade_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        $news = News::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id
        ]);

        // Delete user with cascade
        $user->delete();

        // News should be deleted due to cascade
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    public function test_activity_user_cascade_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        $activity = Activity::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id
        ]);

        // Delete user with cascade
        $user->delete();

        // Activity should be deleted due to cascade
        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_user_organization_set_null_on_delete()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['user_id' => $user->id]);
        
        // Assign organization to user
        $user->organization_id = $organization->id;
        $user->save();

        // Delete organization
        $organization->delete();

        // User organization_id should be set to null
        $user->refresh();
        $this->assertNull($user->organization_id);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'organization_id' => null]);
    }

    public function test_invalid_foreign_key_rejection()
    {
        $user = User::factory()->create();
        
        // Attempt to create organization with non-existent user_id
        $this->expectException(\Illuminate\Database\QueryException::class);
        Organization::factory()->create(['user_id' => 'non-existent-uuid']);
    }

    public function test_database_indexes_exist()
    {
        // Check that indexes are properly created on foreign key columns
        $indexes = \DB::select("SHOW INDEX FROM organizations");
        $userIndexExists = collect($indexes)->contains('Column_name', 'user_id');
        $this->assertTrue($userIndexExists, 'Index on organizations.user_id should exist');

        $indexes = \DB::select("SHOW INDEX FROM activities");
        $orgIndexExists = collect($indexes)->contains('Column_name', 'organization_id');
        $userIndexExists = collect($indexes)->contains('Column_name', 'created_by');
        $this->assertTrue($orgIndexExists, 'Index on activities.organization_id should exist');
        $this->assertTrue($userIndexExists, 'Index on activities.created_by should exist');

        $indexes = \DB::select("SHOW INDEX FROM news");
        $orgIndexExists = collect($indexes)->contains('Column_name', 'organization_id');
        $userIndexExists = collect($indexes)->contains('Column_name', 'created_by');
        $this->assertTrue($orgIndexExists, 'Index on news.organization_id should exist');
        $this->assertTrue($userIndexExists, 'Index on news.created_by should exist');
    }
}