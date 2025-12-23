<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Monitoring</p>
                <p class="text-3xl font-bold text-purple-600"><?= $total_monitoring ?></p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Perlu Perhatian</p>
                <p class="text-3xl font-bold text-yellow-600"><?= $perlu_perhatian ?></p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Panggilan</p>
                <p class="text-3xl font-bold text-red-600"><?= $panggilan ?></p>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <i class="fas fa-bell text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Surat Bulan Ini</p>
                <p class="text-3xl font-bold text-blue-600"><?= $surat_bulan_ini ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="mb-4 flex justify-between items-center">
    <h3 class="text-xl font-semibold">Siswa yang Memerlukan Perhatian Bulan Ini</h3>
    <button onclick="generateMonitoring()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
        <i class="fas fa-sync mr-2"></i>Generate Monitoring
    </button>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($siswa_perlu_perhatian)): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
            <p class="text-xl">Tidak ada siswa yang memerlukan perhatian bulan ini</p>
        </div>
    <?php else: ?>
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alpha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $no = 1; foreach ($siswa_perlu_perhatian as $s): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $no++ ?></td>
                    <td class="px-6 py-4 whitespace-nowrap"><?= $s->nis ?></td>
                    <td class="px-6 py-4"><?= $s->nama_siswa ?></td>
                    <td class="px-6 py-4"><?= $s->nama_kelas ?></td>
                    <td class="px-6 py-4">
                        <span class="text-red-600 font-bold"><?= $s->total_alpha ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-yellow-600 font-bold"><?= $s->total_terlambat ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <?php if ($s->status == 'Panggilan'): ?>
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Panggilan</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Perlu Perhatian</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?= base_url('bk/surat/create/' . $s->siswa_id) ?>" 
                           class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-envelope mr-1"></i> Buat Surat
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
function generateMonitoring() {
    Swal.fire({
        title: 'Generate Monitoring',
        text: 'Ini akan memperbarui data monitoring untuk bulan ini. Lanjutkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('bk/dashboard/generate_monitoring') ?>', {
                method: 'POST'
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
        }
    });
}
</script>
