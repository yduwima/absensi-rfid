<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru_model extends CI_Model {

    private $table = 'guru';

    public function get_all($search = '', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nip', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }

    public function count_all($search = '') {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nip', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function get_by_email($email) {
        return $this->db->get_where($this->table, ['email' => $email])->row();
    }

    public function get_by_nip($nip) {
        return $this->db->get_where($this->table, ['nip' => $nip])->row();
    }

    public function get_by_rfid($rfid_uid) {
        return $this->db->get_where($this->table, ['rfid_uid' => $rfid_uid, 'is_active' => 1])->row();
    }

    public function insert($data) {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        // Hash password if being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']); // Don't update if empty
        }
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function get_by_role($role) {
        $this->db->like('role', $role);
        $this->db->where('is_active', 1);
        return $this->db->get($this->table)->result();
    }

    public function count_total() {
        $this->db->where('is_active', 1);
        return $this->db->count_all_results($this->table);
    }

    public function get_wali_kelas() {
        $this->db->select('guru.*');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.wali_kelas_id = guru.id', 'inner');
        $this->db->where('guru.is_active', 1);
        $this->db->group_by('guru.id');
        return $this->db->get()->result();
    }
}
