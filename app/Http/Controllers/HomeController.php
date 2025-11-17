<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Use sample data to avoid database connection issues during initial setup
        $organizations = collect([
            [
                'id' => 1,
                'name' => 'Badan Eksekutif Mahasiswa',
                'acronym' => 'BEM UNP',
                'category' => 'BEM',
                'faculty' => 'Fakultas Ekonomi',
                'description' => 'Lembaga eksekutif tertinggi mahasiswa UNP yang mewakili aspirasi kemahasiswaan dan mengkoordinasikan seluruh kegiatan ormawa.',
                'logo' => null,
                'members_count' => 500,
                'activities_count' => 50
            ],
            [
                'id' => 2,
                'name' => 'UKM Paduan Suara Symphony',
                'acronym' => 'SYMPHONY',
                'category' => 'UKM',
                'faculty' => 'Fakultas Ilmu Budaya',
                'description' => 'Unit Kegiatan Mahasiswa yang mengembangkan bakat vokal mahasiswa UNP melalui paduan suara yang harmonis dan berkualitas.',
                'logo' => null,
                'members_count' => 80,
                'activities_count' => 25
            ],
            [
                'id' => 3,
                'name' => 'Himpunan Mahasiswa Matematika',
                'acronym' => 'HIMA MAT',
                'category' => 'HIMA',
                'faculty' => 'FMIPA',
                'description' => 'Organisasi mahasiswa Program Studi Matematika FMIPA UNP yang fokus pada pengembangan akademik dan kompetensi matematika.',
                'logo' => null,
                'members_count' => 120,
                'activities_count' => 30
            ]
        ]);

        $news = collect([
            [
                'id' => 1,
                'title' => 'Pembukaan Masa Orientasi Mahasiswa Baru 2024',
                'content' => 'BEM UNP mengadakan acara orientasi untuk mahasiswa baru dengan berbagai kegiatan menarik dan informatif untuk menyambut mahasiswa baru.',
                'category' => 'Pengumuman',
                'organization_name' => 'BEM UNP',
                'organization_acronym' => 'BEM UNP',
                'published_at' => now()->subDays(2)->format('d M Y'),
                'image' => null
            ],
            [
                'id' => 2,
                'title' => 'Festival Seni Mahasiswa UNP 2024',
                'content' => 'UKM Symphony akan mengadakan festival seni tahunan dengan penampilan paduan suara dan berbagai pertunjukan seni mahasiswa.',
                'category' => 'Kegiatan',
                'organization_name' => 'UKM Symphony',
                'organization_acronym' => 'SYMPHONY',
                'published_at' => now()->subDays(5)->format('d M Y'),
                'image' => null
            ],
            [
                'id' => 3,
                'title' => 'Rekrutmen Anggota Baru HIMA Matematika',
                'content' => 'HIMA Matematika membuka kesempatan bergabung untuk mahasiswa matematika dengan berbagai program pengembangan kompetensi.',
                'category' => 'Rekrutmen',
                'organization_name' => 'HIMA Matematika',
                'organization_acronym' => 'HIMA MAT',
                'published_at' => now()->subWeek()->format('d M Y'),
                'image' => null
            ]
        ]);

        $announcements = collect([
            [
                'id' => 1,
                'title' => 'Pengumuman Penting: Perubahan Jadwal Kuliah',
                'content' => 'Diberitahukan kepada seluruh mahasiswa bahwa terdapat perubahan jadwal kuliah untuk semester ini. Silakan cek portal akademik untuk detail lebih lanjut.',
                'category' => 'Pengumuman',
                'published_at' => now()->subDays(1)->format('d M Y')
            ],
            [
                'id' => 2,
                'title' => 'Pendaftaran Beasiswa Unggulan Terbuka',
                'content' => 'Beasiswa unggulan Universitas Negeri Padang kini dibuka untuk mahasiswa berprestasi. Segera daftar sebelum batas waktu.',
                'category' => 'Pengumuman',
                'published_at' => now()->subDays(3)->format('d M Y')
            ],
            [
                'id' => 3,
                'title' => 'Workshop Kepemimpinan Mahasiswa',
                'content' => 'BEM UNP mengadakan workshop kepemimpinan untuk meningkatkan soft skills mahasiswa. Pendaftaran gratis.',
                'category' => 'Kegiatan',
                'published_at' => now()->subDays(4)->format('d M Y')
            ]
        ]);

        return view('home', compact('organizations', 'news', 'announcements'));
    }
}