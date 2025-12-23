<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PDF Library
 * 
 * Library untuk generate PDF menggunakan TCPDF
 * Pastikan TCPDF sudah terinstall via Composer
 * 
 * Installation:
 * composer require tecnickcom/tcpdf
 */

require_once(APPPATH . '../vendor/autoload.php');

class Pdf_library extends TCPDF {
    
    protected $CI;
    protected $school_name;
    protected $school_address;
    protected $school_logo;
    protected $principal_name;
    
    public function __construct() {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $this->CI =& get_instance();
        $this->CI->load->model('Settings_model');
        
        // Load school settings
        $settings = $this->CI->Settings_model->get();
        if ($settings) {
            $this->school_name = $settings->nama_sekolah ?? 'SEKOLAH';
            $this->school_address = $settings->alamat_sekolah ?? '';
            $this->principal_name = $settings->nama_kepala_sekolah ?? '';
            $this->school_logo = $settings->logo_sekolah ?? '';
        }
        
        // Set document information
        $this->SetCreator('Sistem Absensi RFID');
        $this->SetAuthor($this->school_name);
        
        // Remove default header/footer
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        
        // Set margins
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(TRUE, 15);
        
        // Set font
        $this->SetFont('helvetica', '', 10);
    }
    
    /**
     * Add custom header with school letterhead
     */
    public function add_letterhead() {
        $html = '<table width="100%" cellpadding="5" style="border-bottom: 3px solid #000;">
            <tr>
                <td width="15%" align="center">';
        
        if (!empty($this->school_logo) && file_exists('assets/uploads/logo/' . $this->school_logo)) {
            $html .= '<img src="assets/uploads/logo/' . $this->school_logo . '" width="60">';
        }
        
        $html .= '</td>
                <td width="85%" align="center">
                    <h2 style="margin:0; font-size:18px; font-weight:bold;">' . strtoupper($this->school_name) . '</h2>
                    <p style="margin:2px 0; font-size:11px;">' . $this->school_address . '</p>
                </td>
            </tr>
        </table>
        <br><br>';
        
        $this->writeHTML($html, true, false, true, false, '');
    }
    
    /**
     * Generate laporan absensi siswa PDF
     */
    public function laporan_absensi_siswa($data, $filters) {
        $this->AddPage();
        $this->add_letterhead();
        
        // Title
        $title = '<h3 style="text-align:center; margin:10px 0;">LAPORAN ABSENSI SISWA</h3>';
        $title .= '<p style="text-align:center; margin:5px 0;">Bulan: ' . $filters['bulan'] . ' | Tahun: ' . $filters['tahun'] . '</p>';
        if (!empty($filters['kelas'])) {
            $title .= '<p style="text-align:center; margin:5px 0;">Kelas: ' . $filters['kelas'] . '</p>';
        }
        $title .= '<br>';
        
        $this->writeHTML($title, true, false, true, false, '');
        
        // Table
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width:100%;">
            <thead>
                <tr style="background-color:#4472C4; color:#fff; font-weight:bold;">
                    <th width="5%">No</th>
                    <th width="15%">NIS</th>
                    <th width="25%">Nama</th>
                    <th width="15%">Kelas</th>
                    <th width="15%">Jam Masuk</th>
                    <th width="15%">Jam Pulang</th>
                    <th width="10%">Terlambat</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        foreach ($data as $row) {
            $html .= '<tr>
                <td align="center">' . $no++ . '</td>
                <td>' . $row->nis . '</td>
                <td>' . $row->nama_siswa . '</td>
                <td>' . $row->nama_kelas . '</td>
                <td>' . ($row->jam_masuk ?? '-') . '</td>
                <td>' . ($row->jam_pulang ?? '-') . '</td>
                <td align="center">' . ($row->keterlambatan_menit > 0 ? $row->keterlambatan_menit . ' mnt' : '-') . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        
        $this->writeHTML($html, true, false, true, false, '');
        
        // Footer
        $footer = '<br><br><table width="100%">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="center">
                    <p>Mengetahui,<br>Kepala Sekolah</p>
                    <br><br><br>
                    <p><u>' . $this->principal_name . '</u><br>NIP. ........................</p>
                </td>
            </tr>
        </table>';
        
        $this->writeHTML($footer, true, false, true, false, '');
    }
    
    /**
     * Generate laporan absensi guru PDF
     */
    public function laporan_absensi_guru($data, $filters) {
        $this->AddPage();
        $this->add_letterhead();
        
        // Title
        $title = '<h3 style="text-align:center; margin:10px 0;">LAPORAN ABSENSI GURU</h3>';
        $title .= '<p style="text-align:center; margin:5px 0;">Bulan: ' . $filters['bulan'] . ' | Tahun: ' . $filters['tahun'] . '</p><br>';
        
        $this->writeHTML($title, true, false, true, false, '');
        
        // Table
        $html = '<table border="1" cellpadding="5" style="border-collapse: collapse; width:100%;">
            <thead>
                <tr style="background-color:#4472C4; color:#fff; font-weight:bold;">
                    <th width="5%">No</th>
                    <th width="15%">NIP</th>
                    <th width="30%">Nama</th>
                    <th width="15%">Jam Masuk</th>
                    <th width="15%">Jam Pulang</th>
                    <th width="10%">Terlambat</th>
                    <th width="10%">Status</th>
                </tr>
            </thead>
            <tbody>';
        
        $no = 1;
        foreach ($data as $row) {
            $html .= '<tr>
                <td align="center">' . $no++ . '</td>
                <td>' . $row->nip . '</td>
                <td>' . $row->nama_guru . '</td>
                <td>' . ($row->jam_masuk ?? '-') . '</td>
                <td>' . ($row->jam_pulang ?? '-') . '</td>
                <td align="center">' . ($row->keterlambatan_menit > 0 ? $row->keterlambatan_menit . ' mnt' : '-') . '</td>
                <td align="center">' . ($row->jam_masuk ? 'Hadir' : 'Alpha') . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        
        $this->writeHTML($html, true, false, true, false, '');
        
        // Footer
        $footer = '<br><br><table width="100%">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="center">
                    <p>Mengetahui,<br>Kepala Sekolah</p>
                    <br><br><br>
                    <p><u>' . $this->principal_name . '</u><br>NIP. ........................</p>
                </td>
            </tr>
        </table>';
        
        $this->writeHTML($footer, true, false, true, false, '');
    }
    
    /**
     * Generate surat BK
     */
    public function surat_bk($surat_data, $siswa_data, $monitoring_data) {
        $this->AddPage();
        $this->add_letterhead();
        
        // Nomor surat
        $html = '<p align="right">Nomor: ' . $surat_data->nomor_surat . '</p><br>';
        
        // Title
        $html .= '<h3 style="text-align:center; margin:20px 0;">SURAT PEMANGGILAN ORANG TUA/WALI</h3><br>';
        
        // Content
        $html .= '<p>Kepada Yth.<br>
            Orang Tua/Wali dari:<br>
            <b>Nama: ' . $siswa_data->nama . '</b><br>
            <b>Kelas: ' . $siswa_data->nama_kelas . '</b><br>
            Di tempat</p><br>';
        
        $html .= '<p>Dengan hormat,<br><br>
            Sehubungan dengan catatan kehadiran putra/putri Bapak/Ibu, kami bermaksud untuk memanggil Bapak/Ibu untuk hadir ke sekolah guna membahas masalah kedisiplinan putra/putri Bapak/Ibu.</p><br>';
        
        $html .= '<p><b>Data Pelanggaran:</b></p>
            <ul>
                <li>Jumlah Alpha: ' . $monitoring_data->jumlah_alpha . ' kali</li>
                <li>Jumlah Terlambat: ' . $monitoring_data->jumlah_terlambat . ' kali</li>
                <li>Status: ' . $monitoring_data->status . '</li>
            </ul><br>';
        
        $html .= '<p>Demikian surat pemanggilan ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p><br><br>';
        
        // Signature
        $html .= '<table width="100%">
            <tr>
                <td width="60%"></td>
                <td width="40%" align="center">
                    <p>' . date('d F Y', strtotime($surat_data->tanggal_surat)) . '<br>
                    Guru BK</p>
                    <br><br><br>
                    <p><u>' . ($surat_data->nama_guru ?? '......................') . '</u><br>
                    NIP. ........................</p>
                </td>
            </tr>
        </table>';
        
        $this->writeHTML($html, true, false, true, false, '');
    }
    
    /**
     * Output PDF to browser
     */
    public function stream($filename = 'document.pdf') {
        $this->Output($filename, 'I');
    }
    
    /**
     * Download PDF
     */
    public function download($filename = 'document.pdf') {
        $this->Output($filename, 'D');
    }
    
    /**
     * Save PDF to file
     */
    public function save_file($filepath) {
        $this->Output($filepath, 'F');
    }
}
