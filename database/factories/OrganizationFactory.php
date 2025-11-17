<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'name' => $this->faker->company(),
            'type' => $this->faker->randomElement(['ORMAWA', 'UKM', 'DEPARTEMEN']),
            'description' => $this->faker->paragraph(3),
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'contact' => $this->faker->email(),
            'social_media' => [
                'instagram' => '@' . $this->faker->userName(),
                'twitter' => '@' . $this->faker->userName(),
                'facebook' => $this->faker->company(),
            ],
        ];
    }
}