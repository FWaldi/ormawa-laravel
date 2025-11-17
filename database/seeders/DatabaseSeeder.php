<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            OrganizationSeeder::class,
            ActivitySeeder::class,
            AnnouncementSeeder::class,
            NewsSeeder::class,
        ]);

        // Create additional random users for testing
        \App\Models\User::factory(10)->create();
    }
}
