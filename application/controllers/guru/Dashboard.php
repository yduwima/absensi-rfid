<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Guru']);
        
        $this->load->model('Jadwal_model');
        $this->load->model('Jurnal_model');
        $this->load->model('Absensi_harian_model');
    }

    private function _check_login() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    private function _check_role($allowed_roles) {
        $user_role = $this->session->userdata('role');
        $has_access = false;
        
        foreach ($allowed_roles as $role) {
            if (strpos($user_role, $role) !== false) {
                $has_access = true;
                break;
            }
        }
        
        if (!$has_access) {
            show_error('Access Denied', 403);
        }
    }

    public function index() {
        $guru_id = $this->session->userdata('user_id');
        
        // Get today's schedule
        $data['jadwal_hari_ini'] = $this->Jadwal_model->get_today_by_guru($guru_id);
        
        // Get journals this month
        $data['jurnal_bulan_ini'] = $this->Jurnal_model->count_by_guru_and_month(
            $guru_id, 
            date('m'), 
            date('Y')
        );
        
        // Get my attendance this month
        $this->db->where('user_type', 'guru');
        $this->db->where('user_id', $guru_id);
        $this->db->where('MONTH(tanggal)', date('m'));
        $this->db->where('YEAR(tanggal)', date('Y'));
        $data['absensi_saya'] = $this->db->count_all_results('absensi_harian');
        
        $data['title'] = 'Dashboard Guru';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/guru_header', $data);
        $this->load->view('guru/dashboard', $data);
        $this->load->view('templates/guru_footer');
    }
}
