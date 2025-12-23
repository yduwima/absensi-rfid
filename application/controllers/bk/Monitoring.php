<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
        if (!in_array($this->session->userdata('role'), ['bk', 'admin'])) {
            show_error('Akses ditolak', 403);
        }
        $this->load->model('Monitoring_bk_model');
        $this->load->model('Surat_bk_model');
    }

    public function index()
    {
        $bulan = $this->input->get('bulan') ?: date('Y-m');
        $kelas_id = $this->input->get('kelas_id');
        
        $data['monitoring'] = $this->Monitoring_bk_model->get_all(50, 0, null, $bulan, $kelas_id);
        $data['bulan'] = $bulan;
        
        $this->load->view('templates/bk_header', ['title' => 'Monitoring Siswa']);
        $this->load->view('bk/monitoring/index', $data);
        $this->load->view('templates/bk_footer');
    }

    public function generate()
    {
        $bulan = $this->input->post('bulan') ?: date('Y-m');
        
        if ($this->Monitoring_bk_model->auto_generate($bulan)) {
            $this->session->set_flashdata('success', 'Monitoring berhasil di-generate');
        } else {
            $this->session->set_flashdata('error', 'Gagal generate monitoring');
        }
        
        redirect('bk/monitoring');
    }

    public function create_surat($monitoring_id)
    {
        $monitoring = $this->Monitoring_bk_model->get($monitoring_id);
        $data['monitoring'] = $monitoring;
        
        $this->load->view('templates/bk_header', ['title' => 'Buat Surat']);
        $this->load->view('bk/monitoring/create_surat', $data);
        $this->load->view('templates/bk_footer');
    }

    public function submit_surat()
    {
        $this->form_validation->set_rules('monitoring_id', 'Monitoring', 'required');
        $this->form_validation->set_rules('jenis_surat', 'Jenis Surat', 'required');
        $this->form_validation->set_rules('tanggal_surat', 'Tanggal Surat', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $data = [
            'monitoring_id' => $this->input->post('monitoring_id'),
            'jenis_surat' => $this->input->post('jenis_surat'),
            'nomor_surat' => $this->Surat_bk_model->generate_nomor(),
            'tanggal_surat' => $this->input->post('tanggal_surat'),
            'isi_surat' => $this->input->post('isi_surat'),
            'guru_id' => $this->session->userdata('user_id')
        ];
        
        if ($this->Surat_bk_model->insert($data)) {
            // Update monitoring status
            $this->Monitoring_bk_model->update($this->input->post('monitoring_id'), [
                'status' => 'Surat Terbit'
            ]);
            
            $this->session->set_flashdata('success', 'Surat berhasil dibuat');
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal membuat surat']);
        }
    }
}
