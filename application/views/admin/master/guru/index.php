<div class="mb-6 flex justify-between items-center">
    <div>
        <form method="get" class="flex gap-2">
            <input type="text" name="search" placeholder="Cari guru..." value="<?= $this->input->get('search') ?>" 
                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari
            </button>
        </form>
    </div>
    <button onclick="openAddModal()" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i>Tambah Guru
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No HP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php if (empty($guru)): ?>
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
            </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($guru as $g): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $no++ ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $g->nip ?? '-' ?></td>
                    <td class="px-6 py-4"><?= $g->nama ?></td>
                    <td class="px-6 py-4"><?= $g->email ?></td>
                    <td class="px-6 py-4"><?= $g->no_hp ?? '-' ?></td>
                    <td class="px-6 py-4">
                        <?php
                        $roles = explode(',', $g->role);
                        foreach ($roles as $role) {
                            echo '<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">' . trim($role) . '</span>';
                        }
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="editGuru(<?= $g->id ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="<?= base_url('admin/master/guru/delete/' . $g->id) ?>" 
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
<div id="guruModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Tambah Guru</h3>
            <button onclick="closeModal()" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="guruForm">
            <input type="hidden" id="guru_id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2">NIP</label>
                    <input type="text" name="nip" id="nip" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Nama *</label>
                    <input type="text" name="nama" id="nama" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Password *</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg">
                    <small class="text-gray-500">Kosongkan jika tidak ingin mengubah</small>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">No HP</label>
                    <input type="text" name="no_hp" id="no_hp" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">- Pilih -</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Role *</label>
                    <select name="role" id="role" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="Guru">Guru</option>
                        <option value="Guru,Piket">Guru + Piket</option>
                        <option value="Guru,BK">Guru + BK</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">RFID UID</label>
                    <input type="text" name="rfid_uid" id="rfid_uid" class="w-full px-4 py-2 border rounded-lg">
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
    document.getElementById('guruModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Tambah Guru';
    document.getElementById('guruForm').reset();
    document.getElementById('guru_id').value = '';
    document.getElementById('password').required = true;
}

function closeModal() {
    document.getElementById('guruModal').classList.add('hidden');
}

function editGuru(id) {
    fetch('<?= base_url('admin/master/guru/get_by_id/') ?>' + id)
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                const data = result.data;
                document.getElementById('guru_id').value = data.id;
                document.getElementById('nip').value = data.nip || '';
                document.getElementById('nama').value = data.nama;
                document.getElementById('email').value = data.email;
                document.getElementById('email').readOnly = true;
                document.getElementById('no_hp').value = data.no_hp || '';
                document.getElementById('jenis_kelamin').value = data.jenis_kelamin || '';
                document.getElementById('alamat').value = data.alamat || '';
                document.getElementById('role').value = data.role;
                document.getElementById('rfid_uid').value = data.rfid_uid || '';
                document.getElementById('password').required = false;
                
                document.getElementById('modalTitle').textContent = 'Edit Guru';
                document.getElementById('guruModal').classList.remove('hidden');
            }
        });
}

document.getElementById('guruForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const guruId = document.getElementById('guru_id').value;
    const url = guruId ? 
        '<?= base_url('admin/master/guru/update/') ?>' + guruId : 
        '<?= base_url('admin/master/guru/store') ?>';
    
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
