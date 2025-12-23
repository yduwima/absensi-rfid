<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Guru_model');
    }

    public function login() {
        // If already logged in, redirect to dashboard
        if ($this->session->userdata('logged_in')) {
            $this->_redirect_by_role();
        }
        
        $data['title'] = 'Login - Sistem Absensi RFID';
        $this->load->view('auth/login', $data);
    }

    public function do_login() {
        $this->form_validation->set_rules('email', 'Email', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('login');
        }

        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $guru = $this->Guru_model->get_by_email($email);

        if ($guru && password_verify($password, $guru->password)) {
            if ($guru->is_active == 1) {
                $session_data = array(
                    'user_id' => $guru->id,
                    'nama' => $guru->nama,
                    'email' => $guru->email,
                    'role' => $guru->role,
                    'foto' => $guru->foto,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);
                $this->_redirect_by_role();
            } else {
                $this->session->set_flashdata('error', 'Akun Anda tidak aktif. Hubungi administrator.');
                redirect('login');
            }
        } else {
            $this->session->set_flashdata('error', 'Email atau password salah.');
            redirect('login');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    private function _redirect_by_role() {
        $role = $this->session->userdata('role');
        
        if (strpos($role, 'Admin') !== false) {
            redirect('admin/dashboard');
        } elseif (strpos($role, 'BK') !== false) {
            redirect('bk/dashboard');
        } else {
            redirect('guru/dashboard');
        }
    }
}
