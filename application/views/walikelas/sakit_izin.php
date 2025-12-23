<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Input Sakit / Izin Siswa</h2>
        
        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <form id="sakitIzinForm" class="max-w-md">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Pilih Siswa</label>
                <select name="siswa_id" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s->id ?>"><?= $s->nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" class="w-full px-4 py-2 border rounded-lg" value="<?= $today ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Keterangan</label>
                <select name="keterangan" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">-- Pilih --</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Izin">Izin</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                <i class="fas fa-save mr-2"></i>Simpan
            </button>
        </form>

        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Keterangan:</h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>Input sakit/izin untuk siswa yang tidak hadir tanpa tap kartu RFID</li>
                <li>Data akan tercatat dalam absensi harian dengan jam masuk/pulang kosong</li>
                <li>Pilih tanggal yang sesuai dengan hari ketidakhadiran</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.getElementById('sakitIzinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?= base_url('walikelas/sakit_izin/submit') ?>', { method: 'POST', body: new FormData(this) })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                Swal.fire('Berhasil!', 'Data sakit/izin telah dicatat', 'success').then(() => this.reset());
            } else {
                Swal.fire('Error!', d.message, 'error');
            }
        });
});
</script>
