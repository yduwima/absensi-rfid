<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Naik Kelas</h2>

        <?php if($this->session->flashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $this->session->flashdata('success') ?>
        </div>
        <?php endif; ?>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Fitur ini digunakan untuk menaikkan siswa dari satu kelas ke kelas berikutnya secara massal.
                Pilih kelas asal, pilih siswa yang akan dinaikkan, lalu pilih kelas tujuan.
            </p>
        </div>

        <?php if($tahun_ajaran_aktif): ?>
        <div class="mb-4">
            <p class="text-sm text-gray-600">Tahun Ajaran Aktif: 
                <span class="font-semibold"><?= $tahun_ajaran_aktif->tahun_mulai ?>/<?= $tahun_ajaran_aktif->tahun_selesai ?></span>
            </p>
        </div>
        <?php endif; ?>

        <form id="naikKelasForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Kelas Asal -->
                <div>
                    <label class="block text-sm font-medium mb-2">Kelas Asal</label>
                    <select id="kelasAsal" name="kelas_asal_id" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="">Pilih Kelas Asal</option>
                        <?php foreach($kelas_list as $kelas): ?>
                        <option value="<?= $kelas->id ?>"><?= $kelas->nama_kelas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Kelas Tujuan -->
                <div>
                    <label class="block text-sm font-medium mb-2">Kelas Tujuan</label>
                    <select id="kelasTujuan" name="kelas_tujuan_id" class="w-full px-3 py-2 border rounded-lg" required>
                        <option value="">Pilih Kelas Tujuan</option>
                        <?php foreach($kelas_list as $kelas): ?>
                        <option value="<?= $kelas->id ?>"><?= $kelas->nama_kelas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Daftar Siswa -->
            <div id="siswaList" class="mb-6 hidden">
                <h3 class="text-lg font-semibold mb-4">Pilih Siswa yang Akan Dinaikkan</h3>
                <div class="mb-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" class="mr-2">
                        <span class="text-sm font-medium">Pilih Semua</span>
                    </label>
                </div>
                <div id="siswaContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto border rounded-lg p-4">
                    <!-- Will be populated by AJAX -->
                </div>
            </div>

            <button type="submit" id="btnNaikKelas" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 hidden">
                <i class="fas fa-arrow-up mr-2"></i> Proses Naik Kelas
            </button>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#kelasAsal').change(function() {
        const kelasId = $(this).val();
        
        if (kelasId) {
            $.ajax({
                url: '<?= base_url('admin/naik_kelas/get_siswa_by_kelas') ?>',
                type: 'POST',
                data: {kelas_id: kelasId},
                dataType: 'json',
                success: function(response) {
                    let html = '';
                    response.forEach(siswa => {
                        html += `
                            <label class="flex items-center p-2 border rounded hover:bg-gray-50">
                                <input type="checkbox" name="siswa_ids[]" value="${siswa.id}" class="siswa-checkbox mr-2">
                                <span class="text-sm">${siswa.nama}</span>
                            </label>
                        `;
                    });
                    
                    $('#siswaContainer').html(html);
                    $('#siswaList').removeClass('hidden');
                    $('#btnNaikKelas').removeClass('hidden');
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data siswa', 'error');
                }
            });
        } else {
            $('#siswaList').addClass('hidden');
            $('#btnNaikKelas').addClass('hidden');
        }
    });

    $('#selectAll').change(function() {
        $('.siswa-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('#naikKelasForm').submit(function(e) {
        e.preventDefault();
        
        const kelasAsal = $('#kelasAsal').val();
        const kelasTujuan = $('#kelasTujuan').val();
        const siswaIds = $('input[name="siswa_ids[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (!kelasTujuan) {
            Swal.fire('Peringatan', 'Pilih kelas tujuan terlebih dahulu', 'warning');
            return;
        }

        if (siswaIds.length === 0) {
            Swal.fire('Peringatan', 'Pilih minimal satu siswa', 'warning');
            return;
        }

        if (kelasAsal === kelasTujuan) {
            Swal.fire('Peringatan', 'Kelas asal dan tujuan tidak boleh sama', 'warning');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: `Naikan ${siswaIds.length} siswa ke kelas tujuan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Naikan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/naik_kelas/proses_naik_kelas') ?>',
                    type: 'POST',
                    data: {
                        kelas_asal_id: kelasAsal,
                        kelas_tujuan_id: kelasTujuan,
                        siswa_ids: siswaIds
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });
});
</script>
