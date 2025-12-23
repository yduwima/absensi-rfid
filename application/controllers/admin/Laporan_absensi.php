<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_absensi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        
        if ($this->session->userdata('role') != 'Admin') {
            show_error('Access denied', 403);
        }
        
        $this->load->model('Absensi_harian_model');
        $this->load->model('Siswa_model');
        $this->load->model('Guru_model');
        $this->load->model('Kelas_model');
        $this->load->helper('app');
    }

    public function siswa() {
        $data['title'] = 'Laporan Absensi Siswa';
        
        // Get filter parameters
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        $kelas_id = $this->input->get('kelas_id');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['kelas_id'] = $kelas_id;
        
        // Get all classes for filter
        $data['kelas_list'] = $this->Kelas_model->get_all_no_pagination();
        
        // Get attendance data
        $data['absensi'] = $this->Absensi_harian_model->get_laporan_siswa($bulan, $tahun, $kelas_id);
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/laporan/siswa', $data);
        $this->load->view('templates/admin_footer');
    }

    public function guru() {
        $data['title'] = 'Laporan Absensi Guru';
        
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        
        $data['absensi'] = $this->Absensi_harian_model->get_laporan_guru($bulan, $tahun);
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/laporan/guru', $data);
        $this->load->view('templates/admin_footer');
    }

    public function rekap_siswa() {
        $data['title'] = 'Rekap Absensi Siswa';
        
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $siswa_id = $this->input->get('siswa_id');
        $kelas_id = $this->input->get('kelas_id');
        
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['siswa_id'] = $siswa_id;
        $data['kelas_id'] = $kelas_id;
        
        $data['kelas_list'] = $this->Kelas_model->get_all_no_pagination();
        
        if ($kelas_id) {
            $data['siswa_list'] = $this->Siswa_model->get_by_kelas($kelas_id);
        }
        
        if ($bulan && $tahun && $siswa_id) {
            $data['rekap'] = $this->Absensi_harian_model->get_rekap_siswa($siswa_id, $bulan, $tahun);
        }
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/laporan/rekap_siswa', $data);
        $this->load->view('templates/admin_footer');
    }

    public function export_excel_siswa() {
        // Placeholder for Excel export
        // Requires PhpSpreadsheet library
        $this->session->set_flashdata('info', 'Fitur export Excel akan segera tersedia');
        redirect('admin/laporan_absensi/siswa');
    }

    public function export_pdf_siswa() {
        // Placeholder for PDF export
        // Requires TCPDF library
        $this->session->set_flashdata('info', 'Fitur export PDF akan segera tersedia');
        redirect('admin/laporan_absensi/siswa');
    }
}
