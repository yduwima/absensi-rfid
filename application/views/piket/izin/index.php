<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Input Izin Siswa</h2>
            <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-2"></i>Input Izin</button>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr><th class="px-4 py-2">Tanggal</th><th class="px-4 py-2">Nama Siswa</th><th class="px-4 py-2">Kelas</th><th class="px-4 py-2">Jenis</th><th class="px-4 py-2">Waktu</th><th class="px-4 py-2">Alasan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($izin as $i): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2"><?= date('d/m/Y', strtotime($i->tanggal)) ?></td>
                            <td class="px-4 py-2"><?= $i->nama_siswa ?></td>
                            <td class="px-4 py-2"><?= $i->nama_kelas ?></td>
                            <td class="px-4 py-2"><span class="px-2 py-1 rounded text-xs <?= $i->jenis_izin == 'Masuk' ? 'bg-blue-100' : 'bg-orange-100' ?>"><?= $i->jenis_izin ?></span></td>
                            <td class="px-4 py-2"><?= $i->waktu ?></td>
                            <td class="px-4 py-2"><?= $i->alasan ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Input Izin Siswa</h3>
        <form id="izinForm">
            <div class="mb-4">
                <label class="block mb-2">Siswa</label>
                <select name="siswa_id" class="w-full px-4 py-2 border rounded" required>
                    <option value="">Pilih Siswa</option>
                    <?php foreach ($siswa as $s): ?>
                        <option value="<?= $s->id ?>"><?= $s->nama ?> - <?= $s->nama_kelas ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Jenis Izin</label>
                <select name="jenis_izin" class="w-full px-4 py-2 border rounded" required>
                    <option value="">Pilih</option>
                    <option value="Masuk">Izin Masuk (Terlambat)</option>
                    <option value="Keluar">Izin Keluar (Pulang Awal)</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Tanggal</label>
                <input type="date" name="tanggal" class="w-full px-4 py-2 border rounded" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Waktu</label>
                <input type="time" name="waktu" class="w-full px-4 py-2 border rounded" value="<?= date('H:i') ?>" required>
            </div>
            <div class="mb-4">
                <label class="block mb-2">Alasan</label>
                <textarea name="alasan" class="w-full px-4 py-2 border rounded" rows="3" required></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('formModal').classList.remove('hidden'); document.getElementById('formModal').classList.add('flex'); }
function closeModal() { document.getElementById('formModal').classList.add('hidden'); }
document.getElementById('izinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?= base_url('piket/izin/create') ?>', { method: 'POST', body: new FormData(this) })
        .then(r => r.json())
        .then(d => { if (d.status === 'success') { Swal.fire('Berhasil!', 'Izin dicatat', 'success').then(() => location.reload()); } else { Swal.fire('Error!', d.message, 'error'); } });
});
</script>
