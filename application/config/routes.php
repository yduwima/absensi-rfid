<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth routes
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['auth/do_login'] = 'auth/do_login';

// RFID routes (no login required)
$route['rfid'] = 'rfid/index';
$route['rfid/scan'] = 'rfid/scan';

// Admin routes
$route['admin'] = 'admin/dashboard';
$route['admin/dashboard'] = 'admin/dashboard';

// Guru routes  
$route['guru'] = 'guru/dashboard';
$route['guru/dashboard'] = 'guru/dashboard';

// BK routes
$route['bk'] = 'bk/dashboard';
$route['bk/dashboard'] = 'bk/dashboard';

// API routes
$route['api/wa/process_queue'] = 'api/wa_queue/process';
