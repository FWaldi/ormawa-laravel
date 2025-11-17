<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first organization and admin user for seeding
        $organization = DB::table('organizations')->first();
        $user = DB::table('users')->where('role', 'ADMIN')->first();

        if (!$organization || !$user) {
            $this->command->warn('Please run UserSeeder and OrganizationSeeder first');
            return;
        }

        $news = [
            [
                'id' => Str::uuid(),
                'title' => 'Prestasi Gemilang Mahasiswa dalam Kompetisi Nasional',
                'content' => 'Tim mahasiswa Universitas berhasil meraih juara pertama dalam Kompetisi Inovasi Teknologi tingkat nasional. Prestasi ini membawa nama baik universitas di kancah nasional.',
                'image' => 'achievement_news.jpg',
                'organization_id' => $organization->id,
                'is_published' => true,
                'published_at' => now()->subDays(3),
                'created_by' => $user->id,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Kerjasama Internasional dengan Universitas Luar Negeri',
                'content' => 'Universitas menjalin kerjasama dengan beberapa universitas ternama dari luar negeri untuk program pertukaran mahasiswa dan joint research.',
                'image' => 'international_cooperation.jpg',
                'organization_id' => $organization->id,
                'is_published' => true,
                'published_at' => now()->subWeek(),
                'created_by' => $user->id,
                'created_at' => now()->subWeek(),
                'updated_at' => now()->subWeek(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Program Beasiswa untuk Mahasiswa Berprestasi',
                'content' => 'Universitas menyediakan program beasiswa untuk mahasiswa berprestasi yang membutuhkan bantuan finansial. Beasiswa mencakup biaya kuliah dan living cost.',
                'image' => 'scholarship_program.jpg',
                'organization_id' => $organization->id,
                'is_published' => true,
                'published_at' => now()->subWeeks(2),
                'created_by' => $user->id,
                'created_at' => now()->subWeeks(2),
                'updated_at' => now()->subWeeks(2),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Fasilitas Laboratorium Baru',
                'content' => 'Universitas menambah fasilitas laboratorium baru dengan peralatan modern untuk mendukung kegiatan penelitian dan praktikum mahasiswa.',
                'image' => 'new_lab_facility.jpg',
                'organization_id' => $organization->id,
                'is_published' => false,
                'published_at' => null,
                'created_by' => $user->id,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('news')->insert($news);
    }
}