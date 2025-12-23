<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas_model extends CI_Model {

    private $table = 'kelas';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        $this->db->select('kelas.*, guru.nama as nama_wali, tahun_ajaran.tahun_ajaran');
        $this->db->from($this->table);
        $this->db->join('guru', 'guru.id = kelas.wali_kelas_id', 'left');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = kelas.tahun_ajaran_id', 'left');
        
        if (!empty($search)) {
            $this->db->like('kelas.nama_kelas', $search);
        }
        
        $this->db->order_by('kelas.tingkat', 'ASC');
        $this->db->order_by('kelas.nama_kelas', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '') {
        if (!empty($search)) {
            $this->db->like('nama_kelas', $search);
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
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

    public function get_active() {
        $this->db->select('kelas.*');
        $this->db->from($this->table);
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = kelas.tahun_ajaran_id', 'inner');
        $this->db->where('tahun_ajaran.is_active', 1);
        $this->db->order_by('kelas.tingkat', 'ASC');
        $this->db->order_by('kelas.nama_kelas', 'ASC');
        return $this->db->get()->result();
    }
}
