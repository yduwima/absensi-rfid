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
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        $kelas_id = $this->input->get('kelas_id');
        
        $this->load->library('Excel_library');
        
        $absensi = $this->Absensi_harian_model->get_laporan_siswa($bulan, $tahun, $kelas_id);
        
        $this->excel_library->create();
        $this->excel_library->set_title('Laporan Absensi Siswa');
        
        // Header
        $data = [
            ['No', 'NIS', 'Nama', 'Kelas', 'Jam Masuk', 'Jam Pulang', 'Terlambat (menit)', 'Tanggal']
        ];
        
        // Data
        $no = 1;
        foreach ($absensi as $row) {
            $data[] = [
                $no++,
                $row->nis,
                $row->nama_siswa,
                $row->nama_kelas,
                $row->jam_masuk ?? '-',
                $row->jam_pulang ?? '-',
                $row->keterlambatan_menit > 0 ? $row->keterlambatan_menit : '-',
                $row->tanggal
            ];
        }
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'H');
        $this->excel_library->auto_size('A', 'H');
        $this->excel_library->download('laporan_absensi_siswa_' . $bulan . '_' . $tahun . '.xlsx');
    }

    public function export_pdf_siswa() {
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        $kelas_id = $this->input->get('kelas_id');
        
        $this->load->library('Pdf_library');
        
        $absensi = $this->Absensi_harian_model->get_laporan_siswa($bulan, $tahun, $kelas_id);
        
        $kelas_name = '';
        if ($kelas_id) {
            $kelas = $this->Kelas_model->get($kelas_id);
            $kelas_name = $kelas->nama_kelas ?? '';
        }
        
        $filters = [
            'bulan' => date('F Y', strtotime($tahun . '-' . $bulan . '-01')),
            'tahun' => $tahun,
            'kelas' => $kelas_name
        ];
        
        $this->pdf_library->laporan_absensi_siswa($absensi, $filters);
        $this->pdf_library->stream('laporan_absensi_siswa_' . $bulan . '_' . $tahun . '.pdf');
    }
    
    public function export_excel_guru() {
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $this->load->library('Excel_library');
        
        $absensi = $this->Absensi_harian_model->get_laporan_guru($bulan, $tahun);
        
        $this->excel_library->create();
        $this->excel_library->set_title('Laporan Absensi Guru');
        
        // Header
        $data = [
            ['No', 'NIP', 'Nama', 'Jam Masuk', 'Jam Pulang', 'Terlambat (menit)', 'Status', 'Tanggal']
        ];
        
        // Data
        $no = 1;
        foreach ($absensi as $row) {
            $data[] = [
                $no++,
                $row->nip,
                $row->nama_guru,
                $row->jam_masuk ?? '-',
                $row->jam_pulang ?? '-',
                $row->keterlambatan_menit > 0 ? $row->keterlambatan_menit : '-',
                $row->jam_masuk ? 'Hadir' : 'Alpha',
                $row->tanggal
            ];
        }
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'H');
        $this->excel_library->auto_size('A', 'H');
        $this->excel_library->download('laporan_absensi_guru_' . $bulan . '_' . $tahun . '.xlsx');
    }
    
    public function export_pdf_guru() {
        $bulan = $this->input->get('bulan') ?: date('m');
        $tahun = $this->input->get('tahun') ?: date('Y');
        
        $this->load->library('Pdf_library');
        
        $absensi = $this->Absensi_harian_model->get_laporan_guru($bulan, $tahun);
        
        $filters = [
            'bulan' => date('F Y', strtotime($tahun . '-' . $bulan . '-01')),
            'tahun' => $tahun
        ];
        
        $this->pdf_library->laporan_absensi_guru($absensi, $filters);
        $this->pdf_library->stream('laporan_absensi_guru_' . $bulan . '_' . $tahun . '.pdf');
    }
}
