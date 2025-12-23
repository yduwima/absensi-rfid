# 🚀 QUICK START GUIDE

Panduan cepat untuk menjalankan dan testing sistem dalam 10 menit!

## ⚡ Super Quick Setup (Minimum Steps)

### 1. Prerequisites Check
```bash
# Check PHP version (need 7.4+)
php -v

# Check MySQL is running
mysql --version

# Check Apache/Nginx is running
```

### 2. Download CodeIgniter System Folder
```bash
cd absensi-rfid

# Download CI 3.1.13
wget https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip

# Extract
unzip 3.1.13.zip

# Copy system folder
cp -r CodeIgniter-3.1.13/system .

# Cleanup
rm -rf CodeIgniter-3.1.13 3.1.13.zip
```

**Windows**: Download dari https://github.com/bcit-ci/CodeIgniter/archive/3.1.13.zip dan extract folder `system` ke root project.

### 3. Setup Database (One Command)
```bash
# Create database and import
mysql -u root -p -e "CREATE DATABASE absensi_rfid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p absensi_rfid < sql/database.sql
mysql -u root -p absensi_rfid < sql/sample_data.sql
```

**Via phpMyAdmin**:
1. Create database: `absensi_rfid`
2. Import: `sql/database.sql`
3. Import: `sql/sample_data.sql`

### 4. Configure Base URL
Edit `application/config/config.php` line 26:
```php
$config['base_url'] = 'http://localhost/absensi-rfid/';
```

### 5. Done! Access the App
- **Admin Panel**: http://localhost/absensi-rfid/login
- **RFID Page**: http://localhost/absensi-rfid/rfid

---

## 🔐 Default Login Credentials

### Admin Account
```
Email: admin@sekolah.com
Password: password
```

### Sample Guru Accounts (from sample_data.sql)
```
Email: budi.santoso@sekolah.com
Password: password

Email: siti.nurhasanah@sekolah.com
Password: password
```

---

## 🎯 Testing Checklist

### Test 1: Login System ✅
1. Go to: http://localhost/absensi-rfid/login
2. Enter: admin@sekolah.com / password
3. Should redirect to Admin Dashboard
4. Check statistics are displayed

### Test 2: RFID Scanning ✅
1. Go to: http://localhost/absensi-rfid/rfid
2. Click on RFID input field
3. Type: `RFID001` and press Enter
4. Should show: Andi Pratama, X-IPA 1, Status Masuk
5. Type again: `RFID001` and press Enter
6. Should show: Status Pulang

### Test 3: View Data ✅
1. Login as admin
2. Check dashboard statistics
3. All numbers should be > 0 (from sample data)

### Test 4: Database Check ✅
```sql
-- Check if sample data loaded
SELECT COUNT(*) FROM siswa;  -- Should return 10
SELECT COUNT(*) FROM guru;   -- Should return 6 (5 + admin)
SELECT COUNT(*) FROM kelas;  -- Should return 6
SELECT COUNT(*) FROM absensi_harian;  -- Should have today's data
```

---

## 🐛 Troubleshooting

### Error 404 - Page Not Found
```bash
# Check .htaccess exists
ls -la .htaccess

# Check mod_rewrite enabled (Apache)
sudo a2enmod rewrite
sudo service apache2 restart
```

### Error: Database Connection Failed
Edit `application/config/database.php`:
```php
'hostname' => 'localhost',
'username' => 'root',        // Your MySQL username
'password' => '',            // Your MySQL password
'database' => 'absensi_rfid',
```

### Blank Page / White Screen
```bash
# Check PHP errors
# Edit index.php line 16-19, change to:
error_reporting(-1);
ini_set('display_errors', 1);
```

### CSRF Token Mismatch
```bash
# Clear browser cache and cookies
# Or refresh the page (F5)
```

### Upload Folder Permission Error
```bash
chmod -R 777 assets/uploads
```

---

## 📝 Quick Testing Scenarios

### Scenario 1: Student Attendance Flow
1. Open RFID page
2. Scan card: `RFID002` → Budi Setiawan masuk
3. Wait 5 seconds
4. Scan same card: `RFID002` → Budi Setiawan pulang
5. Check today's attendance list updated

### Scenario 2: Teacher Attendance
1. Scan: `1234567890` → Drs. Budi Santoso
2. Should calculate if late based on jam_kerja

### Scenario 3: Invalid RFID
1. Scan: `INVALID123`
2. Should show error: "RFID tidak terdaftar"

### Scenario 4: Multiple Students
1. Scan 5 different student RFIDs
2. All should appear in attendance list
3. Refresh page, data should persist

---

## 🔍 Verify Installation

### Check Files Exist
```bash
# Essential files check
ls -la index.php                    # ✓ Should exist
ls -la .htaccess                    # ✓ Should exist
ls -la application/config/config.php # ✓ Should exist
ls -la sql/database.sql             # ✓ Should exist
ls -la system/core/CodeIgniter.php  # ✓ Should exist (after CI install)
```

### Check Database Tables
```sql
USE absensi_rfid;
SHOW TABLES;
-- Should show 18 tables
```

### Check Sample Data
```sql
-- Check students
SELECT nama, nis, rfid_uid FROM siswa LIMIT 5;

-- Check teachers
SELECT nama, email, role FROM guru;

-- Check classes
SELECT nama_kelas, tingkat FROM kelas;
```

---

## 🎨 UI Preview

After successful setup, you should see:

### Login Page
- Clean, modern design
- Blue gradient background
- School icon
- Email and password fields

### Admin Dashboard
- Sidebar with menu
- 4 statistic cards (blue, green, purple, orange)
- Quick links cards
- Info panel

### RFID Page
- School header with logo
- Real-time clock
- RFID input (auto-focused)
- Attendance history table
- Scan result card (appears after scan)

---

## 🎯 Next Steps After Testing

### For End Users:
1. Change admin password
2. Upload school logo
3. Configure school settings
4. Add real student/teacher data
5. Setup WhatsApp API
6. Configure RFID reader hardware

### For Developers:
1. Read DEVELOPMENT_GUIDE.md
2. Check STATUS.md for TODO items
3. Start building CRUD interfaces
4. Add Excel import/export
5. Create PDF reports
6. Build remaining features

---

## 📊 Expected Results

After successful setup:
- ✅ Can login with admin account
- ✅ Dashboard shows statistics
- ✅ RFID page loads without errors
- ✅ Can scan RFID and get response
- ✅ Attendance data saved to database
- ✅ No PHP errors in console
- ✅ All sample data loaded

---

## 💡 Tips for Demo/Presentation

### Prepare Demo Data
```sql
-- Add more sample attendance for today
INSERT INTO absensi_harian (user_type, user_id, tanggal, jam_masuk, status_masuk) 
VALUES 
('siswa', 4, CURDATE(), '06:58:00', 'Tepat Waktu'),
('siswa', 5, CURDATE(), '07:15:00', 'Terlambat'),
('siswa', 6, CURDATE(), '07:00:00', 'Tepat Waktu');
```

### Demo Flow
1. Show login system
2. Show admin dashboard with live stats
3. Open RFID page in new tab
4. Do live RFID scanning (type RFID manually)
5. Show immediate response
6. Show attendance list updates
7. Back to admin dashboard
8. Show stats updated

### Points to Highlight
- ✨ No page reload needed (AJAX)
- ⚡ Instant response (< 1 second)
- 🔒 Secure (password hashed, CSRF protection)
- 📱 Responsive (works on mobile)
- 🎨 Modern UI (Tailwind CSS)
- 📊 Real-time statistics
- 🔔 WhatsApp notification ready

---

## 📞 Need Help?

1. **Check Documentation**: README.md, INSTALL.md, etc.
2. **Check Logs**: `application/logs/`
3. **Enable Debug**: Set ENVIRONMENT to 'development'
4. **Browser Console**: Check for JavaScript errors
5. **Database Log**: Check MySQL error log
6. **GitHub Issues**: Create issue if needed

---

## ✅ Success Indicators

You know setup is successful when:
- [x] No errors on login page
- [x] Can login successfully
- [x] Dashboard loads completely
- [x] RFID page loads without errors
- [x] Can scan RFID and see result
- [x] Data appears in database
- [x] Logout works

---

**Ready to Go!** 🎉

Jika semua test di atas berhasil, sistem sudah siap digunakan untuk development atau demo!
