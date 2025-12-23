<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        
        $this->load->model('Guru_model');
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
        
        $data['guru'] = $this->Guru_model->get_all($search, $per_page, $offset);
        $data['total'] = $this->Guru_model->count_all($search);
        
        // Pagination
        $config['base_url'] = base_url('admin/master/guru');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        $data['title'] = 'Data Guru';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/master/guru/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[guru.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nip' => $this->input->post('nip'),
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
            'no_hp' => $this->input->post('no_hp'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'alamat' => $this->input->post('alamat'),
            'role' => $this->input->post('role'),
            'rfid_uid' => $this->input->post('rfid_uid')
        ];
        
        if ($this->Guru_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data guru']);
        }
    }

    public function update($id) {
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nama' => $this->input->post('nama'),
            'no_hp' => $this->input->post('no_hp'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'alamat' => $this->input->post('alamat'),
            'role' => $this->input->post('role'),
            'rfid_uid' => $this->input->post('rfid_uid')
        ];
        
        // Only update password if provided
        if ($this->input->post('password')) {
            $data['password'] = $this->input->post('password');
        }
        
        if ($this->Guru_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data guru berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data guru']);
        }
    }

    public function delete($id) {
        if ($this->Guru_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data guru berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data guru');
        }
        
        redirect('admin/master/guru');
    }

    public function get_by_id($id) {
        $guru = $this->Guru_model->get_by_id($id);
        if ($guru) {
            unset($guru->password); // Don't send password
            echo json_encode(['status' => 'success', 'data' => $guru]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }
}
