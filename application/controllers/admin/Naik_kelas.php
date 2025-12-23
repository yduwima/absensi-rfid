<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Naik_kelas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        
        if ($this->session->userdata('role') != 'Admin') {
            show_error('Access denied', 403);
        }
        
        $this->load->model('Siswa_model');
        $this->load->model('Kelas_model');
        $this->load->model('Tahun_ajaran_model');
    }

    public function index() {
        $data['title'] = 'Naik Kelas';
        
        // Get all classes
        $data['kelas_list'] = $this->Kelas_model->get_all_no_pagination();
        
        // Get active academic year
        $data['tahun_ajaran_aktif'] = $this->Tahun_ajaran_model->get_active();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/naik_kelas/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function get_siswa_by_kelas() {
        $kelas_id = $this->input->post('kelas_id');
        $siswa = $this->Siswa_model->get_by_kelas($kelas_id);
        
        echo json_encode($siswa);
    }

    public function proses_naik_kelas() {
        $kelas_asal_id = $this->input->post('kelas_asal_id');
        $kelas_tujuan_id = $this->input->post('kelas_tujuan_id');
        $siswa_ids = $this->input->post('siswa_ids');
        
        if (empty($siswa_ids)) {
            echo json_encode(['success' => false, 'message' => 'Pilih minimal satu siswa']);
            return;
        }
        
        $success_count = 0;
        foreach ($siswa_ids as $siswa_id) {
            $data = array('kelas_id' => $kelas_tujuan_id);
            if ($this->Siswa_model->update($siswa_id, $data)) {
                $success_count++;
            }
        }
        
        if ($success_count > 0) {
            echo json_encode([
                'success' => true, 
                'message' => "$success_count siswa berhasil dinaikkan kelas"
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal menaikkan siswa'
            ]);
        }
    }
}
