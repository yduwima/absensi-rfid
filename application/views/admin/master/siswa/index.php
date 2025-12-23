<div class="mb-6 flex justify-between items-center">
    <div>
        <form method="get" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari siswa..." value="<?= $this->input->get('search') ?>" 
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>
    <button onclick="openAddModal()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Tambah Siswa
    </button>
</div>

<?php if ($this->session->flashdata('success')): ?>
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    <?= $this->session->flashdata('success') ?>
</div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    <?= $this->session->flashdata('error') ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Kelamin</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFID UID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($siswa)): ?>
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($siswa as $s): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $no++ ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $s->nis ?></td>
                    <td class="px-6 py-4"><?= $s->nama ?></td>
                    <td class="px-6 py-4"><?= $s->nama_kelas ?? '-' ?></td>
                    <td class="px-6 py-4"><?= $s->jenis_kelamin ?></td>
                    <td class="px-6 py-4"><?= $s->rfid_uid ?? '-' ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="editSiswa(<?= $s->id ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="<?= base_url('admin/master/siswa/delete/' . $s->id) ?>" 
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

<div class="mt-4 flex justify-between items-center">
    <div class="text-gray-600">
        Showing <?= count($siswa) ?> of <?= $total ?> entries
    </div>
    <div>
        <?= $this->pagination->create_links() ?>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="siswaModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Tambah Siswa</h3>
            <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="siswaForm">
            <input type="hidden" id="siswa_id" name="siswa_id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">NIS *</label>
                    <input type="text" name="nis" id="nis" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">NISN</label>
                    <input type="text" name="nisn" id="nisn" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" name="nama" id="nama" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Kelas</label>
                    <select name="kelas_id" id="kelas_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih Kelas -</option>
                        <?php foreach ($kelas_list as $k): ?>
                        <option value="<?= $k->id ?>"><?= $k->nama_kelas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Jenis Kelamin *</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih -</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="tempat_lahir" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Nama Orang Tua</label>
                    <input type="text" name="nama_ortu" id="nama_ortu" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">No HP Orang Tua</label>
                    <input type="text" name="no_hp_ortu" id="no_hp_ortu" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">RFID UID</label>
                    <input type="text" name="rfid_uid" id="rfid_uid" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
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
    document.getElementById('siswaModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Siswa';
    document.getElementById('siswaForm').reset();
    document.getElementById('siswa_id').value = '';
}

function closeModal() {
    document.getElementById('siswaModal').classList.add('hidden');
}

function editSiswa(id) {
    fetch('<?= base_url('admin/master/siswa/get_by_id/') ?>' + id)
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                const data = result.data;
                document.getElementById('siswa_id').value = data.id;
                document.getElementById('nis').value = data.nis;
                document.getElementById('nisn').value = data.nisn || '';
                document.getElementById('nama').value = data.nama;
                document.getElementById('kelas_id').value = data.kelas_id || '';
                document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
                document.getElementById('tempat_lahir').value = data.tempat_lahir || '';
                document.getElementById('tanggal_lahir').value = data.tanggal_lahir || '';
                document.getElementById('alamat').value = data.alamat || '';
                document.getElementById('nama_ortu').value = data.nama_ortu || '';
                document.getElementById('no_hp_ortu').value = data.no_hp_ortu || '';
                document.getElementById('rfid_uid').value = data.rfid_uid || '';
                
                document.getElementById('modalTitle').textContent = 'Edit Siswa';
                document.getElementById('siswaModal').classList.remove('hidden');
            }
        });
}

document.getElementById('siswaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const siswaId = document.getElementById('siswa_id').value;
    const url = siswaId ? 
        '<?= base_url('admin/master/siswa/update/') ?>' + siswaId : 
        '<?= base_url('admin/master/siswa/store') ?>';
    
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
