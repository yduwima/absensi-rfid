-- Sample Data for Testing
-- Import this after database.sql for testing purposes

USE `absensi_rfid`;

-- Sample Tahun Ajaran
INSERT INTO `tahun_ajaran` (`tahun_ajaran`, `tanggal_mulai`, `tanggal_selesai`, `is_active`) VALUES
('2023/2024', '2023-07-01', '2024-06-30', 0),
('2024/2025', '2024-07-01', '2025-06-30', 1);

-- Sample Semester
INSERT INTO `semester` (`tahun_ajaran_id`, `semester`, `tanggal_mulai`, `tanggal_selesai`, `is_active`) VALUES
(1, 'Ganjil', '2023-07-01', '2023-12-31', 0),
(1, 'Genap', '2024-01-01', '2024-06-30', 0),
(2, 'Ganjil', '2024-07-01', '2024-12-31', 1),
(2, 'Genap', '2025-01-01', '2025-06-30', 0);

-- Sample Guru
INSERT INTO `guru` (`nip`, `nama`, `email`, `password`, `no_hp`, `jenis_kelamin`, `alamat`, `rfid_uid`, `role`) VALUES
('196801011990031001', 'Drs. Budi Santoso', 'budi.santoso@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567801', 'L', 'Jl. Guru No. 1', '1234567890', 'Guru'),
('197205151995122001', 'Dra. Siti Nurhasanah, M.Pd', 'siti.nurhasanah@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567802', 'P', 'Jl. Guru No. 2', '1234567891', 'Guru'),
('198003201999031002', 'Ahmad Fauzi, S.Pd', 'ahmad.fauzi@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567803', 'L', 'Jl. Guru No. 3', '1234567892', 'Guru,Piket'),
('198509102010012001', 'Dewi Lestari, S.Pd', 'dewi.lestari@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567804', 'P', 'Jl. Guru No. 4', '1234567893', 'Guru'),
('199012152015051001', 'Rudi Hermawan, S.Pd', 'rudi.hermawan@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567805', 'L', 'Jl. Guru No. 5', '1234567894', 'Guru,BK');

-- Sample Kelas
INSERT INTO `kelas` (`nama_kelas`, `tingkat`, `wali_kelas_id`, `tahun_ajaran_id`) VALUES
('X-IPA 1', 10, 2, 2),
('X-IPA 2', 10, 3, 2),
('XI-IPA 1', 11, 4, 2),
('XI-IPA 2', 11, NULL, 2),
('XII-IPA 1', 12, NULL, 2),
('XII-IPA 2', 12, NULL, 2);

-- Sample Siswa
INSERT INTO `siswa` (`nis`, `nisn`, `nama`, `kelas_id`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `nama_ortu`, `no_hp_ortu`, `rfid_uid`) VALUES
('2024001', '0061234567', 'Andi Pratama', 1, 'L', 'Jakarta', '2008-01-15', 'Jl. Siswa No. 1', 'Bapak Pratama', '081234560001', 'RFID001'),
('2024002', '0061234568', 'Budi Setiawan', 1, 'L', 'Jakarta', '2008-02-20', 'Jl. Siswa No. 2', 'Bapak Setiawan', '081234560002', 'RFID002'),
('2024003', '0061234569', 'Citra Dewi', 1, 'P', 'Jakarta', '2008-03-10', 'Jl. Siswa No. 3', 'Ibu Dewi', '081234560003', 'RFID003'),
('2024004', '0061234570', 'Dian Kusuma', 1, 'P', 'Jakarta', '2008-04-05', 'Jl. Siswa No. 4', 'Bapak Kusuma', '081234560004', 'RFID004'),
('2024005', '0061234571', 'Eko Wahyudi', 1, 'L', 'Jakarta', '2008-05-12', 'Jl. Siswa No. 5', 'Bapak Wahyudi', '081234560005', 'RFID005'),
('2024006', '0061234572', 'Fitri Handayani', 2, 'P', 'Jakarta', '2008-06-18', 'Jl. Siswa No. 6', 'Ibu Handayani', '081234560006', 'RFID006'),
('2024007', '0061234573', 'Gilang Ramadhan', 2, 'L', 'Jakarta', '2008-07-22', 'Jl. Siswa No. 7', 'Bapak Ramadhan', '081234560007', 'RFID007'),
('2024008', '0061234574', 'Hani Safitri', 2, 'P', 'Jakarta', '2008-08-30', 'Jl. Siswa No. 8', 'Ibu Safitri', '081234560008', 'RFID008'),
('2023009', '0061234575', 'Indra Gunawan', 3, 'L', 'Jakarta', '2007-01-10', 'Jl. Siswa No. 9', 'Bapak Gunawan', '081234560009', 'RFID009'),
('2023010', '0061234576', 'Juwita Sari', 3, 'P', 'Jakarta', '2007-02-14', 'Jl. Siswa No. 10', 'Ibu Sari', '081234560010', 'RFID010');

-- Sample Mata Pelajaran
INSERT INTO `mapel` (`kode_mapel`, `nama_mapel`) VALUES
('MAT', 'Matematika'),
('FIS', 'Fisika'),
('KIM', 'Kimia'),
('BIO', 'Biologi'),
('ING', 'Bahasa Inggris'),
('IND', 'Bahasa Indonesia'),
('SEJ', 'Sejarah'),
('GEO', 'Geografi'),
('EKO', 'Ekonomi'),
('SOS', 'Sosiologi');

-- Sample Jadwal
INSERT INTO `jadwal` (`kelas_id`, `mapel_id`, `guru_id`, `semester_id`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(1, 1, 2, 3, 'Senin', '07:00:00', '08:30:00'),
(1, 2, 3, 3, 'Senin', '08:30:00', '10:00:00'),
(1, 3, 4, 3, 'Selasa', '07:00:00', '08:30:00'),
(2, 1, 2, 3, 'Rabu', '07:00:00', '08:30:00'),
(2, 2, 3, 3, 'Rabu', '08:30:00', '10:00:00');

-- Sample Hari Libur
INSERT INTO `hari_libur` (`tanggal`, `keterangan`) VALUES
('2024-12-25', 'Hari Natal'),
('2024-12-26', 'Cuti Bersama Natal'),
('2025-01-01', 'Tahun Baru 2025'),
('2025-03-12', 'Hari Raya Nyepi'),
('2025-03-31', 'Isra Mikraj'),
('2025-04-10', 'Idul Fitri 1446 H'),
('2025-04-11', 'Idul Fitri 1446 H'),
('2025-05-01', 'Hari Buruh'),
('2025-05-29', 'Kenaikan Isa Almasih'),
('2025-06-01', 'Hari Lahir Pancasila'),
('2025-08-17', 'Hari Kemerdekaan RI');

-- Activate WA for sample classes
INSERT INTO `wa_kelas_aktif` (`kelas_id`, `is_active`) VALUES
(1, 1),
(2, 1),
(3, 1);

-- Sample Absensi Hari Ini (untuk testing)
INSERT INTO `absensi_harian` (`user_type`, `user_id`, `tanggal`, `jam_masuk`, `jam_pulang`, `status_masuk`, `keterlambatan_menit`) VALUES
('siswa', 1, CURDATE(), '06:55:00', NULL, 'Tepat Waktu', 0),
('siswa', 2, CURDATE(), '07:10:00', NULL, 'Terlambat', 10),
('siswa', 3, CURDATE(), '07:00:00', NULL, 'Tepat Waktu', 0),
('guru', 2, CURDATE(), '06:50:00', NULL, 'Tepat Waktu', 0),
('guru', 3, CURDATE(), '07:05:00', NULL, 'Terlambat', 5);
