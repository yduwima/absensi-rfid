<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Admin Panel' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="w-64 bg-gradient-to-b from-blue-800 to-blue-900 text-white flex-shrink-0 hidden md:block">
            <div class="p-4 border-b border-blue-700">
                <div class="flex items-center">
                    <i class="fas fa-id-card text-3xl mr-3"></i>
                    <div>
                        <h1 class="text-xl font-bold">RFID Absensi</h1>
                        <p class="text-xs text-blue-300">Admin Panel</p>
                    </div>
                </div>
            </div>

            <nav class="mt-4 overflow-y-auto" style="max-height: calc(100vh - 100px);">
                <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition <?= $this->uri->segment(2) == 'dashboard' ? 'bg-blue-700' : '' ?>">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>

                <div class="px-4 py-2 text-xs text-blue-300 font-semibold uppercase">Pengaturan</div>
                
                <a href="<?= base_url('admin/pengaturan/sekolah') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-school w-6"></i>
                    <span>Pengaturan Sekolah</span>
                </a>
                
                <a href="<?= base_url('admin/pengaturan/jam_kerja') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-clock w-6"></i>
                    <span>Jam Kerja & Libur</span>
                </a>

                <div class="px-4 py-2 text-xs text-blue-300 font-semibold uppercase">Data Master</div>
                
                <a href="<?= base_url('admin/master/tahun_ajaran') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-calendar-alt w-6"></i>
                    <span>Tahun Ajaran</span>
                </a>
                
                <a href="<?= base_url('admin/master/semester') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-calendar-check w-6"></i>
                    <span>Semester</span>
                </a>
                
                <a href="<?= base_url('admin/master/kelas') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-door-open w-6"></i>
                    <span>Kelas</span>
                </a>
                
                <a href="<?= base_url('admin/master/naik_kelas') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-level-up-alt w-6"></i>
                    <span>Naik Kelas</span>
                </a>
                
                <a href="<?= base_url('admin/master/siswa') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-user-graduate w-6"></i>
                    <span>Data Siswa</span>
                </a>
                
                <a href="<?= base_url('admin/master/guru') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-chalkboard-teacher w-6"></i>
                    <span>Data Guru</span>
                </a>

                <a href="<?= base_url('admin/master/mapel') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-book w-6"></i>
                    <span>Mata Pelajaran</span>
                </a>

                <a href="<?= base_url('admin/master/jadwal') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-calendar w-6"></i>
                    <span>Jadwal Pelajaran</span>
                </a>

                <div class="px-4 py-2 text-xs text-blue-300 font-semibold uppercase">WhatsApp</div>
                
                <a href="<?= base_url('admin/wa/config') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fab fa-whatsapp w-6"></i>
                    <span>Pengaturan WA</span>
                </a>

                <div class="px-4 py-2 text-xs text-blue-300 font-semibold uppercase">Laporan</div>
                
                <a href="<?= base_url('admin/laporan/absensi_siswa') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-file-alt w-6"></i>
                    <span>Laporan Siswa</span>
                </a>
                
                <a href="<?= base_url('admin/laporan/absensi_guru') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-file-invoice w-6"></i>
                    <span>Laporan Guru</span>
                </a>

                <a href="<?= base_url('admin/laporan/rekap_siswa') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span>Rekap Siswa</span>
                </a>

                <a href="<?= base_url('admin/laporan/rekap_guru') ?>" class="flex items-center px-4 py-3 hover:bg-blue-700 transition">
                    <i class="fas fa-chart-line w-6"></i>
                    <span>Rekap Guru</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-md">
                <div class="flex items-center justify-between px-6 py-4">
                    <button id="sidebarToggle" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>

                    <div class="flex items-center space-x-4">
                        <a href="<?= base_url('rfid') ?>" target="_blank" class="text-blue-600 hover:text-blue-800" title="Buka Halaman RFID">
                            <i class="fas fa-id-card text-xl"></i>
                        </a>
                        
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                                <?php if ($user['foto']): ?>
                                <img src="<?= base_url('assets/uploads/guru/' . $user['foto']) ?>" alt="Foto" class="w-10 h-10 rounded-full border-2 border-blue-500">
                                <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    <?= substr($user['nama'], 0, 1) ?>
                                </div>
                                <?php endif; ?>
                                <span class="font-semibold"><?= $user['nama'] ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block">
                                <div class="px-4 py-2 text-sm text-gray-700 border-b">
                                    <div class="font-semibold"><?= $user['nama'] ?></div>
                                    <div class="text-xs text-gray-500"><?= $user['role'] ?></div>
                                </div>
                                <a href="<?= base_url('admin/profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="<?= base_url('logout') ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
