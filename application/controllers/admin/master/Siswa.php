<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        
        $this->load->model('Siswa_model');
        $this->load->model('Kelas_model');
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
        
        $data['siswa'] = $this->Siswa_model->get_all($search, $per_page, $offset);
        $data['total'] = $this->Siswa_model->count_all($search);
        
        // Pagination
        $config['base_url'] = base_url('admin/master/siswa');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        $data['title'] = 'Data Siswa';
        $data['user'] = $this->session->userdata();
        $data['kelas_list'] = $this->Kelas_model->get_active();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/master/siswa/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->form_validation->set_rules('nis', 'NIS', 'required|is_unique[siswa.nis]');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nis' => $this->input->post('nis'),
            'nisn' => $this->input->post('nisn'),
            'nama' => $this->input->post('nama'),
            'kelas_id' => $this->input->post('kelas_id'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir'),
            'alamat' => $this->input->post('alamat'),
            'nama_ortu' => $this->input->post('nama_ortu'),
            'no_hp_ortu' => $this->input->post('no_hp_ortu'),
            'rfid_uid' => $this->input->post('rfid_uid')
        ];
        
        if ($this->Siswa_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data siswa berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data siswa']);
        }
    }

    public function update($id) {
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'nisn' => $this->input->post('nisn'),
            'nama' => $this->input->post('nama'),
            'kelas_id' => $this->input->post('kelas_id'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'tempat_lahir' => $this->input->post('tempat_lahir'),
            'tanggal_lahir' => $this->input->post('tanggal_lahir'),
            'alamat' => $this->input->post('alamat'),
            'nama_ortu' => $this->input->post('nama_ortu'),
            'no_hp_ortu' => $this->input->post('no_hp_ortu'),
            'rfid_uid' => $this->input->post('rfid_uid')
        ];
        
        if ($this->Siswa_model->update($id, $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Data siswa berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data siswa']);
        }
    }

    public function delete($id) {
        if ($this->Siswa_model->delete($id)) {
            $this->session->set_flashdata('success', 'Data siswa berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data siswa');
        }
        
        redirect('admin/master/siswa');
    }

    public function get_by_id($id) {
        $siswa = $this->Siswa_model->get_by_id($id);
        if ($siswa) {
            echo json_encode(['status' => 'success', 'data' => $siswa]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }
    }
}
