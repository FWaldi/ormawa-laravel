<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isPublished = $this->faker->boolean(80); // 80% chance of being published
        
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'title' => $this->faker->sentence(8),
            'content' => $this->faker->paragraph(10),
            'image' => $this->faker->imageUrl(1200, 800, 'news'),
            'organization_id' => \App\Models\Organization::factory(),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_by' => \App\Models\User::factory(),
        ];
    }
}