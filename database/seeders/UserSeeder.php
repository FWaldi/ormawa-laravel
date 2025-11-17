<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => Str::uuid(),
                'name' => 'Administrator',
                'email' => 'admin@university.ac.id',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
                'avatar' => null,
                'organization_id' => null,
                'is_email_verified' => true,
                'email_verification_code' => null,
                'email_verification_expires' => null,
                'password_reset_token' => null,
                'password_reset_expires' => null,
                'google_id' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Kemahasiswaan Staff',
                'email' => 'kemahasiswaan@university.ac.id',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'KEMAHASISWAAN',
                'avatar' => null,
                'organization_id' => null,
                'is_email_verified' => true,
                'email_verification_code' => null,
                'email_verification_expires' => null,
                'password_reset_token' => null,
                'password_reset_expires' => null,
                'google_id' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'BEM Chairman',
                'email' => 'bem@university.ac.id',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'ORMAWA',
                'avatar' => null,
                'organization_id' => null,
                'is_email_verified' => true,
                'email_verification_code' => null,
                'email_verification_expires' => null,
                'password_reset_token' => null,
                'password_reset_expires' => null,
                'google_id' => null,
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}