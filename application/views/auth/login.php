<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Login' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-lg shadow-2xl p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-id-card text-6xl text-blue-600 mb-4"></i>
                    <h1 class="text-3xl font-bold text-gray-800">Sistem Absensi RFID</h1>
                    <p class="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
                </div>

                <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?= $this->session->flashdata('error') ?></span>
                </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <span class="block sm:inline"><?= $this->session->flashdata('success') ?></span>
                </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/do_login') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan email Anda">
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan password Anda">
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="<?= base_url('rfid') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Halaman Absensi RFID
                    </a>
                </div>
            </div>

            <div class="text-center mt-4 text-white text-sm">
                <p>&copy; <?= date('Y') ?> Sistem Absensi RFID. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
