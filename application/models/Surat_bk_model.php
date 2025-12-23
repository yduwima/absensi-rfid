<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_bk_model extends CI_Model {

    private $table = 'surat_bk';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        $this->db->select('surat_bk.*, siswa.nama as nama_siswa, siswa.nis, kelas.nama_kelas,
                          guru.nama as nama_pembuat');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = surat_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->join('guru', 'guru.id = surat_bk.created_by', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('surat_bk.nomor_surat', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('surat_bk.tanggal_surat', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '') {
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = surat_bk.siswa_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('surat_bk.nomor_surat', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('surat_bk.*, siswa.nama as nama_siswa, siswa.nis, siswa.jenis_kelamin,
                          siswa.tempat_lahir, siswa.tanggal_lahir, siswa.alamat,
                          kelas.nama_kelas, kelas.tingkat,
                          guru.nama as nama_pembuat, guru.nip as nip_pembuat');
        $this->db->from($this->table);
        $this->db->join('siswa', 'siswa.id = surat_bk.siswa_id', 'left');
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->join('guru', 'guru.id = surat_bk.created_by', 'left');
        $this->db->where('surat_bk.id', $id);
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

    public function get_by_siswa($siswa_id) {
        $this->db->select('surat_bk.*, guru.nama as nama_pembuat');
        $this->db->from($this->table);
        $this->db->join('guru', 'guru.id = surat_bk.created_by', 'left');
        $this->db->where('surat_bk.siswa_id', $siswa_id);
        $this->db->order_by('surat_bk.tanggal_surat', 'DESC');
        return $this->db->get()->result();
    }

    public function generate_nomor_surat($tahun = null) {
        if (!$tahun) {
            $tahun = date('Y');
        }
        
        // Get last number this year
        $this->db->select('nomor_surat');
        $this->db->from($this->table);
        $this->db->where('YEAR(tanggal_surat)', $tahun);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last = $this->db->get()->row();
        
        $number = 1;
        if ($last && preg_match('/^(\d+)/', $last->nomor_surat, $matches)) {
            $number = intval($matches[1]) + 1;
        }
        
        // Format: 001/BK/SMA/XII/2024
        return sprintf('%03d/BK/SMA/XII/%d', $number, $tahun);
    }

    public function count_by_jenis($jenis_surat, $month, $year) {
        $this->db->where('jenis_surat', $jenis_surat);
        $this->db->where('MONTH(tanggal_surat)', $month);
        $this->db->where('YEAR(tanggal_surat)', $year);
        return $this->db->count_all_results($this->table);
    }
}
