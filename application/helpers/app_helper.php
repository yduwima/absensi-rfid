<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format Indonesian date
 */
if (!function_exists('format_tanggal')) {
    function format_tanggal($tanggal, $format = 'long') {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        $timestamp = strtotime($tanggal);
        $day = date('d', $timestamp);
        $month = $bulan[(int)date('m', $timestamp)];
        $year = date('Y', $timestamp);
        $day_name = $hari[date('l', $timestamp)];
        
        if ($format == 'long') {
            return $day_name . ', ' . $day . ' ' . $month . ' ' . $year;
        } else {
            return $day . ' ' . $month . ' ' . $year;
        }
    }
}

/**
 * Format Indonesian time
 */
if (!function_exists('format_waktu')) {
    function format_waktu($waktu) {
        return date('H:i', strtotime($waktu));
    }
}

/**
 * Calculate age from birth date
 */
if (!function_exists('hitung_umur')) {
    function hitung_umur($tanggal_lahir) {
        $birthDate = new DateTime($tanggal_lahir);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
        return $age . ' tahun';
    }
}

/**
 * Format phone number
 */
if (!function_exists('format_phone')) {
    function format_phone($phone) {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add +62 if starts with 0
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        return $phone;
    }
}

/**
 * Get attendance status badge
 */
if (!function_exists('status_badge')) {
    function status_badge($status) {
        $badges = [
            'H' => '<span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Hadir</span>',
            'S' => '<span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">Sakit</span>',
            'I' => '<span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">Izin</span>',
            'A' => '<span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Alpha</span>'
        ];
        
        return isset($badges[$status]) ? $badges[$status] : $status;
    }
}

/**
 * Generate random string
 */
if (!function_exists('generate_rfid')) {
    function generate_rfid($length = 10) {
        return strtoupper(bin2hex(random_bytes($length / 2)));
    }
}

/**
 * Check if current user has role
 */
if (!function_exists('has_role')) {
    function has_role($role) {
        $CI =& get_instance();
        $user_role = $CI->session->userdata('role');
        return strpos($user_role, $role) !== false;
    }
}

/**
 * Format file size
 */
if (!function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
