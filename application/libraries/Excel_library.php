<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Excel Library
 * 
 * Library untuk import dan export file Excel menggunakan PhpSpreadsheet
 * Pastikan PhpSpreadsheet sudah terinstall via Composer
 * 
 * Installation:
 * composer require phpoffice/phpspreadsheet
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Excel_library {
    
    protected $CI;
    protected $spreadsheet;
    protected $active_sheet;
    
    public function __construct() {
        $this->CI =& get_instance();
        
        // Check if PhpSpreadsheet is installed
        if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            log_message('error', 'PhpSpreadsheet library not found. Install via: composer require phpoffice/phpspreadsheet');
        }
    }
    
    /**
     * Create new spreadsheet
     */
    public function create() {
        $this->spreadsheet = new Spreadsheet();
        $this->active_sheet = $this->spreadsheet->getActiveSheet();
        return $this;
    }
    
    /**
     * Load existing file
     */
    public function load($file_path) {
        try {
            $this->spreadsheet = IOFactory::load($file_path);
            $this->active_sheet = $this->spreadsheet->getActiveSheet();
            return $this;
        } catch (Exception $e) {
            log_message('error', 'Error loading Excel file: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Set sheet title
     */
    public function set_title($title) {
        $this->active_sheet->setTitle($title);
        return $this;
    }
    
    /**
     * Write data to cells
     */
    public function write_data($data, $start_row = 1, $start_col = 'A') {
        $row = $start_row;
        foreach ($data as $row_data) {
            $col = $start_col;
            foreach ($row_data as $cell_value) {
                $this->active_sheet->setCellValue($col . $row, $cell_value);
                $col++;
            }
            $row++;
        }
        return $this;
    }
    
    /**
     * Style header row
     */
    public function style_header($row = 1, $start_col = 'A', $end_col = 'Z') {
        $range = $start_col . $row . ':' . $end_col . $row;
        
        $this->active_sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);
        
        return $this;
    }
    
    /**
     * Auto size columns
     */
    public function auto_size($start_col = 'A', $end_col = 'Z') {
        foreach (range($start_col, $end_col) as $col) {
            $this->active_sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return $this;
    }
    
    /**
     * Add borders to range
     */
    public function add_borders($start_cell, $end_cell) {
        $range = $start_cell . ':' . $end_cell;
        $this->active_sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        return $this;
    }
    
    /**
     * Export to browser download
     */
    public function download($filename = 'export.xlsx') {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Save to file
     */
    public function save($file_path) {
        try {
            $writer = new Xlsx($this->spreadsheet);
            $writer->save($file_path);
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error saving Excel file: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Read data from loaded file
     */
    public function read_data($start_row = 2, $has_header = true) {
        if (!$this->spreadsheet) {
            return false;
        }
        
        $data = [];
        $headers = [];
        
        // Get headers if exists
        if ($has_header) {
            $header_row = $this->active_sheet->rangeToArray('A1:Z1')[0];
            $headers = array_filter($header_row, function($val) {
                return !empty($val);
            });
        }
        
        // Get data rows
        $highest_row = $this->active_sheet->getHighestRow();
        $highest_col = $this->active_sheet->getHighestColumn();
        
        for ($row = $start_row; $row <= $highest_row; $row++) {
            $row_data = $this->active_sheet->rangeToArray('A' . $row . ':' . $highest_col . $row)[0];
            
            // Skip empty rows
            if (empty(array_filter($row_data))) {
                continue;
            }
            
            // Associate with headers if available
            if ($has_header && !empty($headers)) {
                $row_data = array_combine($headers, array_slice($row_data, 0, count($headers)));
            }
            
            $data[] = $row_data;
        }
        
        return $data;
    }
    
    /**
     * Import siswa from Excel
     */
    public function import_siswa($file_path) {
        if (!$this->load($file_path)) {
            return ['success' => false, 'message' => 'Gagal membaca file Excel'];
        }
        
        $data = $this->read_data(2, true);
        
        if (empty($data)) {
            return ['success' => false, 'message' => 'File Excel kosong atau format salah'];
        }
        
        $this->CI->load->model('Siswa_model');
        $this->CI->load->model('Kelas_model');
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $row) {
            try {
                // Validate required fields
                if (empty($row['NIS']) || empty($row['Nama'])) {
                    $failed++;
                    $errors[] = "Baris dengan NIS kosong atau Nama kosong";
                    continue;
                }
                
                // Get kelas_id from nama kelas
                $kelas = null;
                if (!empty($row['Kelas'])) {
                    $kelas = $this->CI->Kelas_model->get_by_name($row['Kelas']);
                }
                
                // Prepare data
                $siswa_data = [
                    'nis' => $row['NIS'],
                    'nisn' => $row['NISN'] ?? '',
                    'nama' => $row['Nama'],
                    'kelas_id' => $kelas ? $kelas->id : null,
                    'jenis_kelamin' => $row['Jenis Kelamin'] ?? 'L',
                    'tempat_lahir' => $row['Tempat Lahir'] ?? '',
                    'tanggal_lahir' => !empty($row['Tanggal Lahir']) ? date('Y-m-d', strtotime($row['Tanggal Lahir'])) : null,
                    'alamat' => $row['Alamat'] ?? '',
                    'nama_ortu' => $row['Nama Ortu'] ?? '',
                    'no_hp_ortu' => $row['No HP Ortu'] ?? '',
                    'rfid_uid' => $row['RFID UID'] ?? ''
                ];
                
                // Insert
                if ($this->CI->Siswa_model->insert($siswa_data)) {
                    $success++;
                } else {
                    $failed++;
                    $errors[] = "Gagal import siswa: " . $row['Nama'];
                }
                
            } catch (Exception $e) {
                $failed++;
                $errors[] = "Error: " . $e->getMessage();
            }
        }
        
        return [
            'success' => true,
            'total' => count($data),
            'success_count' => $success,
            'failed_count' => $failed,
            'errors' => $errors
        ];
    }
    
    /**
     * Import guru from Excel
     */
    public function import_guru($file_path) {
        if (!$this->load($file_path)) {
            return ['success' => false, 'message' => 'Gagal membaca file Excel'];
        }
        
        $data = $this->read_data(2, true);
        
        if (empty($data)) {
            return ['success' => false, 'message' => 'File Excel kosong atau format salah'];
        }
        
        $this->CI->load->model('Guru_model');
        
        $success = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $row) {
            try {
                if (empty($row['NIP']) || empty($row['Nama'])) {
                    $failed++;
                    continue;
                }
                
                $guru_data = [
                    'nip' => $row['NIP'],
                    'nama' => $row['Nama'],
                    'email' => $row['Email'] ?? '',
                    'password' => !empty($row['Password']) ? $row['Password'] : 'password123',
                    'no_hp' => $row['No HP'] ?? '',
                    'jenis_kelamin' => $row['Jenis Kelamin'] ?? 'L',
                    'alamat' => $row['Alamat'] ?? '',
                    'rfid_uid' => $row['RFID UID'] ?? '',
                    'role' => $row['Role'] ?? 'guru'
                ];
                
                if ($this->CI->Guru_model->insert($guru_data)) {
                    $success++;
                } else {
                    $failed++;
                }
                
            } catch (Exception $e) {
                $failed++;
                $errors[] = $e->getMessage();
            }
        }
        
        return [
            'success' => true,
            'total' => count($data),
            'success_count' => $success,
            'failed_count' => $failed,
            'errors' => $errors
        ];
    }
}
