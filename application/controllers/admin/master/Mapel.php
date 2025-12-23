<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapel extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Mapel_model');
    }

    public function index()
    {
        $config['base_url'] = base_url('admin/master/mapel/index');
        $config['total_rows'] = $this->Mapel_model->count_all();
        $config['per_page'] = $this->input->get('per_page') ?: 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        $this->pagination->initialize($config);
        
        $page = $this->input->get('page') ?: 0;
        $search = $this->input->get('search');
        
        $data['mapel'] = $this->Mapel_model->get_all($config['per_page'], $page, $search);
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        
        $this->load->view('templates/admin_header', ['title' => 'Data Mata Pelajaran']);
        $this->load->view('admin/master/mapel/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'required|is_unique[mapel.kode_mapel]');
        $this->form_validation->set_rules('nama_mapel', 'Nama Mapel', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'kode_mapel' => $this->input->post('kode_mapel'),
            'nama_mapel' => $this->input->post('nama_mapel')
        ];
        
        if ($this->Mapel_model->insert($data)) {
            $this->session->set_flashdata('success', 'Mata pelajaran berhasil ditambahkan');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }

    public function edit($id)
    {
        $this->form_validation->set_rules('kode_mapel', 'Kode Mapel', 'required');
        $this->form_validation->set_rules('nama_mapel', 'Nama Mapel', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'kode_mapel' => $this->input->post('kode_mapel'),
            'nama_mapel' => $this->input->post('nama_mapel')
        ];
        
        if ($this->Mapel_model->update($id, $data)) {
            $this->session->set_flashdata('success', 'Mata pelajaran berhasil diupdate');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data']);
        }
    }

    public function delete($id)
    {
        if ($this->Mapel_model->delete($id)) {
            $this->session->set_flashdata('success', 'Mata pelajaran berhasil dihapus');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function get($id)
    {
        $data = $this->Mapel_model->get($id);
        echo json_encode($data);
    }
}
