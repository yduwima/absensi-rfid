<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Izin_siswa_model extends CI_Model {

    private $table = 'izin_siswa';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        $this->db->select('izin_siswa.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas,
                          guru_piket.nama as nama_guru_piket, wali_kelas.nama as nama_wali_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = izin_siswa.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->join('guru as guru_piket', 'guru_piket.id = izin_siswa.guru_piket_id', 'left');
        $this->db->join('guru as wali_kelas', 'wali_kelas.id = izin_siswa.wali_kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('izin_siswa.tanggal', 'DESC');
        $this->db->order_by('izin_siswa.waktu', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '') {
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = izin_siswa.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('kelas.nama_kelas', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('izin_siswa.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = izin_siswa.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('izin_siswa.id', $id);
        return $this->db->get()->row();
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

    public function get_by_date($tanggal) {
        $this->db->select('izin_siswa.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = izin_siswa.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('izin_siswa.tanggal', $tanggal);
        $this->db->order_by('izin_siswa.waktu', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_siswa_and_month($siswa_id, $month, $year) {
        $this->db->where('siswa_id', $siswa_id);
        $this->db->where('MONTH(tanggal)', $month);
        $this->db->where('YEAR(tanggal)', $year);
        $this->db->order_by('tanggal', 'ASC');
        return $this->db->get($this->table)->result();
    }

    public function get_by_kelas($kelas_id, $tanggal = null) {
        $this->db->select('izin_siswa.*, siswa.nama as nama_siswa, siswa.nis');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = izin_siswa.siswa_id', 'inner');
        $this->db->where('siswa.kelas_id', $kelas_id);
        
        if ($tanggal) {
            $this->db->where('izin_siswa.tanggal', $tanggal);
        }
        
        $this->db->order_by('izin_siswa.tanggal', 'DESC');
        return $this->db->get()->result();
    }

    public function count_by_jenis($jenis_izin, $month, $year) {
        $this->db->where('jenis_izin', $jenis_izin);
        $this->db->where('MONTH(tanggal)', $month);
        $this->db->where('YEAR(tanggal)', $year);
        return $this->db->count_all_results($this->table);
    }
}
