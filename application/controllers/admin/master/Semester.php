<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Semester extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Semester_model');
        $this->load->model('Tahun_ajaran_model');
    }

    public function index()
    {
        $config['base_url'] = base_url('admin/master/semester/index');
        $config['total_rows'] = $this->Semester_model->count_all();
        $config['per_page'] = $this->input->get('per_page') ?: 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        $this->pagination->initialize($config);
        
        $page = $this->input->get('page') ?: 0;
        $search = $this->input->get('search');
        
        $data['semester'] = $this->Semester_model->get_all($config['per_page'], $page, $search);
        $data['tahun_ajaran'] = $this->Tahun_ajaran_model->get_all();
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        
        $this->load->view('templates/admin_header', ['title' => 'Data Semester']);
        $this->load->view('admin/master/semester/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('tahun_ajaran_id', 'Tahun Ajaran', 'required');
        $this->form_validation->set_rules('nama_semester', 'Nama Semester', 'required');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $is_active = $this->input->post('is_active') ? 1 : 0;
        
        // If setting as active, deactivate others
        if ($is_active) {
            $this->Semester_model->deactivate_all();
        }
        
        $data = [
            'tahun_ajaran_id' => $this->input->post('tahun_ajaran_id'),
            'nama_semester' => $this->input->post('nama_semester'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'is_active' => $is_active
        ];
        
        if ($this->Semester_model->insert($data)) {
            $this->session->set_flashdata('success', 'Semester berhasil ditambahkan');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }

    public function edit($id)
    {
        $this->form_validation->set_rules('tahun_ajaran_id', 'Tahun Ajaran', 'required');
        $this->form_validation->set_rules('nama_semester', 'Nama Semester', 'required');
        $this->form_validation->set_rules('tanggal_mulai', 'Tanggal Mulai', 'required');
        $this->form_validation->set_rules('tanggal_selesai', 'Tanggal Selesai', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $is_active = $this->input->post('is_active') ? 1 : 0;
        
        // If setting as active, deactivate others
        if ($is_active) {
            $this->Semester_model->deactivate_all();
        }
        
        $data = [
            'tahun_ajaran_id' => $this->input->post('tahun_ajaran_id'),
            'nama_semester' => $this->input->post('nama_semester'),
            'tanggal_mulai' => $this->input->post('tanggal_mulai'),
            'tanggal_selesai' => $this->input->post('tanggal_selesai'),
            'is_active' => $is_active
        ];
        
        if ($this->Semester_model->update($id, $data)) {
            $this->session->set_flashdata('success', 'Semester berhasil diupdate');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data']);
        }
    }

    public function delete($id)
    {
        if ($this->Semester_model->delete($id)) {
            $this->session->set_flashdata('success', 'Semester berhasil dihapus');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function get($id)
    {
        $data = $this->Semester_model->get($id);
        echo json_encode($data);
    }
}
