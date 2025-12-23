<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Pengaturan WhatsApp Notifikasi</h2>

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

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px">
                <li class="mr-2">
                    <a href="#config" class="tab-link active inline-block p-4 border-b-2 border-blue-600 rounded-t-lg">
                        Konfigurasi API
                    </a>
                </li>
                <li class="mr-2">
                    <a href="#template" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                        Template Pesan
                    </a>
                </li>
                <li class="mr-2">
                    <a href="#kelas" class="tab-link inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                        Kelas Penerima
                    </a>
                </li>
            </ul>
        </div>

        <!-- Config Tab -->
        <div id="config" class="tab-content">
            <form method="POST" action="<?= base_url('admin/wa_notifikasi/update_config') ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">API URL</label>
                        <input type="text" name="api_url" value="<?= $wa_config->api_url ?? '' ?>" 
                               class="w-full px-3 py-2 border rounded-lg" placeholder="https://api.fonnte.com/send">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">API Key</label>
                        <input type="text" name="api_key" value="<?= $wa_config->api_key ?? '' ?>" 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Sender Number</label>
                        <input type="text" name="sender_number" value="<?= $wa_config->sender_number ?? '' ?>" 
                               class="w-full px-3 py-2 border rounded-lg" placeholder="628123456789">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Link URL (Optional)</label>
                        <input type="text" name="link_url" value="<?= $wa_config->link_url ?? '' ?>" 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" 
                               <?= (isset($wa_config->is_active) && $wa_config->is_active) ? 'checked' : '' ?> 
                               class="mr-2">
                        <span class="text-sm font-medium">Aktifkan Notifikasi WhatsApp</span>
                    </label>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Simpan Konfigurasi
                </button>
            </form>
        </div>

        <!-- Template Tab -->
        <div id="template" class="tab-content hidden">
            <form method="POST" action="<?= base_url('admin/wa_notifikasi/update_config') ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Template Pesan Masuk</label>
                    <textarea name="template_masuk" rows="6" class="w-full px-3 py-2 border rounded-lg"><?= $wa_config->template_masuk ?? '' ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Placeholder: {nama_siswa}, {kelas}, {jam_masuk}, {tanggal}, {keterangan_terlambat}, {nama_sekolah}, {nama_ortu}
                    </p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Template Pesan Pulang</label>
                    <textarea name="template_pulang" rows="6" class="w-full px-3 py-2 border rounded-lg"><?= $wa_config->template_pulang ?? '' ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        Placeholder: {nama_siswa}, {kelas}, {jam_pulang}, {tanggal}, {nama_sekolah}, {nama_ortu}
                    </p>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Simpan Template
                </button>
            </form>
        </div>

        <!-- Kelas Tab -->
        <div id="kelas" class="tab-content hidden">
            <form method="POST" action="<?= base_url('admin/wa_notifikasi/update_kelas_aktif') ?>">
                <p class="mb-4 text-sm text-gray-600">Pilih kelas yang akan menerima notifikasi WhatsApp:</p>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <?php foreach($kelas_list as $kelas): ?>
                    <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <input type="checkbox" name="kelas_ids[]" value="<?= $kelas->id ?>" 
                               <?= in_array($kelas->id, array_column($selected_kelas, 'kelas_id')) ? 'checked' : '' ?>
                               class="mr-2">
                        <span><?= $kelas->nama_kelas ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Simpan Pilihan Kelas
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tab-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all links and hide all content
        document.querySelectorAll('.tab-link').forEach(l => {
            l.classList.remove('active', 'border-blue-600');
            l.classList.add('border-transparent');
        });
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        
        // Add active class to clicked link and show content
        this.classList.add('active', 'border-blue-600');
        this.classList.remove('border-transparent');
        const targetId = this.getAttribute('href').substring(1);
        document.getElementById(targetId).classList.remove('hidden');
    });
});
</script>
