<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_queue extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Wa_sender');
    }

    /**
     * Process WhatsApp queue
     * This endpoint should be called via cron job
     * Example: */5 * * * * curl http://localhost/absensi-rfid/api/wa/process_queue
     */
    public function process() {
        $limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
        
        $results = $this->wa_sender->process_queue($limit);
        
        echo json_encode([
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'results' => $results
        ]);
    }
}
