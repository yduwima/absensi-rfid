<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_config_model extends CI_Model {

    private $config_table = 'wa_config';
    private $kelas_table = 'wa_kelas_aktif';

    public function get_config() {
        return $this->db->get_where($this->config_table, ['id' => 1])->row();
    }

    public function update_config($data) {
        return $this->db->update($this->config_table, $data, ['id' => 1]);
    }

    public function is_kelas_active($kelas_id) {
        $result = $this->db->get_where($this->kelas_table, [
            'kelas_id' => $kelas_id,
            'is_active' => 1
        ])->row();
        return $result ? true : false;
    }

    public function get_active_kelas() {
        $this->db->select('wa_kelas_aktif.*, kelas.nama_kelas');
        $this->db->from($this->kelas_table);
        $this->db->join('kelas', 'kelas.id = wa_kelas_aktif.kelas_id', 'inner');
        $this->db->where('wa_kelas_aktif.is_active', 1);
        return $this->db->get()->result();
    }

    public function set_kelas_active($kelas_id, $is_active) {
        // Check if exists
        $existing = $this->db->get_where($this->kelas_table, ['kelas_id' => $kelas_id])->row();
        
        if ($existing) {
            return $this->db->update($this->kelas_table, ['is_active' => $is_active], ['kelas_id' => $kelas_id]);
        } else {
            return $this->db->insert($this->kelas_table, ['kelas_id' => $kelas_id, 'is_active' => $is_active]);
        }
    }
}
