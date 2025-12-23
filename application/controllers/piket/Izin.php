<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Izin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['piket', 'admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Izin_siswa_model');
        $this->load->model('Siswa_model');
    }

    public function index()
    {
        $data['izin'] = $this->Izin_siswa_model->get_all(20, 0);
        $data['siswa'] = $this->Siswa_model->get_all();
        
        $this->load->view('templates/admin_header', ['title' => 'Izin Siswa']);
        $this->load->view('piket/izin/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create()
    {
        $this->form_validation->set_rules('siswa_id', 'Siswa', 'required');
        $this->form_validation->set_rules('jenis_izin', 'Jenis Izin', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('waktu', 'Waktu', 'required');
        $this->form_validation->set_rules('alasan', 'Alasan', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'siswa_id' => $this->input->post('siswa_id'),
            'jenis_izin' => $this->input->post('jenis_izin'),
            'tanggal' => $this->input->post('tanggal'),
            'waktu' => $this->input->post('waktu'),
            'alasan' => $this->input->post('alasan'),
            'guru_id' => $this->session->userdata('user_id')
        ];
        
        if ($this->Izin_siswa_model->insert($data)) {
            $this->session->set_flashdata('success', 'Izin berhasil dicatat');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }

    public function rekap()
    {
        $bulan = $this->input->get('bulan') ?: date('Y-m');
        $data['rekap'] = $this->Izin_siswa_model->get_rekap($bulan);
        $data['bulan'] = $bulan;
        
        $this->load->view('templates/admin_header', ['title' => 'Rekap Izin']);
        $this->load->view('piket/izin/rekap', $data);
        $this->load->view('templates/admin_footer');
    }
}
