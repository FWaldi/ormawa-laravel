<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user for seeding
        $user = DB::table('users')->where('role', 'ADMIN')->first();

        if (!$user) {
            $this->command->warn('Please run UserSeeder first');
            return;
        }

        $announcements = [
            [
                'id' => Str::uuid(),
                'title' => 'Pendaftaran Organisasi Mahasiswa Tahun Akademik 2025/2026',
                'content' => 'Dibuka pendaftaran anggota baru untuk seluruh organisasi mahasiswa. Pendaftaran dibuka mulai 1 Januari 2025 hingga 31 Januari 2025. Syarat dan ketentuan berlaku.',
                'category' => 'Pendaftaran',
                'image' => 'registration_announcement.jpg',
                'is_pinned' => true,
                'created_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Libur Semester Ganjil 2025',
                'content' => 'Berdasarkan kalender akademik, libur semester ganjil akan dimulai pada 20 Desember 2025 hingga 5 Januari 2026. Seluruh kegiatan perkuliahan akan diliburkan.',
                'category' => 'Akademik',
                'image' => null,
                'is_pinned' => true,
                'created_by' => $user->id,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Workshop Pengembangan Karir',
                'content' => 'Career Center akan menyelenggarakan workshop pengembangan karir untuk mahasiswa semester akhir. Workshop akan membahas CV writing, interview skills, dan networking.',
                'category' => 'Workshop',
                'image' => 'career_workshop.jpg',
                'is_pinned' => false,
                'created_by' => $user->id,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];

        DB::table('announcements')->insert($announcements);
    }
}