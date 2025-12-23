<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {

    private $table = 'settings';

    public function get_settings() {
        return $this->db->get_where($this->table, ['id' => 1])->row();
    }

    public function update_settings($data) {
        return $this->db->update($this->table, $data, ['id' => 1]);
    }
}
