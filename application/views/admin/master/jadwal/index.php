<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Jadwal Pelajaran</h2>
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-2"></i>Tambah Jadwal</button>
        </div>

        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <div class="mb-4 flex gap-4">
            <select id="kelasFilter" class="px-4 py-2 border rounded-lg">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k->id ?>" <?= $this->input->get('kelas_id') == $k->id ? 'selected' : '' ?>><?= $k->nama_kelas ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" id="searchInput" placeholder="Cari..." class="flex-1 px-4 py-2 border rounded-lg" value="<?= $this->input->get('search') ?>">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-100">
                    <tr><th class="px-2 py-2">No</th><th class="px-2 py-2">Kelas</th><th class="px-2 py-2">Mapel</th><th class="px-2 py-2">Guru</th><th class="px-2 py-2">Hari</th><th class="px-2 py-2">Jam</th><th class="px-2 py-2">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($jadwal)): ?>
                        <tr><td colspan="7" class="text-center py-4">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php foreach ($jadwal as $index => $j): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-2 py-2"><?= $index + 1 ?></td>
                                <td class="px-2 py-2"><?= $j->nama_kelas ?></td>
                                <td class="px-2 py-2"><?= $j->nama_mapel ?></td>
                                <td class="px-2 py-2"><?= $j->nama_guru ?></td>
                                <td class="px-2 py-2"><?= $j->hari ?></td>
                                <td class="px-2 py-2"><?= substr($j->jam_mulai, 0, 5) ?> - <?= substr($j->jam_selesai, 0, 5) ?></td>
                                <td class="px-2 py-2 text-center">
                                    <button onclick="editData(<?= $j->id ?>)" class="text-blue-600 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="deleteData(<?= $j->id ?>)" class="text-red-600"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Tambah Jadwal</h3>
        <form id="dataForm" class="grid grid-cols-2 gap-4">
            <input type="hidden" id="dataId">
            <div>
                <label class="block text-sm mb-1">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Pilih</option><?php foreach ($kelas_list as $k): ?><option value="<?= $k->id ?>"><?= $k->nama_kelas ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Mapel</label>
                <select name="mapel_id" id="mapel_id" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Pilih</option><?php foreach ($mapel_list as $m): ?><option value="<?= $m->id ?>"><?= $m->nama_mapel ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Guru</label>
                <select name="guru_id" id="guru_id" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Pilih</option><?php foreach ($guru_list as $g): ?><option value="<?= $g->id ?>"><?= $g->nama ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Semester</label>
                <select name="semester_id" id="semester_id" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Pilih</option><?php foreach ($semester_list as $s): ?><option value="<?= $s->id ?>"><?= $s->nama_tahun_ajaran ?> - <?= $s->nama_semester ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Hari</label>
                <select name="hari" id="hari" class="w-full px-3 py-2 border rounded" required>
                    <option value="">Pilih</option><option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Jam Mulai</label>
                <input type="time" name="jam_mulai" id="jam_mulai" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="col-span-2">
                <label class="block text-sm mb-1">Jam Selesai</label>
                <input type="time" name="jam_selesai" id="jam_selesai" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="col-span-2 flex justify-end gap-2 mt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('formModal').classList.remove('hidden'); document.getElementById('formModal').classList.add('flex'); document.getElementById('dataForm').reset(); }
function closeModal() { document.getElementById('formModal').classList.add('hidden'); }
function editData(id) { fetch(`<?= base_url('admin/master/jadwal/get/') ?>${id}`).then(r => r.json()).then(d => { document.getElementById('dataId').value = d.id; document.getElementById('kelas_id').value = d.kelas_id; document.getElementById('mapel_id').value = d.mapel_id; document.getElementById('guru_id').value = d.guru_id; document.getElementById('semester_id').value = d.semester_id; document.getElementById('hari').value = d.hari; document.getElementById('jam_mulai').value = d.jam_mulai; document.getElementById('jam_selesai').value = d.jam_selesai; openModal(); }); }
function deleteData(id) { Swal.fire({ title: 'Konfirmasi', text: 'Yakin hapus?', icon: 'warning', showCancelButton: true }).then((result) => { if (result.isConfirmed) { fetch(`<?= base_url('admin/master/jadwal/delete/') ?>${id}`, { method: 'POST' }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Dihapus', 'success').then(() => location.reload()); }); } }); }
document.getElementById('dataForm').addEventListener('submit', function(e) { e.preventDefault(); const id = document.getElementById('dataId').value; const url = id ? `<?= base_url('admin/master/jadwal/edit/') ?>${id}` : '<?= base_url('admin/master/jadwal/create') ?>'; fetch(url, { method: 'POST', body: new FormData(this) }).then(r => r.json()).then(d => { if (d.status === 'success') Swal.fire('Berhasil!', 'Disimpan', 'success').then(() => location.reload()); else Swal.fire('Error!', d.message, 'error'); }); });
document.getElementById('kelasFilter').addEventListener('change', function() { window.location.href = `?kelas_id=${this.value}`; });
</script>
