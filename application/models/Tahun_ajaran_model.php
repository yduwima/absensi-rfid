<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahun_ajaran_model extends CI_Model {

    private $table = 'tahun_ajaran';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            $this->db->like('tahun_ajaran', $search);
        }
        
        $this->db->order_by('tanggal_mulai', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }

    public function count_all($search = '') {
        if (!empty($search)) {
            $this->db->like('tahun_ajaran', $search);
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_active() {
        return $this->db->get_where($this->table, ['is_active' => 1])->row();
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

    public function set_active($id) {
        // Deactivate all
        $this->db->update($this->table, ['is_active' => 0]);
        // Activate selected
        return $this->db->update($this->table, ['is_active' => 1], ['id' => $id]);
    }
}
