<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        
        $this->load->model('Guru_model');
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $data['user'] = $this->Guru_model->get_by_id($user_id);
        $data['title'] = 'Profile Saya';
        
        // Load appropriate template based on role
        $role = $this->session->userdata('role');
        
        if ($role == 'Admin') {
            $header = 'templates/admin_header';
            $footer = 'templates/admin_footer';
        } elseif ($role == 'BK') {
            $header = 'templates/bk_header';
            $footer = 'templates/bk_footer';
        } else {
            $header = 'templates/guru_header';
            $footer = 'templates/guru_footer';
        }
        
        $this->load->view($header, $data);
        $this->load->view('profile/index', $data);
        $this->load->view($footer);
    }

    public function update() {
        $user_id = $this->session->userdata('user_id');
        
        $data = array(
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'no_hp' => $this->input->post('no_hp'),
            'alamat' => $this->input->post('alamat')
        );
        
        // Handle photo upload
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path'] = './assets/uploads/guru/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['max_size'] = 2048;
            $config['file_name'] = 'guru_' . $user_id . '_' . time();
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('foto')) {
                $upload_data = $this->upload->data();
                $data['foto'] = $upload_data['file_name'];
                
                // Delete old photo
                $old_user = $this->Guru_model->get_by_id($user_id);
                if ($old_user->foto && file_exists('./assets/uploads/guru/' . $old_user->foto)) {
                    unlink('./assets/uploads/guru/' . $old_user->foto);
                }
            }
        }
        
        if ($this->Guru_model->update($user_id, $data)) {
            $this->session->set_flashdata('success', 'Profile berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui profile');
        }
        
        redirect('profile');
    }

    public function change_password() {
        $user_id = $this->session->userdata('user_id');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');
        
        // Verify old password
        $user = $this->Guru_model->get_by_id($user_id);
        
        if (!password_verify($old_password, $user->password)) {
            $this->session->set_flashdata('error', 'Password lama tidak sesuai');
            redirect('profile');
            return;
        }
        
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('error', 'Password baru tidak sama');
            redirect('profile');
            return;
        }
        
        if (strlen($new_password) < 6) {
            $this->session->set_flashdata('error', 'Password minimal 6 karakter');
            redirect('profile');
            return;
        }
        
        $data = array('password' => $new_password); // Will be hashed by model
        
        if ($this->Guru_model->update($user_id, $data)) {
            $this->session->set_flashdata('success', 'Password berhasil diubah');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengubah password');
        }
        
        redirect('profile');
    }
}
