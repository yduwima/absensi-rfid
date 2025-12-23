<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sakit_izin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['wali_kelas', 'admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Absensi_harian_model');
        $this->load->model('Siswa_model');
        $this->load->model('Guru_model');
    }

    public function index()
    {
        // Get wali kelas's class
        $guru = $this->Guru_model->get($this->session->userdata('user_id'));
        $data['siswa'] = $this->Siswa_model->get_by_kelas($guru->wali_kelas_id);
        $data['today'] = date('Y-m-d');
        
        $this->load->view('templates/guru_header', ['title' => 'Input Sakit/Izin']);
        $this->load->view('walikelas/sakit_izin', $data);
        $this->load->view('templates/guru_footer');
    }

    public function submit()
    {
        $this->form_validation->set_rules('siswa_id', 'Siswa', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'user_type' => 'siswa',
            'user_id' => $this->input->post('siswa_id'),
            'tanggal' => $this->input->post('tanggal'),
            'jam_masuk' => null,
            'jam_pulang' => null,
            'keterlambatan_menit' => 0,
            'keterangan' => $this->input->post('keterangan') // 'Sakit' or 'Izin'
        ];
        
        if ($this->Absensi_harian_model->insert($data)) {
            $this->session->set_flashdata('success', 'Data sakit/izin berhasil dicatat');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan data']);
        }
    }
}
