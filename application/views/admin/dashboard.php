<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-600">Selamat datang di Sistem Absensi RFID</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Siswa -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm mb-1">Total Siswa</p>
                <h3 class="text-3xl font-bold"><?= number_format($total_siswa) ?></h3>
            </div>
            <div class="bg-blue-400 bg-opacity-30 rounded-full p-4">
                <i class="fas fa-user-graduate text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm mb-1">Total Guru</p>
                <h3 class="text-3xl font-bold"><?= number_format($total_guru) ?></h3>
            </div>
            <div class="bg-green-400 bg-opacity-30 rounded-full p-4">
                <i class="fas fa-chalkboard-teacher text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Absen Siswa Hari Ini -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm mb-1">Absen Siswa Hari Ini</p>
                <h3 class="text-3xl font-bold"><?= number_format($absen_siswa_hari_ini) ?></h3>
            </div>
            <div class="bg-purple-400 bg-opacity-30 rounded-full p-4">
                <i class="fas fa-clipboard-check text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Absen Guru Hari Ini -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-lg shadow-lg p-6 transform hover:scale-105 transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm mb-1">Absen Guru Hari Ini</p>
                <h3 class="text-3xl font-bold"><?= number_format($absen_guru_hari_ini) ?></h3>
            </div>
            <div class="bg-orange-400 bg-opacity-30 rounded-full p-4">
                <i class="fas fa-user-check text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-users text-blue-600 mr-2"></i>
            Data Master
        </h3>
        <div class="space-y-2">
            <a href="<?= base_url('admin/master/siswa') ?>" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Data Siswa
            </a>
            <a href="<?= base_url('admin/master/guru') ?>" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Data Guru
            </a>
            <a href="<?= base_url('admin/master/kelas') ?>" class="block text-gray-700 hover:text-blue-600 hover:bg-blue-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Data Kelas
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-file-alt text-green-600 mr-2"></i>
            Laporan
        </h3>
        <div class="space-y-2">
            <a href="<?= base_url('admin/laporan/absensi_siswa') ?>" class="block text-gray-700 hover:text-green-600 hover:bg-green-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Laporan Absensi Siswa
            </a>
            <a href="<?= base_url('admin/laporan/absensi_guru') ?>" class="block text-gray-700 hover:text-green-600 hover:bg-green-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Laporan Absensi Guru
            </a>
            <a href="<?= base_url('admin/laporan/rekap_siswa') ?>" class="block text-gray-700 hover:text-green-600 hover:bg-green-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Rekap Siswa
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-cog text-purple-600 mr-2"></i>
            Pengaturan
        </h3>
        <div class="space-y-2">
            <a href="<?= base_url('admin/pengaturan/sekolah') ?>" class="block text-gray-700 hover:text-purple-600 hover:bg-purple-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Pengaturan Sekolah
            </a>
            <a href="<?= base_url('admin/pengaturan/jam_kerja') ?>" class="block text-gray-700 hover:text-purple-600 hover:bg-purple-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Jam Kerja & Libur
            </a>
            <a href="<?= base_url('admin/wa/config') ?>" class="block text-gray-700 hover:text-purple-600 hover:bg-purple-50 px-3 py-2 rounded transition">
                <i class="fas fa-angle-right mr-2"></i>Pengaturan WhatsApp
            </a>
        </div>
    </div>
</div>

<!-- Info Panel -->
<div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
        <div>
            <h4 class="text-lg font-bold text-blue-800 mb-2">Informasi Sistem</h4>
            <ul class="text-blue-700 space-y-1">
                <li><i class="fas fa-check mr-2"></i>Sistem absensi menggunakan kartu RFID untuk masuk dan pulang</li>
                <li><i class="fas fa-check mr-2"></i>Notifikasi WhatsApp otomatis dikirim ke orang tua siswa</li>
                <li><i class="fas fa-check mr-2"></i>Absensi per mata pelajaran diisi oleh guru di jurnal mengajar</li>
                <li><i class="fas fa-check mr-2"></i>Monitoring BK otomatis mendeteksi siswa dengan pelanggaran</li>
            </ul>
        </div>
    </div>
</div>
