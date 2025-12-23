<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        
        $this->load->model('Settings_model');
        $this->load->model('Jam_kerja_model');
        $this->load->model('Hari_libur_model');
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
        $data['settings'] = $this->Settings_model->get_all();
        $data['title'] = 'Pengaturan Sekolah';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/settings/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update() {
        $settings = [
            'nama_sekolah' => $this->input->post('nama_sekolah'),
            'alamat_sekolah' => $this->input->post('alamat_sekolah'),
            'telp_sekolah' => $this->input->post('telp_sekolah'),
            'email_sekolah' => $this->input->post('email_sekolah'),
            'nama_kepsek' => $this->input->post('nama_kepsek'),
            'nip_kepsek' => $this->input->post('nip_kepsek')
        ];
        
        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path'] = './assets/uploads/logo/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 2048;
            $config['file_name'] = 'logo_sekolah';
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();
                $settings['logo_sekolah'] = $upload_data['file_name'];
            }
        }
        
        foreach ($settings as $key => $value) {
            $this->Settings_model->update_by_key($key, $value);
        }
        
        $this->session->set_flashdata('success', 'Pengaturan berhasil diupdate');
        redirect('admin/settings');
    }

    public function jam_kerja() {
        $data['jam_kerja'] = $this->Jam_kerja_model->get_all();
        $data['title'] = 'Pengaturan Jam Kerja';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/settings/jam_kerja', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update_jam_kerja() {
        $hari = $this->input->post('hari');
        $jam_masuk = $this->input->post('jam_masuk');
        $jam_pulang = $this->input->post('jam_pulang');
        $is_libur = $this->input->post('is_libur');
        
        foreach ($hari as $idx => $h) {
            $existing = $this->Jam_kerja_model->get_by_hari($h);
            
            $data = [
                'jam_masuk' => $jam_masuk[$idx],
                'jam_pulang' => $jam_pulang[$idx],
                'is_libur' => isset($is_libur[$idx]) ? 1 : 0
            ];
            
            if ($existing) {
                $this->Jam_kerja_model->update($existing->id, $data);
            } else {
                $data['hari'] = $h;
                $this->Jam_kerja_model->insert($data);
            }
        }
        
        $this->session->set_flashdata('success', 'Jam kerja berhasil diupdate');
        redirect('admin/settings/jam_kerja');
    }

    public function hari_libur() {
        $search = $this->input->get('search');
        $per_page = $this->input->get('per_page') ?? 20;
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $per_page;
        
        $data['hari_libur'] = $this->Hari_libur_model->get_all($search, $per_page, $offset);
        $data['total'] = $this->Hari_libur_model->count_all($search);
        $data['title'] = 'Hari Libur Nasional';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/settings/hari_libur', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store_hari_libur() {
        $data = [
            'tanggal' => $this->input->post('tanggal'),
            'keterangan' => $this->input->post('keterangan')
        ];
        
        if ($this->Hari_libur_model->insert($data)) {
            echo json_encode(['status' => 'success', 'message' => 'Hari libur berhasil ditambahkan']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan hari libur']);
        }
    }

    public function delete_hari_libur($id) {
        if ($this->Hari_libur_model->delete($id)) {
            $this->session->set_flashdata('success', 'Hari libur berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus hari libur');
        }
        
        redirect('admin/settings/hari_libur');
    }
}
