<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_belongs_to_organization()
    {
        $organization = Organization::factory()->create();
        $activity = Activity::factory()->create(['organization_id' => $organization->id]);

        $this->assertInstanceOf(Organization::class, $activity->organization);
        $this->assertEquals($organization->id, $activity->organization->id);
    }

    public function test_activity_belongs_to_creator()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $activity->creator);
        $this->assertEquals($user->id, $activity->creator->id);
    }

    public function test_activity_status_enum_values()
    {
        $validStatuses = ['DRAFT', 'PUBLISHED', 'COMPLETED', 'CANCELLED'];
        
        foreach ($validStatuses as $status) {
            $activity = Activity::factory()->create(['status' => $status]);
            $this->assertEquals($status, $activity->status);
        }
    }

    public function test_activity_fillable_fields()
    {
        $fillable = [
            'title', 'description', 'organization_id', 'start_date', 'end_date',
            'location', 'images', 'status', 'created_by'
        ];

        $activity = new Activity();
        $this->assertEquals($fillable, $activity->getFillable());
    }

    public function test_activity_images_casts_to_array()
    {
        $images = ['image1.jpg', 'image2.jpg', 'image3.jpg'];

        $activity = Activity::factory()->create(['images' => $images]);
        
        $this->assertIsArray($activity->images);
        $this->assertEquals($images, $activity->images);
    }

    public function test_activity_dates_are_cast_to_datetime()
    {
        $startDate = now();
        $endDate = now()->addDays(2);

        $activity = Activity::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $activity->start_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $activity->end_date);
        $this->assertEquals($startDate->format('Y-m-d H:i:s'), $activity->start_date->format('Y-m-d H:i:s'));
        $this->assertEquals($endDate->format('Y-m-d H:i:s'), $activity->end_date->format('Y-m-d H:i:s'));
    }
}