<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Laporan Absensi Guru</h1>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Export Buttons -->
    <div class="mb-4">
        <a href="<?= base_url('admin/laporan_absensi/export_pdf_guru?bulan='.$bulan.'&tahun='.$tahun) ?>" 
           class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 inline-block">
            <i class="fas fa-file-pdf mr-2"></i> Export PDF
        </a>
        <a href="<?= base_url('admin/laporan_absensi/export_excel_guru?bulan='.$bulan.'&tahun='.$tahun) ?>" 
           class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 inline-block ml-2">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </a>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam Pulang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($absensi)): ?>
                    <?php $no = 1; foreach ($absensi as $row): ?>
                        <tr>
                            <td class="px-6 py-4"><?= $no++ ?></td>
                            <td class="px-6 py-4"><?= $row->nip ?></td>
                            <td class="px-6 py-4"><?= $row->nama_guru ?></td>
                            <td class="px-6 py-4"><?= format_tanggal($row->tanggal) ?></td>
                            <td class="px-6 py-4"><?= $row->jam_masuk ?? '-' ?></td>
                            <td class="px-6 py-4"><?= $row->jam_pulang ?? '-' ?></td>
                            <td class="px-6 py-4">
                                <?php if ($row->keterlambatan_menit > 0): ?>
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
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
