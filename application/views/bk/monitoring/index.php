<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Monitoring Siswa Bermasalah</h2>
            <form method="post" action="<?= base_url('bk/monitoring/generate') ?>" class="inline">
                <input type="hidden" name="bulan" value="<?= $bulan ?>">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-sync mr-2"></i>Generate Monitoring</button>
            </form>
        </div>

        <div class="mb-4 flex gap-4">
            <input type="month" id="bulanFilter" value="<?= $bulan ?>" class="px-4 py-2 border rounded-lg">
            <button onclick="filterBulan()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Filter</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Nama Siswa</th>
                        <th class="px-4 py-2">Kelas</th>
                        <th class="px-4 py-2">Bulan</th>
                        <th class="px-4 py-2">Alpha</th>
                        <th class="px-4 py-2">Terlambat</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($monitoring)): ?>
                        <tr><td colspan="7" class="text-center py-4">Tidak ada data. Klik "Generate Monitoring" untuk memulai.</td></tr>
                    <?php else: ?>
                        <?php foreach ($monitoring as $m): ?>
                            <tr class="border-b">
                                <td class="px-4 py-2"><?= $m->nama_siswa ?></td>
                                <td class="px-4 py-2"><?= $m->nama_kelas ?></td>
                                <td class="px-4 py-2"><?= $m->bulan ?></td>
                                <td class="px-4 py-2 text-center"><span class="bg-red-100 text-red-800 px-2 py-1 rounded"><?= $m->jumlah_alpha ?></span></td>
                                <td class="px-4 py-2 text-center"><span class="bg-orange-100 text-orange-800 px-2 py-1 rounded"><?= $m->jumlah_terlambat ?></span></td>
                                <td class="px-4 py-2">
                                    <?php if ($m->status == 'Perlu Perhatian'): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">Perlu Perhatian</span>
                                    <?php elseif ($m->status == 'Panggilan'): ?>
                                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs">Panggilan</span>
                                    <?php elseif ($m->status == 'Surat Terbit'): ?>
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Surat Terbit</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs"><?= $m->status ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <?php if ($m->status != 'Surat Terbit'): ?>
                                        <a href="<?= base_url('bk/monitoring/create_surat/'.$m->id) ?>" class="text-purple-600 hover:text-purple-800"><i class="fas fa-file-alt"></i> Buat Surat</a>
                                    <?php else: ?>
                                        <span class="text-gray-400"><i class="fas fa-check"></i> Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
            <h4 class="font-semibold mb-2">Kriteria Monitoring:</h4>
            <ul class="list-disc list-inside text-sm text-gray-700">
                <li><strong>Perlu Perhatian:</strong> Alpha ≥ 3 kali atau Terlambat ≥ 5 kali dalam sebulan</li>
                <li><strong>Panggilan:</strong> Status setelah monitoring dibuat</li>
                <li><strong>Surat Terbit:</strong> Surat pemanggilan/peringatan sudah dibuat</li>
            </ul>
        </div>
    </div>
</div>

<script>
function filterBulan() {
    const bulan = document.getElementById('bulanFilter').value;
    window.location.href = '?bulan=' + bulan;
}
</script>
