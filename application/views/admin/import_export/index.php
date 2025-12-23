<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Import & Export Data</h1>
        <p class="text-gray-600">Import data dari Excel atau export data ke Excel</p>
    </div>

    <?php if($this->session->flashdata('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= $this->session->flashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if($this->session->flashdata('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- Import Export Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Import Siswa -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-blue-600">
                <i class="fas fa-file-import mr-2"></i> Import Data Siswa
            </h2>
            
            <div class="mb-4">
                <p class="text-gray-700 mb-2">Download template terlebih dahulu:</p>
                <a href="<?= base_url('admin/import_export/download_template_siswa') ?>" 
                   class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-download mr-2"></i> Download Template Siswa
                </a>
            </div>
            
            <form action="<?= base_url('admin/import_export/import_siswa') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Pilih File Excel:</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                           class="w-full border border-gray-300 rounded px-3 py-2">
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB)</p>
                </div>
                
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    <i class="fas fa-upload mr-2"></i> Import Data Siswa
                </button>
            </form>
        </div>

        <!-- Import Guru -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-purple-600">
                <i class="fas fa-file-import mr-2"></i> Import Data Guru
            </h2>
            
            <div class="mb-4">
                <p class="text-gray-700 mb-2">Download template terlebih dahulu:</p>
                <a href="<?= base_url('admin/import_export/download_template_guru') ?>" 
                   class="inline-block bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                    <i class="fas fa-download mr-2"></i> Download Template Guru
                </a>
            </div>
            
            <form action="<?= base_url('admin/import_export/import_guru') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Pilih File Excel:</label>
                    <input type="file" name="file" accept=".xlsx,.xls" required
                           class="w-full border border-gray-300 rounded px-3 py-2">
                    <p class="text-sm text-gray-500 mt-1">Format: .xlsx atau .xls (Max: 10MB)</p>
                </div>
                
                <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    <i class="fas fa-upload mr-2"></i> Import Data Guru
                </button>
            </form>
        </div>

        <!-- Export Siswa -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-green-600">
                <i class="fas fa-file-export mr-2"></i> Export Data Siswa
            </h2>
            
            <p class="text-gray-700 mb-4">Export semua data siswa ke file Excel</p>
            
            <a href="<?= base_url('admin/import_export/export_siswa') ?>" 
               class="inline-block bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                <i class="fas fa-file-excel mr-2"></i> Export Data Siswa
            </a>
        </div>

        <!-- Export Guru -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4 text-indigo-600">
                <i class="fas fa-file-export mr-2"></i> Export Data Guru
            </h2>
            
            <p class="text-gray-700 mb-4">Export semua data guru ke file Excel</p>
            
            <a href="<?= base_url('admin/import_export/export_guru') ?>" 
               class="inline-block bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">
                <i class="fas fa-file-excel mr-2"></i> Export Data Guru
            </a>
        </div>

    </div>

    <!-- Instructions -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">
            <i class="fas fa-info-circle mr-2"></i> Petunjuk Import:
        </h3>
        <ul class="list-disc list-inside text-yellow-800 space-y-1">
            <li>Download template Excel terlebih dahulu</li>
            <li>Isi data sesuai format yang ada di template</li>
            <li>Pastikan tidak ada kolom yang kosong untuk data wajib (NIS, Nama, dll)</li>
            <li>Upload file Excel yang sudah diisi</li>
            <li>Sistem akan memproses dan memberikan laporan hasil import</li>
        </ul>
    </div>
</div>
