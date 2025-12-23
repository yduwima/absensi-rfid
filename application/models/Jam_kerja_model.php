<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jam_kerja_model extends CI_Model {

    private $table = 'jam_kerja';

    public function get_all() {
        return $this->db->get($this->table)->result();
    }

    public function get_by_hari($hari) {
        return $this->db->get_where($this->table, ['hari' => $hari])->row();
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }
}
