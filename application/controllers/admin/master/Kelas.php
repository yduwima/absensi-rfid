<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        
        $this->load->model('Kelas_model');
        $this->load->model('Guru_model');
        $this->load->model('Tahun_ajaran_model');
        $this->load->library('pagination');
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
        $search = $this->input->get('search');
        $per_page = $this->input->get('per_page') ?? 10;
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $per_page;
        
        $data['kelas'] = $this->Kelas_model->get_all($search, $per_page, $offset);
        $data['total'] = $this->Kelas_model->count_all($search);
        
        // Pagination
        $config['base_url'] = base_url('admin/master/kelas');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        $data['title'] = 'Data Kelas';
        $data['user'] = $this->session->userdata();
        $data['guru_list'] = $this->Guru_model->get_all('', 100, 0);
        $data['tahun_ajaran_list'] = $this->Tahun_ajaran_model->get_all('', 100, 0);
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/master/kelas/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'required');
        $this->form_validation->set_rules('tingkat', 'Tingkat', 'required|integer');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nama_kelas' => $this->input->post('nama_kelas'),
            'tingkat' => $this->input->post('tingkat'),
            'wali_kelas_id' => $this->input->post('wali_kelas_id') ?: NULL,
            'tahun_ajaran_id' => $this->input->post('tahun_ajaran_id')
        ];
        
        if ($this->Kelas_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data kelas berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data kelas']);
        }
    }

    public function update($id) {
        $this->form_validation->set_rules('nama_kelas', 'Nama Kelas', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nama_kelas' => $this->input->post('nama_kelas'),
            'tingkat' => $this->input->post('tingkat'),
            'wali_kelas_id' => $this->input->post('wali_kelas_id') ?: NULL,
            'tahun_ajaran_id' => $this->input->post('tahun_ajaran_id')
        ];
        
        if ($this->Kelas_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data kelas berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data kelas']);
        }
    }

    public function delete($id) {
        if ($this->Kelas_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data kelas berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data kelas');
        }
        
        redirect('admin/master/kelas');
    }

    public function get_by_id($id) {
        $kelas = $this->Kelas_model->get_by_id($id);
        if ($kelas) {
            echo json_encode(['status' => 'success', 'data' => $kelas]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }
}
