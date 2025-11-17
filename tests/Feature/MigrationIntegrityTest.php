<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MigrationIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('users'));
        
        $expectedColumns = [
            'id', 'name', 'email', 'email_verified_at', 'password',
            'remember_token', 'created_at', 'updated_at', 'role',
            'avatar', 'organization_id', 'is_email_verified',
            'email_verification_code', 'email_verification_expires',
            'password_reset_token', 'password_reset_expires', 'google_id'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column), "Missing column: {$column}");
        }
    }

    public function test_organizations_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('organizations'));
        
        $expectedColumns = [
            'id', 'name', 'type', 'description', 'logo', 'contact',
            'social_media', 'created_at', 'updated_at', 'deleted_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('organizations', $column), "Missing column: {$column}");
        }
    }

    public function test_activities_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('activities'));
        
        $expectedColumns = [
            'id', 'title', 'description', 'organization_id', 'start_date',
            'end_date', 'location', 'images', 'status', 'created_by',
            'created_at', 'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('activities', $column), "Missing column: {$column}");
        }
    }

    public function test_announcements_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('announcements'));
        
        $expectedColumns = [
            'id', 'title', 'content', 'category', 'image', 'is_pinned',
            'created_by', 'created_at', 'updated_at', 'deleted_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('announcements', $column), "Missing column: {$column}");
        }
    }

    public function test_news_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('news'));
        
        $expectedColumns = [
            'id', 'title', 'content', 'image', 'organization_id',
            'is_published', 'published_at', 'created_by',
            'created_at', 'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('news', $column), "Missing column: {$column}");
        }
    }

    public function test_files_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('files'));
        
        $expectedColumns = [
            'id', 'filename', 'original_name', 'mime_type', 'size',
            'path', 'disk', 'context', 'context_id', 'uploaded_by',
            'created_at', 'updated_at'
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('files', $column), "Missing column: {$column}");
        }
    }

    public function test_foreign_key_constraints_exist()
    {
        // Check users.organization_id foreign key
        $this->assertTrue(
            $this->hasForeignKey('users', 'users_organization_id_foreign'),
            'Missing foreign key: users.organization_id'
        );

        // Check activities.organization_id foreign key
        $this->assertTrue(
            $this->hasForeignKey('activities', 'activities_organization_id_foreign'),
            'Missing foreign key: activities.organization_id'
        );

        // Check activities.created_by foreign key
        $this->assertTrue(
            $this->hasForeignKey('activities', 'activities_created_by_foreign'),
            'Missing foreign key: activities.created_by'
        );

        // Check announcements.created_by foreign key
        $this->assertTrue(
            $this->hasForeignKey('announcements', 'announcements_created_by_foreign'),
            'Missing foreign key: announcements.created_by'
        );

        // Check news.organization_id foreign key
        $this->assertTrue(
            $this->hasForeignKey('news', 'news_organization_id_foreign'),
            'Missing foreign key: news.organization_id'
        );

        // Check news.created_by foreign key
        $this->assertTrue(
            $this->hasForeignKey('news', 'news_created_by_foreign'),
            'Missing foreign key: news.created_by'
        );

        // Check files.uploaded_by foreign key
        $this->assertTrue(
            $this->hasForeignKey('files', 'files_uploaded_by_foreign'),
            'Missing foreign key: files.uploaded_by'
        );
    }

    public function test_foreign_key_constraints_work()
    {
        // Test that we can't create an activity with non-existent organization
        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('activities')->insert([
            'title' => 'Test Activity',
            'description' => 'Test Description',
            'organization_id' => 999, // Non-existent organization
            'start_date' => now(),
            'end_date' => now()->addDay(),
            'location' => 'Test Location',
            'status' => 'draft',
            'created_by' => 1,
        ]);
    }

    public function test_foreign_key_constraints_cascade()
    {
        // Create test data
        $user = \App\Models\User::factory()->create();
        $organization = \App\Models\Organization::factory()->create();
        
        // Create related records
        $activity = \App\Models\Activity::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id
        ]);
        
        $news = \App\Models\News::factory()->create([
            'organization_id' => $organization->id,
            'created_by' => $user->id
        ]);

        // Verify related records exist
        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
        $this->assertDatabaseHas('news', ['id' => $news->id]);

        // Soft delete organization
        $organization->delete();

        // Verify organization is soft deleted but related records still exist
        $this->assertSoftDeleted('organizations', ['id' => $organization->id]);
        $this->assertDatabaseHas('activities', ['id' => $activity->id]);
        $this->assertDatabaseHas('news', ['id' => $news->id]);
    }

    public function test_table_indexes_exist()
    {
        // Check users table indexes
        $this->assertTrue($this->hasIndex('users', 'users_email_unique'));
        $this->assertTrue($this->hasIndex('users', 'users_organization_id_index'));

        // Check organizations table indexes
        $this->assertTrue($this->hasIndex('organizations', 'organizations_name_unique'));

        // Check activities table indexes
        $this->assertTrue($this->hasIndex('activities', 'activities_organization_id_index'));
        $this->assertTrue($this->hasIndex('activities', 'activities_created_by_index'));
        $this->assertTrue($this->hasIndex('activities', 'activities_start_date_index'));

        // Check announcements table indexes
        $this->assertTrue($this->hasIndex('announcements', 'announcements_created_by_index'));
        $this->assertTrue($this->hasIndex('announcements', 'announcements_is_pinned_index'));

        // Check news table indexes
        $this->assertTrue($this->hasIndex('news', 'news_organization_id_index'));
        $this->assertTrue($this->hasIndex('news', 'news_created_by_index'));
        $this->assertTrue($this->hasIndex('news', 'news_is_published_index'));
        $this->assertTrue($this->hasIndex('news', 'news_published_at_index'));

        // Check files table indexes
        $this->assertTrue($this->hasIndex('files', 'files_uploaded_by_index'));
        $this->assertTrue($this->hasIndex('files', 'files_context_index'));
        $this->assertTrue($this->hasIndex('files', 'files_context_id_index'));
    }

    public function test_column_types_are_correct()
    {
        // Test users table column types
        $this->assertEquals('string', Schema::getColumnType('users', 'name'));
        $this->assertEquals('string', Schema::getColumnType('users', 'email'));
        $this->assertEquals('string', Schema::getColumnType('users', 'password'));
        $this->assertEquals('string', Schema::getColumnType('users', 'role'));
        $this->assertEquals('integer', Schema::getColumnType('users', 'organization_id'));
        $this->assertEquals('boolean', Schema::getColumnType('users', 'is_email_verified'));

        // Test organizations table column types
        $this->assertEquals('string', Schema::getColumnType('organizations', 'name'));
        $this->assertEquals('string', Schema::getColumnType('organizations', 'type'));
        $this->assertEquals('text', Schema::getColumnType('organizations', 'description'));
        $this->assertEquals('json', Schema::getColumnType('organizations', 'social_media'));

        // Test activities table column types
        $this->assertEquals('string', Schema::getColumnType('activities', 'title'));
        $this->assertEquals('text', Schema::getColumnType('activities', 'description'));
        $this->assertEquals('integer', Schema::getColumnType('activities', 'organization_id'));
        $this->assertEquals('datetime', Schema::getColumnType('activities', 'start_date'));
        $this->assertEquals('datetime', Schema::getColumnType('activities', 'end_date'));
        $this->assertEquals('json', Schema::getColumnType('activities', 'images'));

        // Test announcements table column types
        $this->assertEquals('string', Schema::getColumnType('announcements', 'title'));
        $this->assertEquals('text', Schema::getColumnType('announcements', 'content'));
        $this->assertEquals('string', Schema::getColumnType('announcements', 'category'));
        $this->assertEquals('boolean', Schema::getColumnType('announcements', 'is_pinned'));

        // Test news table column types
        $this->assertEquals('string', Schema::getColumnType('news', 'title'));
        $this->assertEquals('text', Schema::getColumnType('news', 'content'));
        $this->assertEquals('integer', Schema::getColumnType('news', 'organization_id'));
        $this->assertEquals('boolean', Schema::getColumnType('news', 'is_published'));
        $this->assertEquals('datetime', Schema::getColumnType('news', 'published_at'));

        // Test files table column types
        $this->assertEquals('string', Schema::getColumnType('files', 'filename'));
        $this->assertEquals('string', Schema::getColumnType('files', 'original_name'));
        $this->assertEquals('string', Schema::getColumnType('files', 'mime_type'));
        $this->assertEquals('integer', Schema::getColumnType('files', 'size'));
        $this->assertEquals('string', Schema::getColumnType('files', 'disk'));
        $this->assertEquals('string', Schema::getColumnType('files', 'context'));
        $this->assertEquals('integer', Schema::getColumnType('files', 'context_id'));
    }

    public function test_nullable_columns_are_correct()
    {
        // Test users table nullable columns
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('users', 'organization_id')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('users', 'avatar')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('users', 'google_id')->getNotnull() === false);

        // Test organizations table nullable columns
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('organizations', 'description')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('organizations', 'logo')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('organizations', 'contact')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('organizations', 'social_media')->getNotnull() === false);

        // Test activities table nullable columns
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('activities', 'images')->getNotnull() === false);

        // Test announcements table nullable columns
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('announcements', 'image')->getNotnull() === false);

        // Test news table nullable columns
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('news', 'image')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('news', 'organization_id')->getNotnull() === false);
        $this->assertTrue(Schema::getConnection()
            ->getDoctrineColumn('news', 'published_at')->getNotnull() === false);
    }

    public function test_default_values_are_correct()
    {
        // Test boolean default values
        $this->assertEquals(false, Schema::getConnection()
            ->getDoctrineColumn('users', 'is_email_verified')->getDefault());
        $this->assertEquals(false, Schema::getConnection()
            ->getDoctrineColumn('announcements', 'is_pinned')->getDefault());
        $this->assertEquals(false, Schema::getConnection()
            ->getDoctrineColumn('news', 'is_published')->getDefault());
    }

    private function hasForeignKey($table, $foreignKey)
    {
        $foreignKeys = Schema::getConnection()->getDoctrineSchemaManager()
            ->listTableForeignKeys($table);
            
        foreach ($foreignKeys as $key) {
            if ($key->getName() === $foreignKey) {
                return true;
            }
        }
        
        return false;
    }

    private function hasIndex($table, $index)
    {
        $indexes = Schema::getConnection()->getDoctrineSchemaManager()
            ->listTableIndexes($table);
            
        return isset($indexes[$index]);
    }
}