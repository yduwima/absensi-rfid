<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_export extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Check if logged in
        if (!$this->session->userdata('guru_id')) {
            redirect('auth/login');
        }
        
        // Check if admin
        if (!has_role('admin')) {
            show_error('Anda tidak memiliki akses ke halaman ini', 403);
        }
        
        $this->load->model('Siswa_model');
        $this->load->model('Guru_model');
        $this->load->library('Excel_library');
    }
    
    public function index() {
        $data['title'] = 'Import & Export Data';
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/import_export/index', $data);
        $this->load->view('templates/admin_footer');
    }
    
    /**
     * Download template Excel untuk import siswa
     */
    public function download_template_siswa() {
        $this->excel_library->create();
        $this->excel_library->set_title('Template Siswa');
        
        // Header
        $headers = [
            ['NIS', 'NISN', 'Nama', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Nama Ortu', 'No HP Ortu', 'RFID UID']
        ];
        
        // Sample data
        $sample = [
            ['12345', '0012345678', 'Contoh Siswa', 'X IPA 1', 'L', 'Jakarta', '2008-01-15', 'Jl. Contoh No. 123', 'Bapak Contoh', '081234567890', 'RFID001']
        ];
        
        $data = array_merge($headers, $sample);
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'K');
        $this->excel_library->auto_size('A', 'K');
        $this->excel_library->download('template_siswa.xlsx');
    }
    
    /**
     * Download template Excel untuk import guru
     */
    public function download_template_guru() {
        $this->excel_library->create();
        $this->excel_library->set_title('Template Guru');
        
        // Header
        $headers = [
            ['NIP', 'Nama', 'Email', 'Password', 'No HP', 'Jenis Kelamin', 'Alamat', 'RFID UID', 'Role']
        ];
        
        // Sample data
        $sample = [
            ['1234567890', 'Contoh Guru', 'guru@contoh.com', 'password123', '081234567890', 'L', 'Jl. Guru No. 1', '1234567890', 'guru']
        ];
        
        $data = array_merge($headers, $sample);
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'I');
        $this->excel_library->auto_size('A', 'I');
        $this->excel_library->download('template_guru.xlsx');
    }
    
    /**
     * Import siswa dari Excel
     */
    public function import_siswa() {
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('import_export');
        }
        
        // Upload file
        $config['upload_path'] = './assets/uploads/temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 10240; // 10MB
        $config['encrypt_name'] = TRUE;
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('import_export');
        }
        
        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];
        
        // Import
        $result = $this->excel_library->import_siswa($file_path);
        
        // Delete temp file
        unlink($file_path);
        
        if ($result['success']) {
            $message = "Import selesai! ";
            $message .= "Berhasil: {$result['success_count']}, ";
            $message .= "Gagal: {$result['failed_count']}";
            
            if (!empty($result['errors'])) {
                $message .= "<br>Errors: " . implode(', ', array_slice($result['errors'], 0, 5));
            }
            
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }
        
        redirect('import_export');
    }
    
    /**
     * Import guru dari Excel
     */
    public function import_guru() {
        if (!$this->input->post()) {
            $this->session->set_flashdata('error', 'Invalid request');
            redirect('import_export');
        }
        
        // Upload file
        $config['upload_path'] = './assets/uploads/temp/';
        $config['allowed_types'] = 'xlsx|xls';
        $config['max_size'] = 10240;
        $config['encrypt_name'] = TRUE;
        
        $this->load->library('upload', $config);
        
        if (!$this->upload->do_upload('file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('import_export');
        }
        
        $upload_data = $this->upload->data();
        $file_path = $upload_data['full_path'];
        
        // Import
        $result = $this->excel_library->import_guru($file_path);
        
        // Delete temp file
        unlink($file_path);
        
        if ($result['success']) {
            $message = "Import selesai! ";
            $message .= "Berhasil: {$result['success_count']}, ";
            $message .= "Gagal: {$result['failed_count']}";
            
            $this->session->set_flashdata('success', $message);
        } else {
            $this->session->set_flashdata('error', $result['message']);
        }
        
        redirect('import_export');
    }
    
    /**
     * Export siswa ke Excel
     */
    public function export_siswa() {
        $siswa = $this->Siswa_model->get_all_with_kelas();
        
        $this->excel_library->create();
        $this->excel_library->set_title('Data Siswa');
        
        // Headers
        $data = [
            ['No', 'NIS', 'NISN', 'Nama', 'Kelas', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Nama Ortu', 'No HP Ortu', 'RFID UID']
        ];
        
        // Data
        $no = 1;
        foreach ($siswa as $row) {
            $data[] = [
                $no++,
                $row->nis,
                $row->nisn,
                $row->nama,
                $row->nama_kelas ?? '-',
                $row->jenis_kelamin,
                $row->tempat_lahir,
                $row->tanggal_lahir,
                $row->alamat,
                $row->nama_ortu,
                $row->no_hp_ortu,
                $row->rfid_uid
            ];
        }
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'L');
        $this->excel_library->auto_size('A', 'L');
        $this->excel_library->download('data_siswa_' . date('Y-m-d') . '.xlsx');
    }
    
    /**
     * Export guru ke Excel
     */
    public function export_guru() {
        $guru = $this->Guru_model->get_all();
        
        $this->excel_library->create();
        $this->excel_library->set_title('Data Guru');
        
        // Headers
        $data = [
            ['No', 'NIP', 'Nama', 'Email', 'No HP', 'Jenis Kelamin', 'Alamat', 'RFID UID', 'Role']
        ];
        
        // Data
        $no = 1;
        foreach ($guru as $row) {
            $data[] = [
                $no++,
                $row->nip,
                $row->nama,
                $row->email,
                $row->no_hp,
                $row->jenis_kelamin,
                $row->alamat,
                $row->rfid_uid,
                $row->role
            ];
        }
        
        $this->excel_library->write_data($data);
        $this->excel_library->style_header(1, 'A', 'I');
        $this->excel_library->auto_size('A', 'I');
        $this->excel_library->download('data_guru_' . date('Y-m-d') . '.xlsx');
    }
}
