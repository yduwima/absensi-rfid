<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Jadwal_model');
        $this->load->model('Kelas_model');
        $this->load->model('Mapel_model');
        $this->load->model('Guru_model');
        $this->load->model('Semester_model');
    }

    public function index()
    {
        $config['base_url'] = base_url('admin/master/jadwal/index');
        $config['total_rows'] = $this->Jadwal_model->count_all();
        $config['per_page'] = $this->input->get('per_page') ?: 10;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        $this->pagination->initialize($config);
        
        $page = $this->input->get('page') ?: 0;
        $search = $this->input->get('search');
        $kelas_id = $this->input->get('kelas_id');
        
        $data['jadwal'] = $this->Jadwal_model->get_all($config['per_page'], $page, $search, $kelas_id);
        $data['kelas_list'] = $this->Kelas_model->get_all();
        $data['mapel_list'] = $this->Mapel_model->get_all();
        $data['guru_list'] = $this->Guru_model->get_all();
        $data['semester_list'] = $this->Semester_model->get_all();
        $data['pagination'] = $this->pagination->create_links();
        $data['total'] = $config['total_rows'];
        
        $this->load->view('templates/admin_header', ['title' => 'Jadwal Pelajaran']);
        $this->load->view('admin/master/jadwal/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
        $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required');
        $this->form_validation->set_rules('guru_id', 'Guru', 'required');
        $this->form_validation->set_rules('semester_id', 'Semester', 'required');
        $this->form_validation->set_rules('hari', 'Hari', 'required');
        $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'required');
        $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'kelas_id' => $this->input->post('kelas_id'),
            'mapel_id' => $this->input->post('mapel_id'),
            'guru_id' => $this->input->post('guru_id'),
            'semester_id' => $this->input->post('semester_id'),
            'hari' => $this->input->post('hari'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai')
        ];
        
        if ($this->Jadwal_model->insert($data)) {
            $this->session->set_flashdata('success', 'Jadwal berhasil ditambahkan');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }

    public function edit($id)
    {
        $this->form_validation->set_rules('kelas_id', 'Kelas', 'required');
        $this->form_validation->set_rules('mapel_id', 'Mata Pelajaran', 'required');
        $this->form_validation->set_rules('guru_id', 'Guru', 'required');
        $this->form_validation->set_rules('semester_id', 'Semester', 'required');
        $this->form_validation->set_rules('hari', 'Hari', 'required');
        $this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'required');
        $this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'kelas_id' => $this->input->post('kelas_id'),
            'mapel_id' => $this->input->post('mapel_id'),
            'guru_id' => $this->input->post('guru_id'),
            'semester_id' => $this->input->post('semester_id'),
            'hari' => $this->input->post('hari'),
            'jam_mulai' => $this->input->post('jam_mulai'),
            'jam_selesai' => $this->input->post('jam_selesai')
        ];
        
        if ($this->Jadwal_model->update($id, $data)) {
            $this->session->set_flashdata('success', 'Jadwal berhasil diupdate');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate data']);
        }
    }

    public function delete($id)
    {
        if ($this->Jadwal_model->delete($id)) {
            $this->session->set_flashdata('success', 'Jadwal berhasil dihapus');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data']);
        }
    }

    public function get($id)
    {
        $data = $this->Jadwal_model->get($id);
        echo json_encode($data);
    }
}
