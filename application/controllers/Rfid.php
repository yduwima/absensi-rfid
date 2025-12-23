<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rfid extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Siswa_model');
        $this->load->model('Guru_model');
        $this->load->model('Absensi_harian_model');
        $this->load->model('Jam_kerja_model');
        $this->load->model('Hari_libur_model');
        $this->load->model('Wa_queue_model');
        $this->load->model('Settings_model');
        $this->load->model('Wa_config_model');
        $this->load->library('Wa_sender');
    }

    public function index() {
        $data['title'] = 'Absensi RFID - Sistem Absensi Sekolah';
        $data['settings'] = $this->Settings_model->get_settings();
        $data['absensi_hari_ini'] = $this->Absensi_harian_model->get_today_attendance();
        $this->load->view('rfid/index', $data);
    }

    public function scan() {
        // This endpoint receives RFID UID from the scanner
        $rfid_uid = $this->input->post('rfid_uid');
        
        if (empty($rfid_uid)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'RFID UID tidak ditemukan'
            ]);
            return;
        }

        // Log the scan
        $this->_log_rfid($rfid_uid, 'unknown', null, '', 'failed', 'Processing...');

        // Check if it's a working day
        $today = date('l'); // Get day name in English
        $hari = $this->_get_indonesian_day($today);
        
        // Check if today is a holiday
        if ($this->Hari_libur_model->is_holiday(date('Y-m-d'))) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Hari ini adalah hari libur'
            ]);
            return;
        }

        // Get jam kerja
        $jam_kerja = $this->Jam_kerja_model->get_by_hari($hari);
        
        if (!$jam_kerja || $jam_kerja->is_hari_kerja == 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Hari ini bukan hari kerja'
            ]);
            return;
        }

        // Check if RFID belongs to siswa or guru
        $siswa = $this->Siswa_model->get_by_rfid($rfid_uid);
        $guru = $this->Guru_model->get_by_rfid($rfid_uid);

        if ($siswa) {
            $result = $this->_process_siswa_attendance($siswa, $jam_kerja);
            echo json_encode($result);
        } elseif ($guru) {
            $result = $this->_process_guru_attendance($guru, $jam_kerja);
            echo json_encode($result);
        } else {
            $this->_log_rfid($rfid_uid, 'unknown', null, '', 'failed', 'RFID tidak terdaftar');
            echo json_encode([
                'status' => 'error',
                'message' => 'RFID tidak terdaftar dalam sistem'
            ]);
        }
    }

    private function _process_siswa_attendance($siswa, $jam_kerja) {
        $today = date('Y-m-d');
        $current_time = date('H:i:s');
        
        // Check if already has attendance today
        $existing = $this->Absensi_harian_model->get_by_user_and_date('siswa', $siswa->id, $today);
        
        if ($existing && $existing->jam_masuk && $existing->jam_pulang) {
            // Already completed attendance for today
            return [
                'status' => 'info',
                'message' => 'Anda sudah absen masuk dan pulang hari ini',
                'data' => $this->_format_user_data($siswa, 'siswa', $existing)
            ];
        }

        if (!$existing || !$existing->jam_masuk) {
            // First tap - Masuk
            $keterlambatan = 0;
            $status_masuk = 'Tepat Waktu';
            $keterangan = '';
            
            if ($current_time > $jam_kerja->jam_masuk) {
                $keterlambatan = $this->_calculate_late_minutes($jam_kerja->jam_masuk, $current_time);
                $status_masuk = 'Terlambat';
                $keterangan = 'Terlambat ' . $keterlambatan . ' menit';
            }
            
            $data = [
                'user_type' => 'siswa',
                'user_id' => $siswa->id,
                'tanggal' => $today,
                'jam_masuk' => $current_time,
                'status_masuk' => $status_masuk,
                'keterlambatan_menit' => $keterlambatan,
                'keterangan' => $keterangan
            ];
            
            $this->Absensi_harian_model->insert($data);
            $this->_log_rfid($siswa->rfid_uid, 'siswa', $siswa->id, 'masuk', 'success', 'Absen masuk berhasil');
            
            // Queue WhatsApp notification
            $this->_queue_wa_notification($siswa, 'masuk', $current_time, $keterangan);
            
            return [
                'status' => 'success',
                'action' => 'masuk',
                'message' => 'Absen masuk berhasil' . ($keterangan ? ' - ' . $keterangan : ''),
                'data' => $this->_format_user_data($siswa, 'siswa', null, $current_time, null, $keterangan)
            ];
            
        } else {
            // Second tap - Pulang
            $this->Absensi_harian_model->update($existing->id, [
                'jam_pulang' => $current_time
            ]);
            
            $this->_log_rfid($siswa->rfid_uid, 'siswa', $siswa->id, 'pulang', 'success', 'Absen pulang berhasil');
            
            // Queue WhatsApp notification for pulang
            $this->_queue_wa_notification($siswa, 'pulang', $current_time, '');
            
            return [
                'status' => 'success',
                'action' => 'pulang',
                'message' => 'Absen pulang berhasil',
                'data' => $this->_format_user_data($siswa, 'siswa', null, $existing->jam_masuk, $current_time)
            ];
        }
    }

    private function _process_guru_attendance($guru, $jam_kerja) {
        $today = date('Y-m-d');
        $current_time = date('H:i:s');
        
        $existing = $this->Absensi_harian_model->get_by_user_and_date('guru', $guru->id, $today);
        
        if ($existing && $existing->jam_masuk && $existing->jam_pulang) {
            return [
                'status' => 'info',
                'message' => 'Anda sudah absen masuk dan pulang hari ini',
                'data' => $this->_format_user_data($guru, 'guru', $existing)
            ];
        }

        if (!$existing || !$existing->jam_masuk) {
            // First tap - Masuk
            $keterlambatan = 0;
            $status_masuk = 'Tepat Waktu';
            $keterangan = '';
            
            if ($current_time > $jam_kerja->jam_masuk) {
                $keterlambatan = $this->_calculate_late_minutes($jam_kerja->jam_masuk, $current_time);
                $status_masuk = 'Terlambat';
                $keterangan = 'Terlambat ' . $keterlambatan . ' menit';
            }
            
            $data = [
                'user_type' => 'guru',
                'user_id' => $guru->id,
                'tanggal' => $today,
                'jam_masuk' => $current_time,
                'status_masuk' => $status_masuk,
                'keterlambatan_menit' => $keterlambatan,
                'keterangan' => $keterangan
            ];
            
            $this->Absensi_harian_model->insert($data);
            $this->_log_rfid($guru->rfid_uid, 'guru', $guru->id, 'masuk', 'success', 'Absen masuk berhasil');
            
            return [
                'status' => 'success',
                'action' => 'masuk',
                'message' => 'Absen masuk berhasil' . ($keterangan ? ' - ' . $keterangan : ''),
                'data' => $this->_format_user_data($guru, 'guru', null, $current_time, null, $keterangan)
            ];
            
        } else {
            // Second tap - Pulang
            $this->Absensi_harian_model->update($existing->id, [
                'jam_pulang' => $current_time
            ]);
            
            $this->_log_rfid($guru->rfid_uid, 'guru', $guru->id, 'pulang', 'success', 'Absen pulang berhasil');
            
            return [
                'status' => 'success',
                'action' => 'pulang',
                'message' => 'Absen pulang berhasil',
                'data' => $this->_format_user_data($guru, 'guru', null, $existing->jam_masuk, $current_time)
            ];
        }
    }

    private function _queue_wa_notification($siswa, $type, $time, $keterangan_terlambat) {
        // Check if WA notification is enabled
        $wa_config = $this->Wa_config_model->get_config();
        if (!$wa_config || $wa_config->is_active == 0) {
            return;
        }

        // Check if this class is eligible for WA notification
        if (!$this->Wa_config_model->is_kelas_active($siswa->kelas_id)) {
            return;
        }

        // Get school settings
        $settings = $this->Settings_model->get_settings();
        
        // Get kelas info
        $this->load->model('Kelas_model');
        $kelas = $this->Kelas_model->get_by_id($siswa->kelas_id);
        $kelas_name = $kelas ? $kelas->nama_kelas : '-';

        // Prepare message
        $template = $type == 'masuk' ? $wa_config->template_masuk : $wa_config->template_pulang;
        
        $replacements = [
            '{nama_siswa}' => $siswa->nama,
            '{kelas}' => $kelas_name,
            '{jam_masuk}' => $time,
            '{jam_pulang}' => $time,
            '{tanggal}' => date('d-m-Y'),
            '{keterangan_terlambat}' => $keterangan_terlambat ? "\n" . $keterangan_terlambat : '',
            '{nama_sekolah}' => $settings->nama_sekolah,
            '{nama_ortu}' => $siswa->nama_ortu
        ];
        
        $message = str_replace(array_keys($replacements), array_values($replacements), $template);
        
        // Queue the message
        if ($siswa->no_hp_ortu) {
            $this->Wa_queue_model->add_to_queue($siswa->no_hp_ortu, $message);
        }
    }

    private function _format_user_data($user, $type, $absensi = null, $jam_masuk = null, $jam_pulang = null, $keterangan = '') {
        if ($type == 'siswa') {
            $this->load->model('Kelas_model');
            $kelas = $this->Kelas_model->get_by_id($user->kelas_id);
            $kelas_name = $kelas ? $kelas->nama_kelas : '-';
            
            return [
                'type' => 'siswa',
                'nama' => $user->nama,
                'nis' => $user->nis,
                'kelas' => $kelas_name,
                'foto' => $user->foto ? base_url('assets/uploads/siswa/' . $user->foto) : base_url('assets/img/default-avatar.png'),
                'jam_masuk' => $absensi ? $absensi->jam_masuk : $jam_masuk,
                'jam_pulang' => $absensi ? $absensi->jam_pulang : $jam_pulang,
                'keterangan' => $absensi ? $absensi->keterangan : $keterangan
            ];
        } else {
            return [
                'type' => 'guru',
                'nama' => $user->nama,
                'nip' => $user->nip,
                'foto' => $user->foto ? base_url('assets/uploads/guru/' . $user->foto) : base_url('assets/img/default-avatar.png'),
                'jam_masuk' => $absensi ? $absensi->jam_masuk : $jam_masuk,
                'jam_pulang' => $absensi ? $absensi->jam_pulang : $jam_pulang,
                'keterangan' => $absensi ? $absensi->keterangan : $keterangan
            ];
        }
    }

    private function _calculate_late_minutes($scheduled_time, $actual_time) {
        $scheduled = strtotime($scheduled_time);
        $actual = strtotime($actual_time);
        return round(($actual - $scheduled) / 60);
    }

    private function _get_indonesian_day($english_day) {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        return isset($days[$english_day]) ? $days[$english_day] : 'Senin';
    }

    private function _log_rfid($rfid_uid, $user_type, $user_id, $action, $status, $message) {
        $this->db->insert('rfid_log', [
            'rfid_uid' => $rfid_uid,
            'user_type' => $user_type,
            'user_id' => $user_id,
            'action' => $action,
            'status' => $status,
            'message' => $message
        ]);
    }

    public function get_today_attendance() {
        $data = $this->Absensi_harian_model->get_today_attendance();
        echo json_encode(['status' => 'success', 'data' => $data]);
    }
}
