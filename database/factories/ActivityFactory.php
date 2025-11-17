<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Activity::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+2 days');
        
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(5),
            'organization_id' => \App\Models\Organization::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $this->faker->address(),
            'images' => [
                $this->faker->imageUrl(800, 600, 'events'),
                $this->faker->imageUrl(800, 600, 'people'),
            ],
            'status' => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'COMPLETED', 'CANCELLED']),
            'created_by' => \App\Models\User::factory(),
        ];
    }
}