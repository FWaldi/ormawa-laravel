<?php

namespace Tests\Unit;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_announcement_belongs_to_creator()
    {
        $user = User::factory()->create();
        $announcement = Announcement::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $announcement->creator);
        $this->assertEquals($user->id, $announcement->creator->id);
    }

    public function test_announcement_fillable_fields()
    {
        $fillable = ['title', 'content', 'category', 'image', 'is_pinned', 'created_by'];

        $announcement = new Announcement();
        $this->assertEquals($fillable, $announcement->getFillable());
    }

    public function test_announcement_is_pinned_casts_to_boolean()
    {
        $announcement = Announcement::factory()->create(['is_pinned' => true]);
        $this->assertIsBool($announcement->is_pinned);
        $this->assertTrue($announcement->is_pinned);

        $announcement = Announcement::factory()->create(['is_pinned' => false]);
        $this->assertIsBool($announcement->is_pinned);
        $this->assertFalse($announcement->is_pinned);
    }
}