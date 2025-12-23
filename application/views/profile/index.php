<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Profile Saya</h2>

        <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>

        <?php if($this->session->flashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $this->session->flashdata('error') ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Photo -->
            <div class="text-center">
                <img src="<?= base_url('assets/uploads/guru/' . ($user->foto ?: 'default-avatar.png')) ?>" 
                     alt="Profile Photo" class="w-48 h-48 rounded-full mx-auto mb-4 object-cover border-4 border-gray-200">
                <h3 class="text-xl font-bold"><?= $user->nama ?></h3>
                <p class="text-gray-600"><?= $user->role ?></p>
                <p class="text-sm text-gray-500 mt-2">NIP: <?= $user->nip ?></p>
            </div>

            <!-- Profile Form -->
            <div class="md:col-span-2">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-4 pb-2 border-b">Informasi Pribadi</h4>
                    <form method="POST" action="<?= base_url('profile/update') ?>" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= $user->nama ?>" 
                                       class="w-full px-3 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Email</label>
                                <input type="email" name="email" value="<?= $user->email ?>" 
                                       class="w-full px-3 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">No. HP</label>
                                <input type="text" name="no_hp" value="<?= $user->no_hp ?>" 
                                       class="w-full px-3 py-2 border rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Foto Profile</label>
                                <input type="file" name="foto" accept="image/*" 
                                       class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Alamat</label>
                            <textarea name="alamat" rows="3" class="w-full px-3 py-2 border rounded-lg"><?= $user->alamat ?></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4 pb-2 border-b">Ubah Password</h4>
                    <form method="POST" action="<?= base_url('profile/change_password') ?>">
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Password Lama</label>
                                <input type="password" name="old_password" 
                                       class="w-full px-3 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Password Baru</label>
                                <input type="password" name="new_password" 
                                       class="w-full px-3 py-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" 
                                       class="w-full px-3 py-2 border rounded-lg" required>
                            </div>
                        </div>
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700">
                            Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
