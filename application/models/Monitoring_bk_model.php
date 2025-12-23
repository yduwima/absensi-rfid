<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring_bk_model extends CI_Model {

    private $table = 'monitoring_bk';

    public function get_all($search = '', $limit = 10, $offset = 0, $status = null) {
        $this->db->select('monitoring_bk.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = monitoring_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        if ($status) {
            $this->db->where('monitoring_bk.status', $status);
        }
        
        $this->db->order_by('monitoring_bk.tahun', 'DESC');
        $this->db->order_by('monitoring_bk.bulan', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '', $status = null) {
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = monitoring_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        if ($status) {
            $this->db->where('monitoring_bk.status', $status);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('monitoring_bk.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = monitoring_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('monitoring_bk.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_siswa_and_period($siswa_id, $month, $year) {
        return $this->db->get_where($this->table, [
            'siswa_id' => $siswa_id,
            'bulan' => $month,
            'tahun' => $year
        ])->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function update_or_create($siswa_id, $month, $year, $data) {
        $existing = $this->get_by_siswa_and_period($siswa_id, $month, $year);
        
        if ($existing) {
            return $this->db->update($this->table, $data, [
                'siswa_id' => $siswa_id,
                'bulan' => $month,
                'tahun' => $year
            ]);
        } else {
            $data['siswa_id'] = $siswa_id;
            $data['bulan'] = $month;
            $data['tahun'] = $year;
            return $this->db->insert($this->table, $data);
        }
    }

    public function auto_generate_monitoring($month, $year) {
        // Get all active students
        $this->db->select('siswa.id as siswa_id');
        $this->db->from('siswa');
        $this->db->where('siswa.is_active', 1);
        $students = $this->db->get()->result();
        
        foreach ($students as $student) {
            // Count alpha in absensi_harian
            $this->db->from('absensi_harian');
            $this->db->where('user_type', 'siswa');
            $this->db->where('user_id', $student->siswa_id);
            $this->db->where('MONTH(tanggal)', $month);
            $this->db->where('YEAR(tanggal)', $year);
            $this->db->where('jam_masuk IS NULL');
            $total_alpha_harian = $this->db->count_all_results();
            
            // Count alpha in absensi_mapel
            $this->db->from('absensi_mapel');
            $this->db->join('jurnal', 'jurnal.id = absensi_mapel.jurnal_id', 'inner');
            $this->db->where('absensi_mapel.siswa_id', $student->siswa_id);
            $this->db->where('absensi_mapel.status', 'A');
            $this->db->where('MONTH(jurnal.tanggal)', $month);
            $this->db->where('YEAR(jurnal.tanggal)', $year);
            $total_alpha_mapel = $this->db->count_all_results();
            
            $total_alpha = $total_alpha_harian + $total_alpha_mapel;
            
            // Count late arrivals
            $this->db->from('absensi_harian');
            $this->db->where('user_type', 'siswa');
            $this->db->where('user_id', $student->siswa_id);
            $this->db->where('MONTH(tanggal)', $month);
            $this->db->where('YEAR(tanggal)', $year);
            $this->db->where('status_masuk', 'Terlambat');
            $total_terlambat = $this->db->count_all_results();
            
            // Determine status
            $status = 'Normal';
            if ($total_alpha >= 3 || $total_terlambat >= 5) {
                $status = 'Perlu Perhatian';
            }
            if ($total_alpha >= 5 || $total_terlambat >= 8) {
                $status = 'Panggilan';
            }
            
            // Update or create monitoring record
            $this->update_or_create($student->siswa_id, $month, $year, [
                'total_alpha' => $total_alpha,
                'total_terlambat' => $total_terlambat,
                'status' => $status
            ]);
        }
        
        return true;
    }

    public function get_siswa_need_attention($month, $year) {
        $this->db->select('monitoring_bk.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas,
                          siswa.nama_ortu, siswa.no_hp_ortu');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = monitoring_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('monitoring_bk.bulan', $month);
        $this->db->where('monitoring_bk.tahun', $year);
        $this->db->where_in('monitoring_bk.status', ['Perlu Perhatian', 'Panggilan']);
        $this->db->order_by('monitoring_bk.total_alpha', 'DESC');
        $this->db->order_by('monitoring_bk.total_terlambat', 'DESC');
        return $this->db->get()->result();
    }
}
