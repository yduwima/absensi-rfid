<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_sender {

    protected $CI;
    private $config;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('Wa_config_model');
        $this->config = $this->CI->Wa_config_model->get_config();
    }

    /**
     * Send WhatsApp message
     * 
     * @param string $no_hp Phone number
     * @param string $message Message content
     * @return array Result with status and message
     */
    public function send($no_hp, $message) {
        if (!$this->config || !$this->config->is_active) {
            return [
                'status' => 'error',
                'message' => 'WhatsApp notification is not configured or inactive'
            ];
        }

        // Format phone number (remove leading 0, add country code if needed)
        $no_hp = $this->format_phone_number($no_hp);

        // Prepare API request
        $url = $this->config->url_api;
        $data = [
            'target' => $no_hp,
            'message' => $message,
            'delay' => 0,
            'countryCode' => '62' // Indonesia
        ];

        // Add API key to headers or data depending on the API
        $headers = [
            'Authorization: ' . $this->config->api_key,
            'Content-Type: application/json'
        ];

        // Send via cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            return [
                'status' => 'error',
                'message' => 'cURL Error: ' . $curl_error
            ];
        }

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'status' => 'success',
                'message' => 'Message sent successfully',
                'response' => $response
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'HTTP Error: ' . $http_code,
                'response' => $response
            ];
        }
    }

    /**
     * Format phone number to international format
     * 
     * @param string $phone
     * @return string
     */
    private function format_phone_number($phone) {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove leading 0
        if (substr($phone, 0, 1) == '0') {
            $phone = substr($phone, 1);
        }
        
        // Add country code if not present
        if (substr($phone, 0, 2) != '62') {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Process queue - send pending messages
     * This should be called via cron job or background process
     * 
     * @param int $limit Number of messages to process
     * @return array Results
     */
    public function process_queue($limit = 10) {
        $this->CI->load->model('Wa_queue_model');
        $pending = $this->CI->Wa_queue_model->get_pending($limit);
        
        $results = [
            'total' => count($pending),
            'success' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($pending as $queue) {
            $result = $this->send($queue->no_hp, $queue->pesan);
            
            if ($result['status'] == 'success') {
                $this->CI->Wa_queue_model->mark_as_sent($queue->id);
                $results['success']++;
            } else {
                $this->CI->Wa_queue_model->mark_as_failed($queue->id, $result['message']);
                $results['failed']++;
            }
            
            $results['details'][] = [
                'queue_id' => $queue->id,
                'no_hp' => $queue->no_hp,
                'result' => $result
            ];
            
            // Add delay between messages to avoid rate limiting
            sleep(2);
        }

        return $results;
    }
}
