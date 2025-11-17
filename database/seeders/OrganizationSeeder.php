<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user ID
        $adminUser = DB::table('users')->where('role', 'ADMIN')->first();
        $adminId = $adminUser ? $adminUser->id : null;
        
        $organizations = [
            [
                'id' => Str::uuid(),
                'name' => 'Badan Eksekutif Mahasiswa',
                'type' => 'ORMAWA',
                'description' => 'Organisasi kemahasiswaan tingkat universitas yang mewakili seluruh mahasiswa.',
                'contact' => 'bem@university.ac.id',
                'social_media' => json_encode([
                    'instagram' => '@bem_university',
                    'twitter' => '@bem_university',
                    'facebook' => 'BEM University'
                ]),
                'user_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Himpunan Mahasiswa Teknik Informatika',
                'type' => 'ORMAWA',
                'description' => 'Organisasi mahasiswa program studi Teknik Informatika.',
                'contact' => 'himtif@university.ac.id',
                'social_media' => json_encode([
                    'instagram' => '@himtif_university',
                    'linkedin' => 'HIMTIF University'
                ]),
                'user_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Unit Kegiatan Mahasiswa Olahraga',
                'type' => 'UKM',
                'description' => 'Unit kegiatan mahasiswa yang berfokus pada pengembangan olahraga.',
                'contact' => 'ukm_olahraga@university.ac.id',
                'social_media' => json_encode([
                    'instagram' => '@ukm_olahraga_university'
                ]),
                'user_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('organizations')->insert($organizations);
    }
}