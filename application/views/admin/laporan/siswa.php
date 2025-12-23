<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Laporan Absensi Siswa</h2>

        <!-- Filter -->
        <form method="GET" class="mb-6 bg-gray-50 p-4 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Bulan</label>
                    <select name="bulan" class="w-full px-3 py-2 border rounded-lg">
                        <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0,0,0,$m,1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tahun</label>
                    <select name="tahun" class="w-full px-3 py-2 border rounded-lg">
                        <?php for($y=date('Y')-2; $y<=date('Y')+1; $y++): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Semua Kelas</option>
                        <?php foreach($kelas_list as $kelas): ?>
                        <option value="<?= $kelas->id ?>" <?= $kelas_id == $kelas->id ? 'selected' : '' ?>>
                            <?= $kelas->nama_kelas ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Export Buttons -->
        <div class="mb-4 flex gap-2">
            <a href="<?= base_url('admin/laporan_absensi/export_pdf_siswa?bulan='.$bulan.'&tahun='.$tahun.'&kelas_id='.$kelas_id) ?>" 
               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </a>
            <a href="<?= base_url('admin/laporan_absensi/export_excel_siswa?bulan='.$bulan.'&tahun='.$tahun.'&kelas_id='.$kelas_id) ?>" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kelas</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jam Masuk</th>
                        <th class="px-4 py-3 text-left">Jam Pulang</th>
                        <th class="px-4 py-3 text-left">Terlambat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($absensi)): ?>
                    <?php $no = 1; foreach($absensi as $row): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3"><?= $no++ ?></td>
                        <td class="px-4 py-3"><?= $row->nama_siswa ?></td>
                        <td class="px-4 py-3"><?= $row->nama_kelas ?></td>
                        <td class="px-4 py-3"><?= format_tanggal($row->tanggal) ?></td>
                        <td class="px-4 py-3"><?= $row->jam_masuk ?: '-' ?></td>
                        <td class="px-4 py-3"><?= $row->jam_pulang ?: '-' ?></td>
                        <td class="px-4 py-3">
                            <?php if($row->keterlambatan_menit > 0): ?>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">
                                <?= $row->keterlambatan_menit ?> menit
                            </span>
                            <?php else: ?>
                            <span class="text-gray-500">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data absensi
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
