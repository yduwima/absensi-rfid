<div class="mb-6 flex justify-between items-center">
    <div>
        <form method="get" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari kelas..." value="<?= $this->input->get('search') ?>" 
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>
    <button onclick="openAddModal()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Tambah Kelas
    </button>
</div>

<?php if ($this->session->flashdata('success')): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?= $this->session->flashdata('success') ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tingkat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wali Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun Ajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($kelas)): ?>
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($kelas as $k): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $no++ ?></td>
                    <td class="px-6 py-4 font-medium"><?= $k->nama_kelas ?></td>
                    <td class="px-6 py-4"><?= $k->tingkat ?></td>
                    <td class="px-6 py-4"><?= $k->nama_wali_kelas ?? '-' ?></td>
                    <td class="px-6 py-4"><?= $k->tahun_ajaran ?? '-' ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="editKelas(<?= $k->id ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="<?= base_url('admin/master/kelas/delete/' . $k->id) ?>" 
                           onclick="return confirm('Yakin ingin menghapus?')" 
                           class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="kelasModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Tambah Kelas</h3>
            <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="kelasForm">
            <input type="hidden" id="kelas_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" id="nama_kelas" required 
                           placeholder="Contoh: X IPA 1" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Tingkat *</label>
                    <select name="tingkat" id="tingkat" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih Tingkat -</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Wali Kelas</label>
                    <select name="wali_kelas_id" id="wali_kelas_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih Wali Kelas -</option>
                        <?php foreach ($guru_list as $g): ?>
                        <option value="<?= $g->id ?>"><?= $g->nama ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Tahun Ajaran *</label>
                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih Tahun Ajaran -</option>
                        <?php foreach ($tahun_ajaran_list as $ta): ?>
                        <option value="<?= $ta->id ?>"><?= $ta->tahun_ajaran ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('kelasModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Kelas';
    document.getElementById('kelasForm').reset();
    document.getElementById('kelas_id').value = '';
}

function closeModal() {
    document.getElementById('kelasModal').classList.add('hidden');
}

function editKelas(id) {
    fetch('<?= base_url('admin/master/kelas/get_by_id/') ?>' + id)
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                const data = result.data;
                document.getElementById('kelas_id').value = data.id;
                document.getElementById('nama_kelas').value = data.nama_kelas;
                document.getElementById('tingkat').value = data.tingkat;
                document.getElementById('wali_kelas_id').value = data.wali_kelas_id || '';
                document.getElementById('tahun_ajaran_id').value = data.tahun_ajaran_id;
                
                document.getElementById('modalTitle').textContent = 'Edit Kelas';
                document.getElementById('kelasModal').classList.remove('hidden');
            }
        });
}

document.getElementById('kelasForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const kelasId = document.getElementById('kelas_id').value;
    const url = kelasId ? 
        '<?= base_url('admin/master/kelas/update/') ?>' + kelasId : 
        '<?= base_url('admin/master/kelas/store') ?>';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            Swal.fire('Berhasil!', result.message, 'success').then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    });
});
</script>
