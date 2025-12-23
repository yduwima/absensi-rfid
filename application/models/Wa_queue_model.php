<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_queue_model extends CI_Model {

    private $table = 'wa_queue';

    public function add_to_queue($no_hp, $pesan) {
        return $this->db->insert($this->table, [
            'no_hp' => $no_hp,
            'pesan' => $pesan,
            'status' => 'pending'
        ]);
    }

    public function get_pending($limit = 10) {
        $this->db->where('status', 'pending');
        $this->db->where('retry_count <', 3);
        $this->db->order_by('created_at', 'ASC');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    public function mark_as_sent($id) {
        return $this->db->update($this->table, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
    }

    public function mark_as_failed($id, $error_message) {
        $queue = $this->db->get_where($this->table, ['id' => $id])->row();
        $retry_count = $queue ? $queue->retry_count + 1 : 1;
        
        return $this->db->update($this->table, [
            'status' => 'failed',
            'error_message' => $error_message,
            'retry_count' => $retry_count
        ], ['id' => $id]);
    }
}
