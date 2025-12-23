<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahun_ajaran extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Tahun_ajaran_model');
    }

    public function index()
    {
        $config['base_url'] = base_url('admin/master/tahun_ajaran/index');
        $config['total_rows'] = $this->Tahun_ajaran_model->count_all();
        $config['per_page'] = $this->input->get('per_page') ?: 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        $this->pagination->initialize($config);
        
        $page = $this->input->get('page') ?: 0;
        $search = $this->input->get('search');
        
        $data['tahun_ajaran'] = $this->Tahun_ajaran_model->get_all($config['per_page'], $page, $search);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        
        $this->load->view('templates/admin_header', ['title' => 'Data Tahun Ajaran']);
        $this->load->view('admin/master/tahun_ajaran/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('nama_tahun_ajaran', 'Nama Tahun Ajaran', 'required');
        $this->form_validation->set_rules('tahun_mulai', 'Tahun Mulai', 'required|integer');
        $this->form_validation->set_rules('tahun_selesai', 'Tahun Selesai', 'required|integer');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $is_active = $this->input->post('is_active') ? 1 : 0;
        
        // If setting as active, deactivate others
        if ($is_active) {
            $this->Tahun_ajaran_model->deactivate_all();
        }
        
        $data = [
            'nama_tahun_ajaran' => $this->input->post('nama_tahun_ajaran'),
            'tahun_mulai' => $this->input->post('tahun_mulai'),
            'tahun_selesai' => $this->input->post('tahun_selesai'),
            'is_active' => $is_active
        ];
        
        if ($this->Tahun_ajaran_model->insert($data)) {
            $this->session->set_flashdata('success', 'Tahun ajaran berhasil ditambahkan');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }

    public function edit($id)
    {
        $this->form_validation->set_rules('nama_tahun_ajaran', 'Nama Tahun Ajaran', 'required');
        $this->form_validation->set_rules('tahun_mulai', 'Tahun Mulai', 'required|integer');
        $this->form_validation->set_rules('tahun_selesai', 'Tahun Selesai', 'required|integer');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $is_active = $this->input->post('is_active') ? 1 : 0;
        
        // If setting as active, deactivate others
        if ($is_active) {
            $this->Tahun_ajaran_model->deactivate_all();
        }
        
        $data = [
            'nama_tahun_ajaran' => $this->input->post('nama_tahun_ajaran'),
            'tahun_mulai' => $this->input->post('tahun_mulai'),
            'tahun_selesai' => $this->input->post('tahun_selesai'),
            'is_active' => $is_active
        ];
        
        if ($this->Tahun_ajaran_model->update($id, $data)) {
            $this->session->set_flashdata('success', 'Tahun ajaran berhasil diupdate');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data']);
        }
    }

    public function delete($id)
    {
        if ($this->Tahun_ajaran_model->delete($id)) {
            $this->session->set_flashdata('success', 'Tahun ajaran berhasil dihapus');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function get($id)
    {
        $data = $this->Tahun_ajaran_model->get($id);
        echo json_encode($data);
    }
}
