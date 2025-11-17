<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first organization and user for seeding
        $organization = DB::table('organizations')->first();
        $user = DB::table('users')->where('role', 'ADMIN')->first();

        if (!$organization || !$user) {
            $this->command->warn('Please run UserSeeder and OrganizationSeeder first');
            return;
        }

        $activities = [
            [
                'id' => Str::uuid(),
                'title' => 'Pelatihan Kepemimpinan Mahasiswa',
                'description' => 'Pelatihan untuk mengembangkan soft skill dan kemampuan kepemimpinan mahasiswa.',
                'organization_id' => $organization->id,
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(9),
                'location' => 'Auditorium Universitas',
                'images' => json_encode(['leadership_training.jpg']),
                'status' => 'PUBLISHED',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Lomba Debat Antar Fakultas',
                'description' => 'Kompetisi debat antar fakultas untuk meningkatkan kemampuan berargumentasi.',
                'organization_id' => $organization->id,
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(15),
                'location' => 'Gedung Rektorat Lantai 3',
                'images' => json_encode(['debate_competition.jpg']),
                'status' => 'DRAFT',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Bakti Sosial Desa',
                'description' => 'Kegiatan pengabdian masyarakat di desa-desa sekitar kampus.',
                'organization_id' => $organization->id,
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(23),
                'location' => 'Desa Sukamaju',
                'images' => json_encode(['social_service.jpg', 'community_work.jpg']),
                'status' => 'PUBLISHED',
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('activities')->insert($activities);
    }
}