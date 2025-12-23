<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Data Tahun Ajaran</h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Tambah Tahun Ajaran
            </button>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Search and Filter -->
        <div class="mb-4 flex gap-4">
            <input type="text" id="searchInput" placeholder="Cari tahun ajaran..." 
                   class="flex-1 px-4 py-2 border rounded-lg" value="<?= $this->input->get('search') ?>">
            <select id="perPageSelect" class="px-4 py-2 border rounded-lg">
                <option value="10" <?= $this->input->get('per_page') == 10 ? 'selected' : '' ?>>10</option>
                <option value="20" <?= $this->input->get('per_page') == 20 ? 'selected' : '' ?>>20</option>
                <option value="50" <?= $this->input->get('per_page') == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $this->input->get('per_page') == 100 ? 'selected' : '' ?>>100</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No</th>
                        <th class="px-4 py-2 text-left">Nama Tahun Ajaran</th>
                        <th class="px-4 py-2 text-left">Tahun Mulai</th>
                        <th class="px-4 py-2 text-left">Tahun Selesai</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tahun_ajaran)): ?>
                        <tr><td colspan="6" class="text-center py-4">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php foreach ($tahun_ajaran as $index => $ta): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2"><?= ($this->input->get('page') ?: 0) + $index + 1 ?></td>
                                <td class="px-4 py-2"><?= $ta->nama_tahun_ajaran ?></td>
                                <td class="px-4 py-2"><?= $ta->tahun_mulai ?></td>
                                <td class="px-4 py-2"><?= $ta->tahun_selesai ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($ta->is_active): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Aktif</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button onclick="editData(<?= $ta->id ?>)" class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteData(<?= $ta->id ?>)" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center">
            <div>Total: <?= $total ?> data</div>
            <div><?= $pagination ?></div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Tahun Ajaran</h3>
        <form id="dataForm">
            <input type="hidden" id="dataId" name="id">
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nama Tahun Ajaran</label>
                <input type="text" name="nama_tahun_ajaran" id="nama_tahun_ajaran" 
                       class="w-full px-4 py-2 border rounded-lg" required
                       placeholder="Contoh: 2024/2025">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tahun Mulai</label>
                <input type="number" name="tahun_mulai" id="tahun_mulai" 
                       class="w-full px-4 py-2 border rounded-lg" required
                       placeholder="2024">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Tahun Selesai</label>
                <input type="number" name="tahun_selesai" id="tahun_selesai" 
                       class="w-full px-4 py-2 border rounded-lg" required
                       placeholder="2025">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" class="mr-2">
                    <span class="text-gray-700">Aktif</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('formModal').classList.remove('hidden');
    document.getElementById('formModal').classList.add('flex');
    document.getElementById('modalTitle').textContent = 'Tambah Tahun Ajaran';
    document.getElementById('dataForm').reset();
    document.getElementById('dataId').value = '';
}

function closeModal() {
    document.getElementById('formModal').classList.add('hidden');
    document.getElementById('formModal').classList.remove('flex');
}

function editData(id) {
    fetch(`<?= base_url('admin/master/tahun_ajaran/get/') ?>${id}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('dataId').value = data.id;
            document.getElementById('nama_tahun_ajaran').value = data.nama_tahun_ajaran;
            document.getElementById('tahun_mulai').value = data.tahun_mulai;
            document.getElementById('tahun_selesai').value = data.tahun_selesai;
            document.getElementById('is_active').checked = data.is_active == 1;
            document.getElementById('modalTitle').textContent = 'Edit Tahun Ajaran';
            openModal();
        });
}

function deleteData(id) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('admin/master/tahun_ajaran/delete/') ?>${id}`, { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Berhasil!', 'Data berhasil dihapus', 'success')
                            .then(() => location.reload());
                    }
                });
        }
    });
}

document.getElementById('dataForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('dataId').value;
    const url = id ? `<?= base_url('admin/master/tahun_ajaran/edit/') ?>${id}` : '<?= base_url('admin/master/tahun_ajaran/create') ?>';
    
    fetch(url, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire('Berhasil!', 'Data berhasil disimpan', 'success')
                .then(() => location.reload());
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    });
});

document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        const search = this.value;
        const perPage = document.getElementById('perPageSelect').value;
        window.location.href = `?search=${search}&per_page=${perPage}`;
    }
});

document.getElementById('perPageSelect').addEventListener('change', function() {
    const search = document.getElementById('searchInput').value;
    window.location.href = `?search=${search}&per_page=${this.value}`;
});
</script>
