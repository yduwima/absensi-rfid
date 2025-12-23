<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa_model extends CI_Model {

    private $table = 'siswa';

    public function get_all($search = '', $limit = 10, $offset = 0, $kelas_id = null) {
        $this->db->select('siswa.*, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('siswa.nama', $search);
            $this->db->or_like('siswa.nis', $search);
            $this->db->or_like('siswa.nisn', $search);
            $this->db->group_end();
        }
        
        if ($kelas_id) {
            $this->db->where('siswa.kelas_id', $kelas_id);
        }
        
        $this->db->order_by('siswa.nama', 'ASC');
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
    }

    public function count_all($search = '', $kelas_id = null) {
        $this->db->from($this->table);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('nis', $search);
            $this->db->or_like('nisn', $search);
            $this->db->group_end();
        }
        
        if ($kelas_id) {
            $this->db->where('kelas_id', $kelas_id);
        }
        
        return $this->db->count_all_results();
    }

    public function get_by_id($id) {
        $this->db->select('siswa.*, kelas.nama_kelas');
        $this->db->from($this->table);
        $this->db->join('kelas', 'kelas.id = siswa.kelas_id', 'left');
        $this->db->where('siswa.id', $id);
        return $this->db->get()->row();
    }

    public function get_by_nis($nis) {
        return $this->db->get_where($this->table, ['nis' => $nis])->row();
    }

    public function get_by_rfid($rfid_uid) {
        return $this->db->get_where($this->table, ['rfid_uid' => $rfid_uid, 'is_active' => 1])->row();
    }

    public function get_by_kelas($kelas_id) {
        $this->db->where('kelas_id', $kelas_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('nama', 'ASC');
        return $this->db->get($this->table)->result();
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

    public function count_total() {
        $this->db->where('is_active', 1);
        return $this->db->count_all_results($this->table);
    }

    public function naik_kelas($from_kelas_id, $to_kelas_id) {
        return $this->db->update($this->table, 
            ['kelas_id' => $to_kelas_id], 
            ['kelas_id' => $from_kelas_id, 'is_active' => 1]
        );
    }
}
