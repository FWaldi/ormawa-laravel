-- Ormawa UNP Database Seeding Script for phpMyAdmin
-- Generated: 2025-11-14
-- This script inserts initial data required for the application

USE `ormawa_unp`;

-- =============================================
-- INSERT: Default Admin User
-- =============================================
-- Password: admin123 (hashed with Laravel bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_email_verified`, `created_at`, `updated_at`) VALUES
('00000000-0000-0000-0000-000000000001', 'System Administrator', 'admin@ormawa-unp.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9w5GS', 'ADMIN', 1, NOW(), NOW());

-- =============================================
-- INSERT: Sample Organizations
-- =============================================
INSERT INTO `organizations` (`id`, `name`, `description`, `user_id`, `created_at`, `updated_at`) VALUES
('10000000-0000-0000-0000-000000000001', 'Badan Eksekutif Mahasiswa (BEM)', 'Badan Eksekutif Mahasiswa Universitas Negeri Padang', '00000000-0000-0000-0000-000000000001', NOW(), NOW()),
('10000000-0000-0000-0000-000000000002', 'Himpunan Mahasiswa Teknik Informatika', 'Himpunan Mahasiswa Jurusan Teknik Informatika', '00000000-0000-0000-0000-000000000001', NOW(), NOW()),
('10000000-0000-0000-0000-000000000003', 'Unit Kegiatan Mahasiswa Olahraga', 'Unit Kegiatan Mahasiswa Bidang Olahraga', '00000000-0000-0000-0000-000000000001', NOW(), NOW());

-- =============================================
-- INSERT: Sample Users for Organizations
-- =============================================
-- BEM User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `organization_id`, `is_email_verified`, `created_at`, `updated_at`) VALUES
('20000000-0000-0000-0000-000000000001', 'BEM Chairman', 'bem@ormawa-unp.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9w5GS', 'ORMAWA', '10000000-0000-0000-0000-000000000001', 1, NOW(), NOW());

-- Himpunan Teknik Informatika User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `organization_id`, `is_email_verified`, `created_at`, `updated_at`) VALUES
('20000000-0000-0000-0000-000000000002', 'Himtif Chairman', 'himtif@ormawa-unp.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9w5GS', 'ORMAWA', '10000000-0000-0000-0000-000000000002', 1, NOW(), NOW());

-- UKM Olahraga User
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `organization_id`, `is_email_verified`, `created_at`, `updated_at`) VALUES
('20000000-0000-0000-0000-000000000003', 'UKM Olahraga Chairman', 'ukm-olahraga@ormawa-unp.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj6hsxq9w5GS', 'ORMAWA', '10000000-0000-0000-0000-000000000003', 1, NOW(), NOW());

-- =============================================
-- INSERT: Sample Activities
-- =============================================
INSERT INTO `activities` (`id`, `title`, `description`, `organization_id`, `start_date`, `end_date`, `location`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
('30000000-0000-0000-0000-000000000001', 'Musyawarah Besar BEM', 'Musyawarah Besar tahunan Badan Eksekutif Mahasiswa untuk pemilihan ketua umum periode 2024/2025', '10000000-0000-0000-0000-000000000001', '2024-12-15 09:00:00', '2024-12-15 17:00:00', 'Auditorium Kampus UNP', 'PUBLISHED', '20000000-0000-0000-0000-000000000001', NOW(), NOW()),
('30000000-0000-0000-0000-000000000002', 'Seminar Teknologi AI', 'Seminar tentang perkembangan Artificial Intelligence dalam industri teknologi', '10000000-0000-0000-0000-000000000002', '2024-12-20 13:00:00', '2024-12-20 16:00:00', 'Lab Komputer Teknik Informatika', 'PUBLISHED', '20000000-0000-0000-0000-000000000002', NOW(), NOW()),
('30000000-0000-0000-0000-000000000003', 'Turnamen Futsal Antar UKM', 'Turnamen futsal tahunan antar Unit Kegiatan Mahasiswa se-UNP', '10000000-0000-0000-0000-000000000003', '2024-12-25 08:00:00', '2024-12-26 18:00:00', 'Lapangan Futsal Kampus', 'PUBLISHED', '20000000-0000-0000-0000-000000000003', NOW(), NOW());

-- =============================================
-- INSERT: Sample Announcements
-- =============================================
INSERT INTO `announcements` (`id`, `title`, `content`, `organization_id`, `created_by`, `created_at`, `updated_at`) VALUES
('40000000-0000-0000-0000-000000000001', 'Pendaftaran Anggota Baru BEM', 'Dibuka pendaftaran anggota baru BEM periode 2024/2025. Persyaratan: Mahasiswa aktif UNP, IPK minimal 3.00, dan memiliki pengalaman organisasi.', '10000000-0000-0000-0000-000000000001', '20000000-0000-0000-0000-000000000001', NOW(), NOW()),
('40000000-0000-0000-0000-000000000002', 'Workshop Web Development', 'Himtif akan menyelenggarakan workshop Web Development dengan materi HTML, CSS, dan JavaScript dasar. Gratis untuk anggota himpunan.', '10000000-0000-0000-0000-000000000002', '20000000-0000-0000-0000-000000000002', NOW(), NOW());

-- =============================================
-- INSERT: Sample News
-- =============================================
INSERT INTO `news` (`id`, `title`, `content`, `featured_image`, `created_by`, `created_at`, `updated_at`) VALUES
('50000000-0000-0000-0000-000000000001', 'UNP Raih Peringkat 10 Universitas Terbaik di Sumatera', 'Universitas Negeri Padang berhasil meraih peringkat 10 universitas terbaik di Sumatera berdasarkan pemeringkatan Kemenristekdikti tahun 2024.', NULL, '00000000-0000-0000-0000-000000000001', NOW(), NOW()),
('50000000-0000-0000-0000-000000000002', 'Mahasiswa UNP Juara 1 Lomba Debat Nasional', 'Tim debat mahasiswa UNP berhasil meraih juara 1 dalam Lomba Debat Nasional yang diselenggarakan di Jakarta.', NULL, '00000000-0000-0000-0000-000000000001', NOW(), NOW());

-- =============================================
-- INSERT: Sample Files (for testing file upload)
-- =============================================
INSERT INTO `files` (`id`, `filename`, `original_name`, `mime_type`, `size`, `path`, `uploaded_by`, `created_at`, `updated_at`) VALUES
('60000000-0000-0000-0000-000000000001', 'bem_logo_2024.png', 'BEM Logo 2024.png', 'image/png', 125832, 'uploads/2024/11/bem_logo_2024.png', '20000000-0000-0000-0000-000000000001', NOW(), NOW()),
('60000000-0000-0000-0000-000000000002', 'himtif_structure.pdf', 'Struktur Organisasi HIMTIF.pdf', 'application/pdf', 452156, 'uploads/2024/11/himtif_structure.pdf', '20000000-0000-0000-0000-000000000002', NOW(), NOW());

-- =============================================
-- UPDATE: Organization Users with proper foreign key references
-- =============================================
UPDATE `users` SET `organization_id` = '10000000-0000-0000-0000-000000000001' WHERE `id` = '20000000-0000-0000-0000-000000000001';
UPDATE `users` SET `organization_id` = '10000000-0000-0000-0000-000000000002' WHERE `id` = '20000000-0000-0000-0000-000000000002';
UPDATE `users` SET `organization_id` = '10000000-0000-0000-0000-000000000003' WHERE `id` = '20000000-0000-0000-0000-000000000003';

-- =============================================
-- COMPLETION MESSAGE
-- =============================================
SELECT 'Database seeding completed successfully!' as message;