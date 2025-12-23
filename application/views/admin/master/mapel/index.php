<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data Mata Pelajaran</h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Mata Pelajaran
            </button>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <div class="mb-4 flex gap-4">
            <input type="text" id="searchInput" placeholder="Cari mata pelajaran..." class="flex-1 px-4 py-2 border rounded-lg" value="<?= $this->input->get('search') ?>">
            <select id="perPageSelect" class="px-4 py-2 border rounded-lg">
                <option value="10">10</option><option value="20">20</option><option value="50">50</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr><th class="px-4 py-2 text-left">No</th><th class="px-4 py-2 text-left">Kode Mapel</th><th class="px-4 py-2 text-left">Nama Mata Pelajaran</th><th class="px-4 py-2 text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($mapel)): ?>
                        <tr><td colspan="4" class="text-center py-4">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php foreach ($mapel as $index => $m): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= $index + 1 ?></td>
                                <td class="px-4 py-2 font-semibold text-blue-600"><?= $m->kode_mapel ?></td>
                                <td class="px-4 py-2"><?= $m->nama_mapel ?></td>
                                <td class="px-4 py-2 text-center">
                                    <button onclick="editData(<?= $m->id ?>)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteData(<?= $m->id ?>)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">Total: <?= $total ?> data</div>
    </div>
</div>

<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Mata Pelajaran</h3>
        <form id="dataForm">
            <input type="hidden" id="dataId">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Kode Mapel</label>
                <input type="text" name="kode_mapel" id="kode_mapel" class="w-full px-4 py-2 border rounded-lg" required placeholder="Contoh: MTK">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" id="nama_mapel" class="w-full px-4 py-2 border rounded-lg" required placeholder="Contoh: Matematika">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('formModal').classList.remove('hidden'); document.getElementById('formModal').classList.add('flex'); document.getElementById('dataForm').reset(); }
function closeModal() { document.getElementById('formModal').classList.add('hidden'); }
function editData(id) { fetch(`<?= base_url('admin/master/mapel/get/') ?>${id}`).then(r => r.json()).then(d => { document.getElementById('dataId').value = d.id; document.getElementById('kode_mapel').value = d.kode_mapel; document.getElementById('nama_mapel').value = d.nama_mapel; openModal(); }); }
function deleteData(id) { Swal.fire({ title: 'Konfirmasi', text: 'Yakin hapus?', icon: 'warning', showCancelButton: true }).then((result) => { if (result.isConfirmed) { fetch(`<?= base_url('admin/master/mapel/delete/') ?>${id}`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Data dihapus', 'success').then(() => location.reload()); }); } }); }
document.getElementById('dataForm').addEventListener('submit', function(e) { e.preventDefault(); const id = document.getElementById('dataId').value; const url = id ? `<?= base_url('admin/master/mapel/edit/') ?>${id}` : '<?= base_url('admin/master/mapel/create') ?>';  fetch(url, { method: 'POST', body: new FormData(this) }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Data disimpan', 'success').then(() => location.reload()); else Swal.fire('Error!', d.message, 'error'); }); });
</script>
