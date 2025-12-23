<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['BK']);
        
        $this->load->model('Monitoring_bk_model');
        $this->load->model('Surat_bk_model');
        $this->load->model('Siswa_model');
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
        $current_month = date('m');
        $current_year = date('Y');
        
        // Get students needing attention
        $data['siswa_perlu_perhatian'] = $this->Monitoring_bk_model->get_siswa_need_attention($current_month, $current_year);
        
        // Count statistics
        $data['total_monitoring'] = $this->Monitoring_bk_model->count_all('');
        $data['perlu_perhatian'] = $this->Monitoring_bk_model->count_all('', 'Perlu Perhatian');
        $data['panggilan'] = $this->Monitoring_bk_model->count_all('', 'Panggilan');
        
        // Count letters this month
        $this->db->where('MONTH(tanggal_surat)', $current_month);
        $this->db->where('YEAR(tanggal_surat)', $current_year);
        $data['surat_bulan_ini'] = $this->db->count_all_results('surat_bk');
        
        $data['title'] = 'Dashboard BK';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/bk_header', $data);
        $this->load->view('bk/dashboard', $data);
        $this->load->view('templates/bk_footer');
    }

    public function generate_monitoring() {
        $month = $this->input->post('month') ?? date('m');
        $year = $this->input->post('year') ?? date('Y');
        
        if ($this->Monitoring_bk_model->auto_generate_monitoring($month, $year)) {
            echo json_encode(['status' => 'success', 'message' => 'Monitoring berhasil di-generate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal generate monitoring']);
        }
    }
}
