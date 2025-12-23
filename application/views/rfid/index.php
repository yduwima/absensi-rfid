<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Absensi RFID' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .clock {
            font-family: 'Courier New', monospace;
        }
        .scan-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-600 to-purple-700 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <?php if ($settings && $settings->logo_sekolah): ?>
                    <img src="<?= base_url('assets/uploads/logo/' . $settings->logo_sekolah) ?>" alt="Logo" class="h-16 mr-4">
                    <?php else: ?>
                    <i class="fas fa-school text-4xl text-blue-600 mr-4"></i>
                    <?php endif; ?>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800"><?= $settings ? $settings->nama_sekolah : 'Sistem Absensi RFID' ?></h1>
                        <p class="text-gray-600"><?= $settings ? $settings->alamat_sekolah : '' ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <div id="current-date" class="text-gray-700 font-semibold"></div>
                    <div id="current-time" class="text-3xl font-bold text-blue-600 clock"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Scanner Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-2xl p-6">
                    <div class="text-center mb-6">
                        <div class="scan-animation">
                            <i class="fas fa-id-card text-8xl text-blue-600 mb-4"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan Kartu RFID</h2>
                        <p class="text-gray-600">Tempelkan kartu Anda pada reader</p>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="rfid_input" autofocus
                            class="w-full px-4 py-3 border-2 border-blue-500 rounded-lg text-center text-2xl font-bold focus:outline-none focus:ring-2 focus:ring-blue-600"
                            placeholder="RFID UID">
                    </div>

                    <div id="scan_result" class="hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg p-6 mb-4">
                            <div class="text-center mb-4">
                                <img id="user_photo" src="" alt="Foto" class="w-32 h-32 rounded-full mx-auto border-4 border-white shadow-lg mb-4">
                                <h3 id="user_name" class="text-2xl font-bold"></h3>
                                <p id="user_info" class="text-lg"></p>
                            </div>
                            <div class="border-t border-white pt-4">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <p class="text-sm opacity-75">Jam Masuk</p>
                                        <p id="jam_masuk" class="text-xl font-bold">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm opacity-75">Jam Pulang</p>
                                        <p id="jam_pulang" class="text-xl font-bold">-</p>
                                    </div>
                                </div>
                                <div class="mt-4 text-center">
                                    <p id="keterangan" class="text-sm"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-2xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-history mr-2"></i>Riwayat Absensi Hari Ini
                        </h2>
                        <button onclick="refreshAttendance()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Waktu</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Nama</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Kelas/NIP</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="attendance_list">
                                <?php if (!empty($absensi_hari_ini)): ?>
                                    <?php foreach ($absensi_hari_ini as $absen): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($absen->jam_pulang): ?>
                                                <span class="text-red-600"><i class="fas fa-sign-out-alt mr-1"></i><?= $absen->jam_pulang ?></span>
                                            <?php else: ?>
                                                <span class="text-green-600"><i class="fas fa-sign-in-alt mr-1"></i><?= $absen->jam_masuk ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold"><?= $absen->nama ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($absen->type == 'siswa'): ?>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded"><?= $absen->nama_kelas ?></span>
                                            <?php else: ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded"><?= $absen->nip ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($absen->jam_pulang): ?>
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Pulang</span>
                                            <?php else: ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Masuk</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if ($absen->keterlambatan_menit > 0): ?>
                                                <span class="text-red-600">Terlambat <?= $absen->keterlambatan_menit ?> menit</span>
                                            <?php else: ?>
                                                <span class="text-green-600">Tepat Waktu</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-4xl mb-2"></i>
                                            <p>Belum ada data absensi hari ini</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio feedback -->
    <audio id="success_audio" src="<?= base_url('assets/audio/success.mp3') ?>" preload="auto"></audio>
    <audio id="error_audio" src="<?= base_url('assets/audio/error.mp3') ?>" preload="auto"></audio>

    <script>
        // Update clock
        function updateClock() {
            const now = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            document.getElementById('current-date').textContent = `${dayName}, ${date} ${month} ${year}`;
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        setInterval(updateClock, 1000);
        updateClock();

        // RFID scanning
        $('#rfid_input').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                const rfid_uid = $(this).val().trim();
                
                if (rfid_uid) {
                    processRFID(rfid_uid);
                    $(this).val('');
                }
            }
        });

        // Keep focus on input
        setInterval(function() {
            if (!$('#rfid_input').is(':focus')) {
                $('#rfid_input').focus();
            }
        }, 1000);

        function processRFID(rfid_uid) {
            $.ajax({
                url: '<?= base_url('rfid/scan') ?>',
                type: 'POST',
                data: {
                    rfid_uid: rfid_uid,
                    <?= $this->security->get_csrf_token_name() ?>: '<?= $this->security->get_csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showSuccess(response);
                        playAudio('success');
                        refreshAttendance();
                    } else if (response.status === 'info') {
                        showInfo(response);
                        playAudio('success');
                    } else {
                        showError(response.message);
                        playAudio('error');
                    }
                },
                error: function() {
                    showError('Terjadi kesalahan sistem. Silakan coba lagi.');
                    playAudio('error');
                }
            });
        }

        function showSuccess(response) {
            const data = response.data;
            $('#user_photo').attr('src', data.foto);
            $('#user_name').text(data.nama);
            
            if (data.type === 'siswa') {
                $('#user_info').html(`<i class="fas fa-graduation-cap mr-2"></i>${data.nis} - ${data.kelas}`);
            } else {
                $('#user_info').html(`<i class="fas fa-chalkboard-teacher mr-2"></i>NIP: ${data.nip}`);
            }
            
            $('#jam_masuk').text(data.jam_masuk || '-');
            $('#jam_pulang').text(data.jam_pulang || '-');
            $('#keterangan').text(data.keterangan || '');
            
            $('#scan_result').removeClass('hidden');
            
            Swal.fire({
                icon: 'success',
                title: response.message,
                timer: 3000,
                showConfirmButton: false
            });
            
            setTimeout(function() {
                $('#scan_result').addClass('hidden');
            }, 5000);
        }

        function showInfo(response) {
            Swal.fire({
                icon: 'info',
                title: response.message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        function playAudio(type) {
            const audio = document.getElementById(type + '_audio');
            if (audio) {
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        }

        function refreshAttendance() {
            $.ajax({
                url: '<?= base_url('rfid/get_today_attendance') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        updateAttendanceList(response.data);
                    }
                }
            });
        }

        function updateAttendanceList(data) {
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(absen) {
                    let timeDisplay = '';
                    let statusBadge = '';
                    
                    if (absen.jam_pulang) {
                        timeDisplay = `<span class="text-red-600"><i class="fas fa-sign-out-alt mr-1"></i>${absen.jam_pulang}</span>`;
                        statusBadge = '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Pulang</span>';
                    } else {
                        timeDisplay = `<span class="text-green-600"><i class="fas fa-sign-in-alt mr-1"></i>${absen.jam_masuk}</span>`;
                        statusBadge = '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Masuk</span>';
                    }
                    
                    let infoDisplay = '';
                    if (absen.type === 'siswa') {
                        infoDisplay = `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">${absen.nama_kelas}</span>`;
                    } else {
                        infoDisplay = `<span class="bg-green-100 text-green-800 px-2 py-1 rounded">${absen.nip}</span>`;
                    }
                    
                    let keteranganDisplay = '';
                    if (absen.keterlambatan_menit > 0) {
                        keteranganDisplay = `<span class="text-red-600">Terlambat ${absen.keterlambatan_menit} menit</span>`;
                    } else {
                        keteranganDisplay = '<span class="text-green-600">Tepat Waktu</span>';
                    }
                    
                    html += `
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">${timeDisplay}</td>
                            <td class="px-4 py-3 text-sm font-semibold">${absen.nama}</td>
                            <td class="px-4 py-3 text-sm">${infoDisplay}</td>
                            <td class="px-4 py-3 text-sm">${statusBadge}</td>
                            <td class="px-4 py-3 text-sm">${keteranganDisplay}</td>
                        </tr>
                    `;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada data absensi hari ini</p>
                        </td>
                    </tr>
                `;
            }
            
            $('#attendance_list').html(html);
        }

        // Auto refresh every 30 seconds
        setInterval(refreshAttendance, 30000);
    </script>
</body>
</html>
