# Sistem Absensi RFID

Sistem Absensi Siswa dan Guru berbasis kartu RFID menggunakan CodeIgniter 3 dan Tailwind CSS.

## 🎯 Fitur Utama

### Absensi RFID (Tanpa Login)
- Scan kartu RFID untuk absensi masuk dan pulang
- Tampilan realtime data absensi
- Audio feedback untuk setiap scan
- Riwayat absensi hari ini
- Auto-focus input untuk RFID reader

### Admin Panel
- **Dashboard**: Statistik total siswa, guru, dan absensi hari ini
- **Pengaturan Sekolah**: Nama, alamat, kepala sekolah, logo
- **Jam Kerja & Hari Libur**: Setting hari kerja dan hari libur nasional
- **Data Master**:
  - Tahun Ajaran & Semester
  - Kelas dan Wali Kelas
  - Naik Kelas (bulk update)
  - Data Siswa (CRUD, Import/Export Excel)
  - Data Guru (CRUD, Import/Export Excel)
  - Mata Pelajaran
  - Jadwal Pelajaran
- **WhatsApp Notifikasi**:
  - Konfigurasi API WhatsApp
  - Template pesan untuk masuk/pulang
  - Pilih kelas yang menerima notifikasi
  - Sistem antrian pesan
- **Laporan**:
  - Laporan Absensi Siswa & Guru (bulanan)
  - Rekap per bulan, per individu, per semester
  - Export ke PDF dan Excel

### Guru Panel
- Dashboard dengan jadwal mengajar hari ini
- Isi Jurnal Mengajar
- Input absensi siswa per mata pelajaran (H/S/I/A)
- Laporan kinerja
- Profile management

### Wali Kelas
- Semua fitur Guru +
- Input Sakit/Izin untuk siswa di kelas perwalian

### Guru Piket
- Semua fitur Guru +
- Input izin siswa masuk/keluar tengah KBM
- Rekap izin

### BK (Bimbingan Konseling)
- Dashboard statistik siswa bermasalah
- Monitoring otomatis:
  - Siswa alpha ≥ 3x dalam sebulan
  - Siswa terlambat ≥ 5x dalam sebulan
- Generate surat pemanggilan/peringatan
- Export surat ke PDF

## 🛠️ Instalasi

### Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx dengan mod_rewrite
- Composer (opsional)

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/yduwima/absensi-rfid.git
   cd absensi-rfid
   ```

2. **Download CodeIgniter 3**
   
   Download CodeIgniter 3 dari [https://codeigniter.com/download](https://codeigniter.com/download) dan ekstrak folder `system` ke root project.

3. **Setup Database**
   
   - Buat database baru:
     ```sql
     CREATE DATABASE absensi_rfid;
     ```
   
   - Import file SQL:
     ```bash
     mysql -u root -p absensi_rfid < sql/database.sql
     ```
   
   - Konfigurasi database di `application/config/database.php`:
     ```php
     'hostname' => 'localhost',
     'username' => 'root',
     'password' => '',
     'database' => 'absensi_rfid',
     ```

4. **Konfigurasi Base URL**
   
   Edit `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost/absensi-rfid/';
   ```

5. **Set Permissions**
   ```bash
   chmod -R 777 assets/uploads
   chmod -R 777 application/cache
   chmod -R 777 application/logs
   ```

6. **Akses Aplikasi**
   
   - Admin Panel: `http://localhost/absensi-rfid/login`
   - RFID Page: `http://localhost/absensi-rfid/rfid`

## 🔐 Default Login

- **Email**: admin@sekolah.com
- **Password**: password

⚠️ **PENTING**: Segera ganti password setelah login pertama!

## 📱 Konfigurasi WhatsApp

1. Login sebagai Admin
2. Buka menu **WhatsApp** → **Pengaturan WA**
3. Masukkan:
   - URL API (contoh: Fonnte, Wablas, dll)
   - API Key
   - Sender Number
4. Kustomisasi template pesan dengan placeholder:
   - `{nama_siswa}` - Nama siswa
   - `{kelas}` - Kelas siswa
   - `{jam_masuk}` / `{jam_pulang}` - Waktu absensi
   - `{tanggal}` - Tanggal absensi
   - `{keterangan_terlambat}` - Info keterlambatan
   - `{nama_sekolah}` - Nama sekolah
   - `{nama_ortu}` - Nama orang tua
5. Pilih kelas yang akan menerima notifikasi
6. Aktifkan notifikasi

### Background Process untuk WA Queue

Tambahkan cron job untuk memproses antrian WA:
```bash
*/5 * * * * curl http://localhost/absensi-rfid/api/wa/process_queue
```

## 🎓 Cara Penggunaan

### Absensi RFID

1. Buka halaman `http://localhost/absensi-rfid/rfid` di komputer yang terhubung dengan RFID reader
2. Pastikan input sudah auto-focus
3. Tap kartu RFID pada reader
4. Sistem akan otomatis:
   - Membaca UID kartu
   - Mencatat absensi (masuk/pulang)
   - Menampilkan informasi user
   - Mengirim notifikasi WA (jika aktif)
   - Update riwayat realtime

### Input Absensi Per Mapel (Guru)

1. Login sebagai Guru
2. Buka **Jurnal & Jadwal**
3. Pilih jadwal hari ini
4. Isi materi, kegiatan, catatan
5. Input kehadiran siswa: H (Hadir), S (Sakit), I (Izin), A (Alpha)
6. Simpan

## 📊 Database Schema

Database terdiri dari 18 tabel:
- `settings` - Pengaturan sekolah
- `jam_kerja` - Jam kerja per hari
- `hari_libur` - Hari libur nasional
- `tahun_ajaran` - Tahun ajaran
- `semester` - Semester
- `kelas` - Data kelas
- `guru` - Data guru dan staff
- `siswa` - Data siswa
- `mapel` - Mata pelajaran
- `jadwal` - Jadwal pelajaran
- `absensi_harian` - Absensi masuk/pulang via RFID
- `jurnal` - Jurnal mengajar guru
- `absensi_mapel` - Absensi per mata pelajaran
- `izin_siswa` - Izin siswa
- `wa_config` - Konfigurasi WhatsApp
- `wa_kelas_aktif` - Kelas dengan notifikasi WA aktif
- `wa_queue` - Antrian pesan WhatsApp
- `monitoring_bk` - Monitoring BK
- `surat_bk` - Surat BK
- `rfid_log` - Log aktivitas RFID

## 🎨 Tech Stack

- **Backend**: CodeIgniter 3
- **Frontend**: Tailwind CSS, jQuery
- **Database**: MySQL
- **Libraries**:
  - PhpSpreadsheet (Import/Export Excel)
  - TCPDF/DomPDF (Generate PDF)
  - SweetAlert2 (Notifikasi)
  - Font Awesome (Icons)

## 🔒 Keamanan

- Password hashing menggunakan `password_hash()`
- CSRF Protection aktif
- Session management
- Input validation dan sanitization
- XSS filtering
- Role-based access control

## 📝 Struktur Folder

```
absensi-rfid/
├── application/
│   ├── config/
│   ├── controllers/
│   │   ├── admin/
│   │   ├── guru/
│   │   ├── bk/
│   │   ├── Auth.php
│   │   └── Rfid.php
│   ├── models/
│   ├── views/
│   │   ├── templates/
│   │   ├── admin/
│   │   ├── guru/
│   │   ├── bk/
│   │   ├── rfid/
│   │   └── auth/
│   ├── libraries/
│   └── helpers/
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/
├── sql/
│   └── database.sql
├── system/ (CodeIgniter core)
├── .htaccess
└── index.php
```

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan MIT License.

## 👨‍💻 Developer

Dikembangkan dengan ❤️ untuk sistem pendidikan Indonesia.

## 📞 Support

Jika ada pertanyaan atau issues, silakan buat issue di GitHub atau hubungi developer.

---

**Note**: Sistem ini memerlukan RFID reader hardware yang kompatibel. Pastikan RFID reader terkonfigurasi untuk mengirim UID kartu diikuti dengan Enter key.
