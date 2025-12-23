<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel_model extends CI_Model {

    private $table = 'mapel';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_mapel', $search);
            $this->db->or_like('nama_mapel', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama_mapel', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }

    public function count_all($search = '') {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('kode_mapel', $search);
            $this->db->or_like('nama_mapel', $search);
            $this->db->group_end();
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

    public function get_all_simple() {
        $this->db->order_by('nama_mapel', 'ASC');
        return $this->db->get($this->table)->result();
    }
}
