<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Rekap Absensi Siswa</h1>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 mb-2">Kelas</label>
                <select name="kelas_id" id="kelas_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_list as $kelas): ?>
                        <option value="<?= $kelas->id ?>" <?= $kelas_id == $kelas->id ? 'selected' : '' ?>>
                            <?= $kelas->nama_kelas ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Siswa</label>
                <select name="siswa_id" id="siswa_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php if (isset($siswa_list)): ?>
                        <?php foreach ($siswa_list as $siswa): ?>
                            <option value="<?= $siswa->id ?>" <?= $siswa_id == $siswa->id ? 'selected' : '' ?>>
                                <?= $siswa->nama ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Bulan</label>
                <select name="bulan" class="w-full border border-gray-300 rounded px-3 py-2">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= sprintf('%02d', $i) ?>" <?= $bulan == sprintf('%02d', $i) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Tahun</label>
                <select name="tahun" class="w-full border border-gray-300 rounded px-3 py-2">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="md:col-span-4">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-search mr-2"></i> Tampilkan Rekap
                </button>
            </div>
        </form>
    </div>

    <?php if (isset($rekap)): ?>
        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="text-gray-500 text-sm">Total Hari Sekolah</div>
                <div class="text-2xl font-bold"><?= count($rekap) ?></div>
            </div>
            <div class="bg-green-100 rounded-lg shadow-md p-4">
                <div class="text-gray-500 text-sm">Hadir</div>
                <div class="text-2xl font-bold text-green-600">
                    <?= count(array_filter($rekap, function($r) { return !empty($r->jam_masuk); })) ?>
                </div>
            </div>
            <div class="bg-red-100 rounded-lg shadow-md p-4">
                <div class="text-gray-500 text-sm">Alpha</div>
                <div class="text-2xl font-bold text-red-600">
                    <?= count(array_filter($rekap, function($r) { return empty($r->jam_masuk); })) ?>
                </div>
            </div>
            <div class="bg-yellow-100 rounded-lg shadow-md p-4">
                <div class="text-gray-500 text-sm">Terlambat</div>
                <div class="text-2xl font-bold text-yellow-600">
                    <?= count(array_filter($rekap, function($r) { return isset($r->keterlambatan_menit) && $r->keterlambatan_menit > 0; })) ?>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $no = 1; foreach ($rekap as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= $no++ ?></td>
                            <td class="px-6 py-4"><?= format_tanggal($row->tanggal) ?></td>
                            <td class="px-6 py-4"><?= $row->jam_masuk ?? '-' ?></td>
                            <td class="px-6 py-4"><?= $row->jam_pulang ?? '-' ?></td>
                            <td class="px-6 py-4">
                                <?php if (isset($row->keterlambatan_menit) && $row->keterlambatan_menit > 0): ?>
                                    <span class="text-red-600"><?= $row->keterlambatan_menit ?> menit</span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($row->jam_masuk): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Hadir</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded">Alpha</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Load siswa when kelas changes
$('#kelas_id').change(function() {
    var kelas_id = $(this).val();
    if (kelas_id) {
        $.get('<?= base_url('admin/get_siswa_by_kelas/') ?>' + kelas_id, function(data) {
            $('#siswa_id').html('<option value="">-- Pilih Siswa --</option>');
            $.each(JSON.parse(data), function(i, siswa) {
                $('#siswa_id').append('<option value="' + siswa.id + '">' + siswa.nama + '</option>');
            });
        });
    }
});
</script>
