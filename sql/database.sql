-- Database for RFID Attendance System
-- Created: 2025-12-23

CREATE DATABASE IF NOT EXISTS `absensi_rfid` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `absensi_rfid`;

-- Table: settings (Pengaturan Sekolah)
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(255) DEFAULT NULL,
  `alamat_sekolah` text DEFAULT NULL,
  `nama_kepala_sekolah` varchar(255) DEFAULT NULL,
  `logo_sekolah` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`id`, `nama_sekolah`, `alamat_sekolah`, `nama_kepala_sekolah`, `logo_sekolah`) 
VALUES (1, 'SMA Negeri 1', 'Jl. Pendidikan No. 1, Jakarta', 'Drs. Ahmad Suryadi, M.Pd', NULL);

-- Table: jam_kerja (Pengaturan Jam Kerja per Hari)
CREATE TABLE `jam_kerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `is_hari_kerja` tinyint(1) DEFAULT 1,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `hari` (`hari`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default jam kerja
INSERT INTO `jam_kerja` (`hari`, `is_hari_kerja`, `jam_masuk`, `jam_pulang`) VALUES
('Senin', 1, '07:00:00', '15:00:00'),
('Selasa', 1, '07:00:00', '15:00:00'),
('Rabu', 1, '07:00:00', '15:00:00'),
('Kamis', 1, '07:00:00', '15:00:00'),
('Jumat', 1, '07:00:00', '11:30:00'),
('Sabtu', 1, '07:00:00', '12:00:00'),
('Minggu', 0, NULL, NULL);

-- Table: hari_libur (Hari Libur Nasional)
CREATE TABLE `hari_libur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tahun_ajaran
CREATE TABLE `tahun_ajaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_ajaran` varchar(20) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default tahun ajaran
INSERT INTO `tahun_ajaran` (`tahun_ajaran`, `tanggal_mulai`, `tanggal_selesai`, `is_active`) 
VALUES ('2024/2025', '2024-07-01', '2025-06-30', 1);

-- Table: semester
CREATE TABLE `semester` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` int(11) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`),
  CONSTRAINT `fk_semester_tahun_ajaran` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default semester
INSERT INTO `semester` (`tahun_ajaran_id`, `semester`, `tanggal_mulai`, `tanggal_selesai`, `is_active`) 
VALUES (1, 'Ganjil', '2024-07-01', '2024-12-31', 1);

-- Table: kelas
CREATE TABLE `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  `tingkat` int(11) NOT NULL,
  `wali_kelas_id` int(11) DEFAULT NULL,
  `tahun_ajaran_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `wali_kelas_id` (`wali_kelas_id`),
  KEY `tahun_ajaran_id` (`tahun_ajaran_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: guru (Data Guru dan Staff)
CREATE TABLE `guru` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(50) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Guru',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nip` (`nip`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `rfid_uid` (`rfid_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user
INSERT INTO `guru` (`nip`, `nama`, `email`, `password`, `jenis_kelamin`, `role`) 
VALUES ('admin', 'Administrator', 'admin@sekolah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'L', 'Admin');
-- Password: password

-- Table: siswa (Data Siswa)
CREATE TABLE `siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nis` varchar(50) NOT NULL,
  `nisn` varchar(50) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `nama_ortu` varchar(255) DEFAULT NULL,
  `no_hp_ortu` varchar(20) DEFAULT NULL,
  `rfid_uid` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  UNIQUE KEY `nisn` (`nisn`),
  UNIQUE KEY `rfid_uid` (`rfid_uid`),
  KEY `kelas_id` (`kelas_id`),
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: mapel (Mata Pelajaran)
CREATE TABLE `mapel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(20) NOT NULL,
  `nama_mapel` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_mapel` (`kode_mapel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jadwal (Jadwal Pelajaran)
CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `mapel_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `mapel_id` (`mapel_id`),
  KEY `guru_id` (`guru_id`),
  KEY `semester_id` (`semester_id`),
  CONSTRAINT `fk_jadwal_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_mapel` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jadwal_semester` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: absensi_harian (Absensi Masuk/Pulang via RFID)
CREATE TABLE `absensi_harian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('siswa','guru') NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status_masuk` enum('Tepat Waktu','Terlambat') DEFAULT NULL,
  `keterlambatan_menit` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_absensi_user` (`user_type`, `user_id`, `tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jurnal (Jurnal Mengajar Guru)
CREATE TABLE `jurnal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jadwal_id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `materi` text NOT NULL,
  `kegiatan` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `jadwal_id` (`jadwal_id`),
  KEY `guru_id` (`guru_id`),
  CONSTRAINT `fk_jurnal_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jurnal_guru` FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: absensi_mapel (Absensi Per Mata Pelajaran)
CREATE TABLE `absensi_mapel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jurnal_id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `status` enum('H','S','I','A') NOT NULL DEFAULT 'H',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `jurnal_id` (`jurnal_id`),
  KEY `siswa_id` (`siswa_id`),
  CONSTRAINT `fk_absensi_mapel_jurnal` FOREIGN KEY (`jurnal_id`) REFERENCES `jurnal` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_absensi_mapel_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: izin_siswa (Izin Masuk/Keluar Tengah KBM)
CREATE TABLE `izin_siswa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `jenis_izin` enum('Datang Terlambat','Pulang Lebih Awal','Sakit','Izin') NOT NULL,
  `waktu` time NOT NULL,
  `alasan` text DEFAULT NULL,
  `guru_piket_id` int(11) DEFAULT NULL,
  `wali_kelas_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `guru_piket_id` (`guru_piket_id`),
  KEY `wali_kelas_id` (`wali_kelas_id`),
  CONSTRAINT `fk_izin_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_izin_guru_piket` FOREIGN KEY (`guru_piket_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_izin_wali_kelas` FOREIGN KEY (`wali_kelas_id`) REFERENCES `guru` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: wa_config (Konfigurasi WhatsApp)
CREATE TABLE `wa_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_api` varchar(255) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `sender_number` varchar(20) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `template_masuk` text DEFAULT NULL,
  `template_pulang` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `jam_notifikasi_wali` time DEFAULT '09:00:00',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default WA config
INSERT INTO `wa_config` (`id`, `template_masuk`, `template_pulang`, `is_active`) VALUES
(1, 
'Yth. {nama_ortu},\n\nKami informasikan bahwa {nama_siswa} dari kelas {kelas} telah hadir di {nama_sekolah} pada pukul {jam_masuk}, tanggal {tanggal}.\n\n{keterangan_terlambat}\n\nTerima kasih.',
'Yth. {nama_ortu},\n\nKami informasikan bahwa {nama_siswa} dari kelas {kelas} telah pulang dari {nama_sekolah} pada pukul {jam_pulang}, tanggal {tanggal}.\n\nTerima kasih.',
0);

-- Table: wa_kelas_aktif (Kelas yang Aktif Menerima Notifikasi WA)
CREATE TABLE `wa_kelas_aktif` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kelas_id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelas_id` (`kelas_id`),
  CONSTRAINT `fk_wa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: wa_queue (Antrian Notifikasi WhatsApp)
CREATE TABLE `wa_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_hp` varchar(20) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `retry_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: monitoring_bk (Monitoring Bimbingan Konseling)
CREATE TABLE `monitoring_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `total_alpha` int(11) DEFAULT 0,
  `total_terlambat` int(11) DEFAULT 0,
  `status` enum('Normal','Perlu Perhatian','Panggilan') DEFAULT 'Normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  CONSTRAINT `fk_monitoring_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: surat_bk (Surat Pemanggilan/Peringatan BK)
CREATE TABLE `surat_bk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siswa_id` int(11) NOT NULL,
  `nomor_surat` varchar(100) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `hari` varchar(20) DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `jenis_surat` enum('Pemanggilan','Peringatan','Lainnya') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `siswa_id` (`siswa_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `fk_surat_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_surat_creator` FOREIGN KEY (`created_by`) REFERENCES `guru` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: rfid_log (Log Semua Aktivitas RFID)
CREATE TABLE `rfid_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rfid_uid` varchar(50) NOT NULL,
  `user_type` enum('siswa','guru','unknown') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` enum('masuk','pulang') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rfid_uid` (`rfid_uid`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
