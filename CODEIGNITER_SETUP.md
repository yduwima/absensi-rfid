# PENTING: CodeIgniter System Folder

## ⚠️ LANGKAH WAJIB SEBELUM MENGGUNAKAN SISTEM INI

Sistem ini membutuhkan **CodeIgniter 3 system folder** yang tidak di-include dalam repository karena alasan ukuran dan lisensi.

### Cara Download dan Install CodeIgniter System Folder:

#### Option 1: Download Manual (Recommended)

1. **Download CodeIgniter 3**
   - Kunjungi: https://codeigniter.com/download
   - Atau direct link: https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip
   - Download versi **3.1.13** atau yang terbaru dari branch 3.x

2. **Extract dan Copy**
   ```bash
   # Extract file yang sudah di-download
   unzip CodeIgniter-3.1.13.zip
   
   # Copy folder 'system' ke root project
   cp -r CodeIgniter-3.1.13/system /path/to/absensi-rfid/
   ```

3. **Struktur Folder Akhir**
   ```
   absensi-rfid/
   ├── application/
   ├── assets/
   ├── sql/
   ├── system/          ← Folder dari CodeIgniter (WAJIB ADA!)
   │   ├── core/
   │   ├── database/
   │   ├── fonts/
   │   ├── helpers/
   │   ├── language/
   │   └── libraries/
   ├── .htaccess
   ├── index.php
   └── README.md
   ```

#### Option 2: Clone dari GitHub

```bash
# Clone CodeIgniter 3 repository
git clone https://github.com/bcit-ci/CodeIgniter.git temp-ci
cd temp-ci
git checkout 3.1.13

# Copy system folder
cp -r system ../absensi-rfid/

# Hapus temporary folder
cd ..
rm -rf temp-ci
```

#### Option 3: Menggunakan Composer

```bash
cd absensi-rfid
composer create-project codeigniter/framework:^3 temp-ci
cp -r temp-ci/system .
rm -rf temp-ci
```

### Verifikasi Instalasi

Setelah folder `system` ditambahkan:

1. **Check folder structure:**
   ```bash
   ls -la system/
   # Harus ada: core, database, fonts, helpers, language, libraries
   ```

2. **Test akses aplikasi:**
   - Buka browser: `http://localhost/absensi-rfid/`
   - Seharusnya redirect ke login page
   - Tidak ada error "system folder not found"

### Troubleshooting

#### Error: "Your system folder path does not appear to be set correctly"

**Solusi:**
1. Pastikan folder `system` ada di root project
2. Check permission folder (chmod 755)
3. Check path di `index.php` (line 81):
   ```php
   $system_path = 'system';
   ```

#### Error: "Class 'CI_Controller' not found"

**Solusi:**
1. Folder system belum di-copy
2. Download CodeIgniter dan copy folder system
3. Restart web server (Apache/Nginx)

#### Error 403 Forbidden

**Solusi:**
```bash
# Linux/Mac
chmod -R 755 system

# Check .htaccess ada di root
ls -la .htaccess
```

### File Size Information

- **CodeIgniter 3 system folder**: ~2.5 MB
- **Total project dengan system**: ~3 MB
- **Alasan tidak di-include**: Mengurangi ukuran repository dan menghindari duplikasi

### License Information

CodeIgniter 3 menggunakan **MIT License** yang membolehkan:
- ✅ Penggunaan komersial
- ✅ Modifikasi
- ✅ Distribusi
- ✅ Penggunaan pribadi

## Quick Installation Script

Untuk memudahkan, gunakan script ini:

### Linux/Mac:
```bash
#!/bin/bash
# download-ci.sh

cd /path/to/absensi-rfid
wget https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip
unzip 3.1.13.zip
cp -r CodeIgniter-3.1.13/system .
rm -rf CodeIgniter-3.1.13 3.1.13.zip
echo "CodeIgniter system folder installed successfully!"
```

### Windows (PowerShell):
```powershell
# download-ci.ps1

$url = "https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip"
$output = "ci.zip"

Invoke-WebRequest -Uri $url -OutFile $output
Expand-Archive -Path $output -DestinationPath .
Copy-Item -Path "CodeIgniter-3.1.13\system" -Destination "." -Recurse
Remove-Item -Path "CodeIgniter-3.1.13" -Recurse
Remove-Item -Path $output
Write-Host "CodeIgniter system folder installed successfully!"
```

## Setelah System Folder Terinstall

Lanjutkan dengan langkah instalasi di **INSTALL.md**:
1. Setup database
2. Konfigurasi base_url
3. Set permissions
4. Login dan mulai menggunakan sistem

## Support

Jika mengalami kesulitan:
1. Check dokumentasi CodeIgniter: https://codeigniter.com/userguide3
2. Check file INSTALL.md untuk panduan lengkap
3. Buat issue di GitHub repository

---

**Note**: File ini penting dibaca sebelum melakukan instalasi pertama kali.
