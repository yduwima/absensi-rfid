<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_notifikasi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
        
        // Check if user is admin
        if ($this->session->userdata('role') != 'Admin') {
            show_error('Access denied', 403);
        }
        
        $this->load->model('Wa_config_model');
        $this->load->model('Kelas_model');
    }

    public function index() {
        // Get WA configuration
        $data['wa_config'] = $this->Wa_config_model->get_config();
        
        // Get all classes for notification selection
        $data['kelas_list'] = $this->Kelas_model->get_all_no_pagination();
        
        // Get selected classes for notification
        $data['selected_kelas'] = $this->Wa_config_model->get_active_classes();
        
        $data['title'] = 'Pengaturan WhatsApp Notifikasi';
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/wa_notifikasi/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function update_config() {
        $config_data = array(
            'api_url' => $this->input->post('api_url'),
            'api_key' => $this->input->post('api_key'),
            'sender_number' => $this->input->post('sender_number'),
            'link_url' => $this->input->post('link_url'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
            'template_masuk' => $this->input->post('template_masuk'),
            'template_pulang' => $this->input->post('template_pulang')
        );
        
        if ($this->Wa_config_model->update_config($config_data)) {
            $this->session->set_flashdata('success', 'Konfigurasi WhatsApp berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui konfigurasi');
        }
        
        redirect('admin/wa_notifikasi');
    }

    public function update_kelas_aktif() {
        $kelas_ids = $this->input->post('kelas_ids');
        
        if ($this->Wa_config_model->update_active_classes($kelas_ids)) {
            $this->session->set_flashdata('success', 'Kelas penerima notifikasi berhasil diperbarui');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui kelas penerima');
        }
        
        redirect('admin/wa_notifikasi');
    }

    public function test_notification() {
        $phone = $this->input->post('test_phone');
        $message = $this->input->post('test_message');
        
        $this->load->library('Wa_sender');
        $result = $this->wa_sender->send($phone, $message);
        
        echo json_encode($result);
    }
}
