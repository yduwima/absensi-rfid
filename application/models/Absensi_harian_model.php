<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_harian_model extends CI_Model {

    private $table = 'absensi_harian';

    public function get_by_user_and_date($user_type, $user_id, $tanggal) {
        return $this->db->get_where($this->table, [
            'user_type' => $user_type,
            'user_id' => $user_id,
            'tanggal' => $tanggal
        ])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function get_today_attendance() {
        $today = date('Y-m-d');
        
        // Get siswa attendance
        $this->db->select('absensi_harian.*, siswa.nama, siswa.nis, siswa.foto, kelas.nama_kelas, "siswa" as type');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = absensi_harian.user_id AND absensi_harian.user_type = "siswa"', 'inner');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('absensi_harian.tanggal', $today);
        $this->db->where('absensi_harian.user_type', 'siswa');
        $this->db->order_by('absensi_harian.created_at', 'DESC');
        $siswa_attendance = $this->db->get()->result();
        
        // Get guru attendance
        $this->db->select('absensi_harian.*, guru.nama, guru.nip, guru.foto, "guru" as type');
        $this->db->from($this->table);
        $this->db->join('guru', 'guru.id = absensi_harian.user_id AND absensi_harian.user_type = "guru"', 'inner');
        $this->db->where('absensi_harian.tanggal', $today);
        $this->db->where('absensi_harian.user_type', 'guru');
        $this->db->order_by('absensi_harian.created_at', 'DESC');
        $guru_attendance = $this->db->get()->result();
        
        return array_merge($siswa_attendance, $guru_attendance);
    }

    public function count_today_siswa() {
        $today = date('Y-m-d');
        $this->db->where('tanggal', $today);
        $this->db->where('user_type', 'siswa');
        $this->db->where('jam_masuk IS NOT NULL');
        return $this->db->count_all_results($this->table);
    }

    public function count_today_guru() {
        $today = date('Y-m-d');
        $this->db->where('tanggal', $today);
        $this->db->where('user_type', 'guru');
        $this->db->where('jam_masuk IS NOT NULL');
        return $this->db->count_all_results($this->table);
    }

    public function get_monthly_report($user_type, $month, $year, $search = '', $limit = 10, $offset = 0) {
        $this->db->select('absensi_harian.*');
        
        if ($user_type == 'siswa') {
            $this->db->select('siswa.nama, siswa.nis, kelas.nama_kelas');
            $this->db->from($this->table);
            $this->db->join('siswa', 'siswa.id = absensi_harian.user_id', 'inner');
            $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
            
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('siswa.nama', $search);
                $this->db->or_like('siswa.nis', $search);
                $this->db->group_end();
            }
        } else {
            $this->db->select('guru.nama, guru.nip');
            $this->db->from($this->table);
            $this->db->join('guru', 'guru.id = absensi_harian.user_id', 'inner');
            
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('guru.nama', $search);
                $this->db->or_like('guru.nip', $search);
                $this->db->group_end();
            }
        }
        
        $this->db->where('absensi_harian.user_type', $user_type);
        $this->db->where('MONTH(absensi_harian.tanggal)', $month);
        $this->db->where('YEAR(absensi_harian.tanggal)', $year);
        $this->db->order_by('absensi_harian.tanggal', 'DESC');
        $this->db->limit($limit, $offset);
        
        return $this->db->get()->result();
    }

    public function count_monthly_report($user_type, $month, $year, $search = '') {
        if ($user_type == 'siswa') {
            $this->db->from($this->table);
            $this->db->join('siswa', 'siswa.id = absensi_harian.user_id', 'inner');
            
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('siswa.nama', $search);
                $this->db->or_like('siswa.nis', $search);
                $this->db->group_end();
            }
        } else {
            $this->db->from($this->table);
            $this->db->join('guru', 'guru.id = absensi_harian.user_id', 'inner');
            
            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('guru.nama', $search);
                $this->db->or_like('guru.nip', $search);
                $this->db->group_end();
            }
        }
        
        $this->db->where('absensi_harian.user_type', $user_type);
        $this->db->where('MONTH(absensi_harian.tanggal)', $month);
        $this->db->where('YEAR(absensi_harian.tanggal)', $year);
        
        return $this->db->count_all_results();
    }

    public function get_laporan_siswa($bulan, $tahun, $kelas_id = null) {
        $this->db->select('absensi_harian.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = absensi_harian.user_id', 'inner');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('absensi_harian.user_type', 'siswa');
        $this->db->where('MONTH(absensi_harian.tanggal)', $bulan);
        $this->db->where('YEAR(absensi_harian.tanggal)', $tahun);
        
        if ($kelas_id) {
            $this->db->where('siswa.kelas_id', $kelas_id);
        }
        
        $this->db->order_by('absensi_harian.tanggal', 'DESC');
        $this->db->order_by('siswa.nama', 'ASC');
        
        return $this->db->get()->result();
    }

    public function get_laporan_guru($bulan, $tahun) {
        $this->db->select('absensi_harian.*, guru.nama as nama_guru, guru.nip');
        $this->db->from($this->table);
        $this->db->join('guru', 'guru.id = absensi_harian.user_id', 'inner');
        $this->db->where('absensi_harian.user_type', 'guru');
        $this->db->where('MONTH(absensi_harian.tanggal)', $bulan);
        $this->db->where('YEAR(absensi_harian.tanggal)', $tahun);
        $this->db->order_by('absensi_harian.tanggal', 'DESC');
        $this->db->order_by('guru.nama', 'ASC');
        
        return $this->db->get()->result();
    }

    public function get_rekap_siswa($siswa_id, $bulan, $tahun) {
        $this->db->select('COUNT(*) as total_hadir, SUM(keterlambatan_menit) as total_terlambat');
        $this->db->from($this->table);
        $this->db->where('user_type', 'siswa');
        $this->db->where('user_id', $siswa_id);
        $this->db->where('MONTH(tanggal)', $bulan);
        $this->db->where('YEAR(tanggal)', $tahun);
        $this->db->where('jam_masuk IS NOT NULL');
        
        return $this->db->get()->row();
    }
}
