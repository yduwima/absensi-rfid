<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Sistem Absensi RFID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-purple-800 text-white min-h-screen">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Portal BK</h1>
                <p class="text-sm text-purple-200"><?= $user['nama'] ?? 'BK' ?></p>
            </div>
            
            <nav class="mt-6">
                <a href="<?= base_url('bk/dashboard') ?>" class="flex items-center px-6 py-3 hover:bg-purple-700">
                    <i class="fas fa-home mr-3"></i>
                    Dashboard
                </a>
                <a href="<?= base_url('bk/monitoring') ?>" class="flex items-center px-6 py-3 hover:bg-purple-700">
                    <i class="fas fa-chart-line mr-3"></i>
                    Monitoring Siswa
                </a>
                <a href="<?= base_url('bk/surat') ?>" class="flex items-center px-6 py-3 hover:bg-purple-700">
                    <i class="fas fa-envelope mr-3"></i>
                    Surat Pemanggilan
                </a>
                <a href="<?= base_url('bk/profile') ?>" class="flex items-center px-6 py-3 hover:bg-purple-700">
                    <i class="fas fa-user mr-3"></i>
                    Profile
                </a>
                <a href="<?= base_url('auth/logout') ?>" class="flex items-center px-6 py-3 hover:bg-purple-700">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow-md p-6">
                <h2 class="text-2xl font-semibold text-gray-800"><?= $title ?? 'Dashboard' ?></h2>
            </header>

            <!-- Content -->
            <main class="p-6">
