<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Jadwal Hari Ini</p>
                <p class="text-3xl font-bold text-blue-600"><?= count($jadwal_hari_ini) ?></p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Jurnal Bulan Ini</p>
                <p class="text-3xl font-bold text-green-600"><?= $jurnal_bulan_ini ?></p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-book text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Kehadiran Saya</p>
                <p class="text-3xl font-bold text-purple-600"><?= $absensi_saya ?> hari</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-user-check text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold mb-4">Jadwal Mengajar Hari Ini</h3>
    
    <?php if (empty($jadwal_hari_ini)): ?>
        <p class="text-gray-500 text-center py-8">Tidak ada jadwal mengajar hari ini</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($jadwal_hari_ini as $jadwal): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?= date('H:i', strtotime($jadwal->jam_mulai)) ?> - <?= date('H:i', strtotime($jadwal->jam_selesai)) ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900"><?= $jadwal->nama_mapel ?></div>
                            <div class="text-sm text-gray-500"><?= $jadwal->kode_mapel ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $jadwal->nama_kelas ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="<?= base_url('guru/jurnal/isi/' . $jadwal->id) ?>" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit mr-1"></i> Isi Jurnal
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
