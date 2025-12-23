<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Semester_model extends CI_Model {

    private $table = 'semester';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        $this->db->select('semester.*, tahun_ajaran.tahun_ajaran');
        $this->db->from($this->table);
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = semester.tahun_ajaran_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('semester.semester', $search);
            $this->db->or_like('tahun_ajaran.tahun_ajaran', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('semester.tanggal_mulai', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '') {
        $this->db->from($this->table);
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = semester.tahun_ajaran_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('semester.semester', $search);
            $this->db->or_like('tahun_ajaran.tahun_ajaran', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('semester.*, tahun_ajaran.tahun_ajaran');
        $this->db->from($this->table);
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id = semester.tahun_ajaran_id', 'left');
        $this->db->where('semester.id', $id);
        return $this->db->get()->row();
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

    public function get_by_tahun_ajaran($tahun_ajaran_id) {
        $this->db->where('tahun_ajaran_id', $tahun_ajaran_id);
        $this->db->order_by('tanggal_mulai', 'ASC');
        return $this->db->get($this->table)->result();
    }
}
