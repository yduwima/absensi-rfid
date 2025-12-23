# PANDUAN INSTALASI SISTEM ABSENSI RFID

## Persiapan

### 1. Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache atau Nginx dengan mod_rewrite enabled
- XAMPP/WAMP/LAMP (untuk development lokal)

### 2. Download Dependencies
- **CodeIgniter 3**: Download dari https://codeigniter.com/download
  - Versi: 3.1.13 atau terbaru
  - Ekstrak folder `system` ke root project

## Langkah Instalasi

### Step 1: Clone atau Download Project
```bash
git clone https://github.com/yduwima/absensi-rfid.git
cd absensi-rfid
```

### Step 2: Download CodeIgniter System Folder
1. Download CodeIgniter 3.1.13 dari https://codeigniter.com/download
2. Ekstrak file zip
3. Copy folder `system` dari hasil ekstrak ke folder root project `absensi-rfid/`
4. Struktur folder seharusnya seperti ini:
   ```
   absensi-rfid/
   ├── application/
   ├── assets/
   ├── sql/
   ├── system/        <-- Folder dari CodeIgniter
   ├── .htaccess
   ├── index.php
   └── README.md
   ```

### Step 3: Setup Database

#### 3.1. Buat Database
```sql
CREATE DATABASE absensi_rfid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 3.2. Import Database Schema
```bash
# Via command line
mysql -u root -p absensi_rfid < sql/database.sql

# Atau via phpMyAdmin:
# 1. Buka phpMyAdmin
# 2. Pilih database absensi_rfid
# 3. Klik tab Import
# 4. Choose file: sql/database.sql
# 5. Klik Go
```

#### 3.3. Konfigurasi Database
Edit file `application/config/database.php`:
```php
$db['default'] = array(
    'dsn'      => '',
    'hostname' => 'localhost',
    'username' => 'root',           // Sesuaikan dengan username MySQL Anda
    'password' => '',               // Sesuaikan dengan password MySQL Anda
    'database' => 'absensi_rfid',
    'dbdriver' => 'mysqli',
    // ... (biarkan setting lainnya)
);
```

### Step 4: Konfigurasi Base URL

Edit file `application/config/config.php`:
```php
// Untuk XAMPP Windows
$config['base_url'] = 'http://localhost/absensi-rfid/';

// Untuk XAMPP Linux/Mac
$config['base_url'] = 'http://localhost/absensi-rfid/';

// Untuk production/server
$config['base_url'] = 'http://yourdomain.com/';
```

### Step 5: Set File Permissions (Linux/Mac)
```bash
# Berikan permission untuk upload dan cache folders
chmod -R 777 assets/uploads
chmod -R 777 application/cache
chmod -R 777 application/logs

# Atau dengan chown (lebih aman)
sudo chown -R www-data:www-data assets/uploads
sudo chown -R www-data:www-data application/cache
sudo chown -R www-data:www-data application/logs
```

### Step 6: Konfigurasi Virtual Host (Opsional)

Untuk Apache, edit `httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName absensi.local
    DocumentRoot "C:/xampp/htdocs/absensi-rfid"
    <Directory "C:/xampp/htdocs/absensi-rfid">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Edit hosts file:
```
# Windows: C:\Windows\System32\drivers\etc\hosts
# Linux/Mac: /etc/hosts

127.0.0.1 absensi.local
```

### Step 7: Test Instalasi

1. Buka browser dan akses:
   - **Login Admin**: http://localhost/absensi-rfid/login
   - **RFID Page**: http://localhost/absensi-rfid/rfid

2. Login dengan kredensial default:
   - **Email**: admin@sekolah.com
   - **Password**: password

3. Jika berhasil, Anda akan masuk ke Dashboard Admin

## Konfigurasi Lanjutan

### 1. Pengaturan Sekolah
1. Login sebagai Admin
2. Menu **Pengaturan** → **Pengaturan Sekolah**
3. Isi:
   - Nama Sekolah
   - Alamat Sekolah
   - Nama Kepala Sekolah
   - Upload Logo Sekolah

### 2. Setup Jam Kerja
1. Menu **Pengaturan** → **Jam Kerja & Hari Libur**
2. Atur jam masuk dan pulang untuk setiap hari
3. Centang/uncheck untuk hari kerja/libur

### 3. Input Data Master
1. **Tahun Ajaran**: Buat tahun ajaran aktif
2. **Semester**: Buat semester (Ganjil/Genap)
3. **Kelas**: Tambah kelas-kelas yang ada
4. **Guru**: Input data guru (bisa manual atau import Excel)
5. **Siswa**: Input data siswa (bisa manual atau import Excel)

### 4. Setup RFID Card
- Untuk setiap siswa/guru, assign RFID UID di field `rfid_uid`
- RFID UID bisa didapat dengan scan kartu di halaman RFID
- Pastikan setiap RFID UID unik

### 5. Konfigurasi WhatsApp (Opsional)
1. Daftar di penyedia WhatsApp API (Fonnte, Wablas, dll)
2. Dapatkan API Key dan URL endpoint
3. Login Admin → Menu **WhatsApp** → **Pengaturan WA**
4. Masukkan:
   - URL API
   - API Key
   - Sender Number
5. Kustomisasi template pesan
6. Pilih kelas yang akan menerima notifikasi
7. Aktifkan notifikasi

### 6. Setup Cron Job untuk WA Queue (Production)
```bash
# Edit crontab
crontab -e

# Tambahkan baris berikut untuk proses queue setiap 5 menit
*/5 * * * * curl http://yourdomain.com/api/wa/process_queue

# Atau dengan wget
*/5 * * * * wget -q -O /dev/null http://yourdomain.com/api/wa/process_queue
```

## Troubleshooting

### Error 404 - Page Not Found
- Pastikan mod_rewrite Apache sudah aktif
- Check file `.htaccess` ada di root folder
- Untuk XAMPP, pastikan `httpd.conf` sudah enable mod_rewrite

### Database Connection Error
- Check kredensial database di `application/config/database.php`
- Pastikan MySQL service sudah running
- Test koneksi database dengan phpMyAdmin

### Upload File Error
- Check permission folder `assets/uploads`
- Untuk Windows: Properties → Security → Full Control
- Untuk Linux: `chmod 777 assets/uploads`

### CSRF Token Mismatch
- Jika terjadi saat submit form, refresh halaman
- Check `application/config/config.php` → `$config['csrf_protection']`
- Clear browser cache dan cookies

### WhatsApp Tidak Terkirim
- Check koneksi internet
- Pastikan API Key valid
- Check saldo/quota API WhatsApp
- Lihat error message di tabel `wa_queue`

## Hardware Setup - RFID Reader

### Recommended Hardware
- RFID Reader USB HID (Human Interface Device)
- Compatible dengan kartu MIFARE/EM4100
- Output: UID + Enter key

### Setup RFID Reader
1. Hubungkan RFID reader via USB
2. Reader akan terdeteksi sebagai keyboard
3. Test dengan Notepad - scan kartu, UID akan muncul
4. Buka halaman RFID: http://localhost/absensi-rfid/rfid
5. Input akan auto-focus, tinggal tap kartu

### Tips
- Pastikan komputer tidak sleep/hibernate
- Gunakan fullscreen mode di browser
- Test beberapa kali untuk memastikan UID terbaca konsisten

## Security Checklist

### Before Going to Production:
1. ✓ Ganti password admin default
2. ✓ Set environment ke 'production' di `index.php`
3. ✓ Disable error display di PHP
4. ✓ Set proper file permissions (755 untuk folders, 644 untuk files)
5. ✓ Enable HTTPS/SSL
6. ✓ Backup database secara berkala
7. ✓ Update CodeIgniter ke versi terbaru
8. ✓ Change encryption key di `application/config/config.php`

## Maintenance

### Regular Tasks:
- Backup database setiap hari
- Monitor log files di `application/logs`
- Clear cache di `application/cache` jika perlu
- Update data hari libur nasional
- Check WA queue status
- Monitor storage untuk uploads folder

### Monthly Tasks:
- Update software dependencies
- Review security logs
- Clean old log files
- Optimize database

## Support & Documentation

- **Manual Lengkap**: Lihat file README.md
- **Database Schema**: sql/database.sql
- **Issues**: https://github.com/yduwima/absensi-rfid/issues

## Selamat Menggunakan!

Sistem Absensi RFID siap digunakan. Untuk pertanyaan lebih lanjut, silakan hubungi developer atau buat issue di GitHub.
