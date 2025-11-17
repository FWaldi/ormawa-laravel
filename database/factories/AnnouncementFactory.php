<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraph(8),
            'category' => $this->faker->randomElement(['Akademik', 'Kemahasiswaan', 'Beasiswa', 'Lomba', 'Workshop']),
            'image' => $this->faker->imageUrl(1200, 600, 'education'),
            'is_pinned' => $this->faker->boolean(20), // 20% chance of being pinned
            'created_by' => \App\Models\User::factory(),
        ];
    }
}