<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jurnal extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Guru']);
        
        $this->load->model('Jadwal_model');
        $this->load->model('Jurnal_model');
        $this->load->model('Absensi_mapel_model');
        $this->load->model('Siswa_model');
        $this->load->model('Kelas_model');
    }

    private function _check_login() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    private function _check_role($allowed_roles) {
        $user_role = $this->session->userdata('role');
        $has_access = false;
        
        foreach ($allowed_roles as $role) {
            if (strpos($user_role, $role) !== false) {
                $has_access = true;
                break;
            }
        }
        
        if (!$has_access) {
            show_error('Access Denied', 403);
        }
    }

    public function index() {
        $guru_id = $this->session->userdata('user_id');
        $search = $this->input->get('search');
        $per_page = $this->input->get('per_page') ?? 10;
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $per_page;
        
        $data['jurnal'] = $this->Jurnal_model->get_all($search, $per_page, $offset, $guru_id);
        $data['total'] = $this->Jurnal_model->count_all($search, $guru_id);
        
        $data['title'] = 'Jurnal Mengajar';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/guru_header', $data);
        $this->load->view('guru/jurnal/index', $data);
        $this->load->view('templates/guru_footer');
    }

    public function create($jadwal_id) {
        $jadwal = $this->Jadwal_model->get_by_id($jadwal_id);
        
        if (!$jadwal || $jadwal->guru_id != $this->session->userdata('user_id')) {
            show_404();
        }
        
        $data['jadwal'] = $jadwal;
        $data['title'] = 'Isi Jurnal Mengajar';
        $data['user'] = $this->session->userdata();
        
        // Get students in this class
        $data['siswa'] = $this->Siswa_model->get_by_kelas($jadwal->kelas_id);
        
        $this->load->view('templates/guru_header', $data);
        $this->load->view('guru/jurnal/create', $data);
        $this->load->view('templates/guru_footer');
    }

    public function store() {
        $this->form_validation->set_rules('jadwal_id', 'Jadwal', 'required');
        $this->form_validation->set_rules('tanggal', 'Tanggal', 'required');
        $this->form_validation->set_rules('materi', 'Materi', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $jadwal_id = $this->input->post('jadwal_id');
        $tanggal = $this->input->post('tanggal');
        
        // Check if journal already exists
        $existing = $this->Jurnal_model->get_by_jadwal_and_date($jadwal_id, $tanggal);
        if ($existing) {
            echo json_encode(['status' => 'error', 'message' => 'Jurnal untuk jadwal ini di tanggal tersebut sudah ada']);
            return;
        }
        
        $jurnal_data = [
            'jadwal_id' => $jadwal_id,
            'guru_id' => $this->session->userdata('user_id'),
            'tanggal' => $tanggal,
            'materi' => $this->input->post('materi'),
            'kegiatan' => $this->input->post('kegiatan'),
            'catatan' => $this->input->post('catatan')
        ];
        
        if ($this->Jurnal_model->insert($jurnal_data)) {
            $jurnal_id = $this->db->insert_id();
            
            // Insert attendance records
            $absensi_data = [];
            $siswa_ids = $this->input->post('siswa_id');
            $statuses = $this->input->post('status');
            
            if ($siswa_ids && $statuses) {
                foreach ($siswa_ids as $idx => $siswa_id) {
                    $absensi_data[] = [
                        'jurnal_id' => $jurnal_id,
                        'siswa_id' => $siswa_id,
                        'status' => $statuses[$idx]
                    ];
                }
                
                if (!empty($absensi_data)) {
                    $this->Absensi_mapel_model->insert_batch($absensi_data);
                }
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Jurnal berhasil disimpan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan jurnal']);
        }
    }

    public function edit($id) {
        $jurnal = $this->Jurnal_model->get_by_id($id);
        
        if (!$jurnal || $jurnal->guru_id != $this->session->userdata('user_id')) {
            show_404();
        }
        
        $data['jurnal'] = $jurnal;
        $data['title'] = 'Edit Jurnal Mengajar';
        $data['user'] = $this->session->userdata();
        
        // Get students and their attendance
        $data['siswa'] = $this->Siswa_model->get_by_kelas($jurnal->kelas_id);
        $data['absensi'] = $this->Absensi_mapel_model->get_by_jurnal($id);
        
        $this->load->view('templates/guru_header', $data);
        $this->load->view('guru/jurnal/edit', $data);
        $this->load->view('templates/guru_footer');
    }

    public function update($id) {
        $this->form_validation->set_rules('materi', 'Materi', 'required');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['status' => 'error', 'message' => validation_errors()]);
            return;
        }
        
        $jurnal_data = [
            'materi' => $this->input->post('materi'),
            'kegiatan' => $this->input->post('kegiatan'),
            'catatan' => $this->input->post('catatan')
        ];
        
        if ($this->Jurnal_model->update($id, $jurnal_data)) {
            // Update attendance
            $this->Absensi_mapel_model->delete_by_jurnal($id);
            
            $absensi_data = [];
            $siswa_ids = $this->input->post('siswa_id');
            $statuses = $this->input->post('status');
            
            if ($siswa_ids && $statuses) {
                foreach ($siswa_ids as $idx => $siswa_id) {
                    $absensi_data[] = [
                        'jurnal_id' => $id,
                        'siswa_id' => $siswa_id,
                        'status' => $statuses[$idx]
                    ];
                }
                
                if (!empty($absensi_data)) {
                    $this->Absensi_mapel_model->insert_batch($absensi_data);
                }
            }
            
            echo json_encode(['status' => 'success', 'message' => 'Jurnal berhasil diupdate']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate jurnal']);
        }
    }

    public function delete($id) {
        $jurnal = $this->Jurnal_model->get_by_id($id);
        
        if (!$jurnal || $jurnal->guru_id != $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Jurnal tidak ditemukan');
            redirect('guru/jurnal');
        }
        
        // Delete attendance first
        $this->Absensi_mapel_model->delete_by_jurnal($id);
        
        if ($this->Jurnal_model->delete($id)) {
            $this->session->set_flashdata('success', 'Jurnal berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus jurnal');
        }
        
        redirect('guru/jurnal');
    }
}
