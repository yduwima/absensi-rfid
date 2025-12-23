<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        
        $this->load->model('Siswa_model');
        $this->load->model('Guru_model');
        $this->load->model('Absensi_harian_model');
        $this->load->model('Settings_model');
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
        $data['title'] = 'Dashboard Admin';
        $data['user'] = $this->session->userdata();
        $data['settings'] = $this->Settings_model->get_settings();
        
        // Statistics
        $data['total_siswa'] = $this->Siswa_model->count_total();
        $data['total_guru'] = $this->Guru_model->count_total();
        $data['absen_siswa_hari_ini'] = $this->Absensi_harian_model->count_today_siswa();
        $data['absen_guru_hari_ini'] = $this->Absensi_harian_model->count_today_guru();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('templates/admin_footer');
    }
}
