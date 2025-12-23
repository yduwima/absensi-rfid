<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data Semester</h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Semester
            </button>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 flex gap-4">
            <input type="text" id="searchInput" placeholder="Cari semester..." 
                   class="flex-1 px-4 py-2 border rounded-lg" value="<?= $this->input->get('search') ?>">
            <select id="perPageSelect" class="px-4 py-2 border rounded-lg">
                <option value="10" <?= $this->input->get('per_page') == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $this->input->get('per_page') == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $this->input->get('per_page') == 50 ? 'selected' : '' ?>>50</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Tahun Ajaran</th>
                        <th class="px-4 py-2 text-left">Semester</th>
                        <th class="px-4 py-2 text-left">Periode</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($semester)): ?>
                        <tr><td colspan="6" class="text-center py-4">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php foreach ($semester as $index => $s): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= ($this->input->get('page') ?: 0) + $index + 1 ?></td>
                                <td class="px-4 py-2"><?= $s->nama_tahun_ajaran ?></td>
                                <td class="px-4 py-2"><?= $s->nama_semester ?></td>
                                <td class="px-4 py-2"><?= date('d/m/Y', strtotime($s->tanggal_mulai)) ?> - <?= date('d/m/Y', strtotime($s->tanggal_selesai)) ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($s->is_active): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Aktif</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button onclick="editData(<?= $s->id ?>)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteData(<?= $s->id ?>)" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex justify-between items-center">
            <div>Total: <?= $total ?> data</div>
            <div><?= $pagination ?></div>
        </div>
    </div>
</div>

<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Semester</h3>
        <form id="dataForm">
            <input type="hidden" id="dataId" name="id">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php foreach ($tahun_ajaran as $ta): ?>
                        <option value="<?= $ta->id ?>"><?= $ta->nama_tahun_ajaran ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Semester</label>
                <select name="nama_semester" id="nama_semester" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">Pilih</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="flex items-center"><input type="checkbox" name="is_active" id="is_active" class="mr-2"><span>Aktif</span></label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('formModal').classList.remove('hidden'); document.getElementById('formModal').classList.add('flex'); document.getElementById('dataForm').reset(); document.getElementById('dataId').value = ''; }
function closeModal() { document.getElementById('formModal').classList.add('hidden'); }
function editData(id) { fetch(`<?= base_url('admin/master/semester/get/') ?>${id}`).then(r => r.json()).then(d => { document.getElementById('dataId').value = d.id; document.getElementById('tahun_ajaran_id').value = d.tahun_ajaran_id; document.getElementById('nama_semester').value = d.nama_semester; document.getElementById('tanggal_mulai').value = d.tanggal_mulai; document.getElementById('tanggal_selesai').value = d.tanggal_selesai; document.getElementById('is_active').checked = d.is_active == 1; openModal(); }); }
function deleteData(id) { Swal.fire({ title: 'Konfirmasi', text: 'Yakin hapus?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus!' }).then((result) => { if (result.isConfirmed) { fetch(`<?= base_url('admin/master/semester/delete/') ?>${id}`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Data dihapus', 'success').then(() => location.reload()); }); } }); }
document.getElementById('dataForm').addEventListener('submit', function(e) { e.preventDefault(); const id = document.getElementById('dataId').value; const url = id ? `<?= base_url('admin/master/semester/edit/') ?>${id}` : '<?= base_url('admin/master/semester/create') ?>'; fetch(url, { method: 'POST', body: new FormData(this) }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Data disimpan', 'success').then(() => location.reload()); else Swal.fire('Error!', d.message, 'error'); }); });
</script>
