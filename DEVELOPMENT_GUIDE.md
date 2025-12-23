# DEVELOPER GUIDE - Melanjutkan Development

Panduan ini untuk developer yang akan melanjutkan pengembangan sistem ini.

## 🏗️ Arsitektur Aplikasi

### MVC Pattern
```
application/
├── controllers/     ← Logic & routing
├── models/          ← Database operations
└── views/           ← User interface (HTML)
```

### Request Flow
```
User → Route → Controller → Model → Database
                    ↓
                   View → User
```

## 📁 Struktur Code Pattern

### 1. Model Pattern (Contoh: Siswa_model.php)

```php
class Siswa_model extends CI_Model {
    private $table = 'siswa';
    
    // Get all with pagination & search
    public function get_all($search = '', $limit = 10, $offset = 0) {
        if (!empty($search)) {
            $this->db->like('nama', $search);
        }
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result();
    }
    
    // Count for pagination
    public function count_all($search = '') { ... }
    
    // CRUD operations
    public function get_by_id($id) { ... }
    public function insert($data) { ... }
    public function update($id, $data) { ... }
    public function delete($id) { ... }
}
```

### 2. Controller Pattern (Contoh: Admin/Siswa.php)

```php
class Siswa extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->_check_login();     // Auth middleware
        $this->_check_role(['Admin']); // Role check
        $this->load->model('Siswa_model');
    }
    
    // List page with pagination
    public function index() {
        $config = $this->_pagination_config();
        $data['siswa'] = $this->Siswa_model->get_all(...);
        $this->load->view('admin/siswa/index', $data);
    }
    
    // CRUD operations
    public function create() { ... }
    public function store() { ... }
    public function edit($id) { ... }
    public function update($id) { ... }
    public function delete($id) { ... }
}
```

### 3. View Pattern (Contoh: admin/siswa/index.php)

```php
<!-- Header -->
<?php $this->load->view('templates/admin_header', $data); ?>

<!-- Content -->
<div class="container">
    <h1>Data Siswa</h1>
    
    <!-- Search & Filter -->
    <form method="get">
        <input name="search" placeholder="Cari...">
    </form>
    
    <!-- Table -->
    <table>
        <?php foreach ($siswa as $s): ?>
        <tr>
            <td><?= $s->nama ?></td>
            <td>
                <a href="<?= base_url('admin/siswa/edit/' . $s->id) ?>">Edit</a>
                <a href="<?= base_url('admin/siswa/delete/' . $s->id) ?>">Hapus</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- Pagination -->
    <?= $this->pagination->create_links(); ?>
</div>

<!-- Footer -->
<?php $this->load->view('templates/admin_footer'); ?>
```

## 🎯 Cara Membuat Fitur Baru

### Step-by-Step: Membuat CRUD Data Guru

#### 1. Pastikan Model Sudah Ada
✅ `Guru_model.php` sudah ada

#### 2. Buat Controller

**File:** `application/controllers/admin/Guru.php`

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Guru extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->_check_login();
        $this->_check_role(['Admin']);
        $this->load->model('Guru_model');
        $this->load->library('pagination');
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
        // Get parameters
        $search = $this->input->get('search');
        $per_page = $this->input->get('per_page') ?? 10;
        $page = $this->input->get('page') ?? 1;
        $offset = ($page - 1) * $per_page;
        
        // Get data
        $data['guru'] = $this->Guru_model->get_all($search, $per_page, $offset);
        $data['total'] = $this->Guru_model->count_all($search);
        
        // Pagination config
        $config['base_url'] = base_url('admin/guru');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        // Load view
        $data['title'] = 'Data Guru';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/guru/index', $data);
        $this->load->view('templates/admin_footer');
    }

    public function create() {
        $data['title'] = 'Tambah Guru';
        $data['user'] = $this->session->userdata();
        
        $this->load->view('templates/admin_header', $data);
        $this->load->view('admin/guru/create', $data);
        $this->load->view('templates/admin_footer');
    }

    public function store() {
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[guru.email]');
        $this->form_validation->set_rules('nip', 'NIP', 'required|is_unique[guru.nip]');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/guru/create');
        }
        
        $data = [
            'nip' => $this->input->post('nip'),
            'nama' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'password' => $this->input->post('password'),
            'no_hp' => $this->input->post('no_hp'),
            'jenis_kelamin' => $this->input->post('jenis_kelamin'),
            'alamat' => $this->input->post('alamat'),
            'role' => $this->input->post('role')
        ];
        
        if ($this->Guru_model->insert($data)) {
            $this->session->set_flashdata('success', 'Data guru berhasil ditambahkan');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data guru');
        }
        
        redirect('admin/guru');
    }

    // Similar for edit(), update(), delete()
}
```

#### 3. Buat Views

**File:** `application/views/admin/guru/index.php`

```php
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Data Guru</h1>
</div>

<!-- Search & Add Button -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <form method="get" class="flex gap-2">
            <input type="text" name="search" value="<?= $this->input->get('search') ?>" 
                   class="border rounded px-4 py-2" placeholder="Cari guru...">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
        
        <a href="<?= base_url('admin/guru/create') ?>" 
           class="bg-green-600 text-white px-4 py-2 rounded">
            <i class="fas fa-plus"></i> Tambah Guru
        </a>
    </div>
    
    <!-- Table -->
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">No</th>
                <th class="px-4 py-2">NIP</th>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($guru as $g): ?>
            <tr class="border-b">
                <td class="px-4 py-2"><?= $no++ ?></td>
                <td class="px-4 py-2"><?= $g->nip ?></td>
                <td class="px-4 py-2"><?= $g->nama ?></td>
                <td class="px-4 py-2"><?= $g->email ?></td>
                <td class="px-4 py-2"><?= $g->role ?></td>
                <td class="px-4 py-2">
                    <a href="<?= base_url('admin/guru/edit/' . $g->id) ?>" 
                       class="text-blue-600">Edit</a>
                    <a href="<?= base_url('admin/guru/delete/' . $g->id) ?>" 
                       class="text-red-600 ml-2"
                       onclick="return confirm('Yakin hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="mt-4">
        <?= $this->pagination->create_links(); ?>
    </div>
</div>
```

## 🔧 Tools & Libraries Yang Perlu Ditambahkan

### 1. PhpSpreadsheet (Import/Export Excel)

**Install via Composer:**
```bash
composer require phpoffice/phpspreadsheet
```

**Contoh Penggunaan:**
```php
// Import Excel
use PhpOffice\PhpSpreadsheet\IOFactory;

$spreadsheet = IOFactory::load($file_path);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

foreach ($rows as $row) {
    // Process each row
}

// Export Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World!');

$writer = new Xlsx($spreadsheet);
$writer->save('output.xlsx');
```

### 2. TCPDF (Generate PDF)

**Download:** https://github.com/tecnickcom/TCPDF

**Contoh Penggunaan:**
```php
require_once('tcpdf/tcpdf.php');

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, 'Hello World!');
$pdf->Output('output.pdf', 'I'); // I = inline, D = download
```

## 📊 Tips & Best Practices

### 1. Security
```php
// Always sanitize input
$data = $this->security->xss_clean($this->input->post());

// Use prepared statements (CodeIgniter does this automatically)
$this->db->where('id', $id);

// Validate file uploads
$config['allowed_types'] = 'jpg|png|jpeg';
$this->upload->initialize($config);
```

### 2. Error Handling
```php
// Use try-catch for database operations
try {
    $this->db->insert('table', $data);
    if ($this->db->affected_rows() > 0) {
        // Success
    }
} catch (Exception $e) {
    log_message('error', $e->getMessage());
    // Handle error
}
```

### 3. AJAX Operations
```php
// Controller
public function ajax_delete($id) {
    $result = $this->Model->delete($id);
    echo json_encode(['status' => $result ? 'success' : 'error']);
}

// JavaScript
$.ajax({
    url: '<?= base_url("controller/ajax_delete/") ?>' + id,
    type: 'POST',
    dataType: 'json',
    success: function(response) {
        if (response.status == 'success') {
            Swal.fire('Success', 'Data deleted', 'success');
        }
    }
});
```

## 🚀 Next Steps Priority

1. **Admin CRUD** (Prioritas Tinggi)
   - Guru (import/export)
   - Siswa (import/export)
   - Kelas
   - Mata Pelajaran
   - Jadwal

2. **Guru Features**
   - Jurnal Mengajar
   - Input Absensi per Mapel

3. **Laporan**
   - Export PDF
   - Export Excel
   - Filtering & Search

4. **BK Features**
   - Monitoring
   - Generate Surat

## 📚 Resources

- **CodeIgniter 3 Docs**: https://codeigniter.com/userguide3/
- **Tailwind CSS**: https://tailwindcss.com/docs
- **SweetAlert2**: https://sweetalert2.github.io/
- **jQuery**: https://api.jquery.com/

## 🐛 Debugging Tips

```php
// Enable profiler (for development only)
$this->output->enable_profiler(TRUE);

// Debug variables
var_dump($data);
print_r($data);

// Check last query
echo $this->db->last_query();

// Log messages
log_message('error', 'Error message here');
log_message('debug', 'Debug info here');
```

## 💡 Common Issues & Solutions

### Issue: CSRF Token Mismatch
```php
// Solution: Regenerate token or exclude route
$config['csrf_exclude_uris'] = array('api/*');
```

### Issue: Upload Error
```php
// Solution: Check permissions & config
chmod 777 assets/uploads
$config['upload_path'] = './assets/uploads/';
$config['allowed_types'] = 'gif|jpg|png';
```

### Issue: Session Lost
```php
// Solution: Check session config
$config['sess_driver'] = 'files';
$config['sess_save_path'] = APPPATH . 'sessions/';
mkdir application/sessions && chmod 777 application/sessions
```

---

Semoga guide ini membantu! Happy coding! 🎉
