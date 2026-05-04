<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    $errors = [];
    $kode = '';
    $nama = '';
    $deskripsi = '';
    $status = 'Aktif';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // Ambil dan sanitasi data
        $kode = htmlspecialchars(trim($_POST['kode'] ?? ''));
        $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
        $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
        $status = htmlspecialchars(trim($_POST['status'] ?? ''));
        
        if (empty($kode)) {
            $errors[] = "Kode kategori wajib diisi.";
        } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
            $errors[] = "Kode kategori harus 4-10 karakter.";
        } elseif (!preg_match('/^KAT\-/', $kode)) {
            $errors[] = "Kode kategori harus diawali dengan 'KAT-'.";
        }
        
        if (empty($nama)) {
            $errors[] = "Nama kategori wajib diisi.";
        } elseif (strlen($nama) < 3) {
            $errors[] = "Nama kategori minimal 3 karakter.";
        } elseif (strlen($nama) > 50) {
            $errors[] = "Nama kategori maksimal 50 karakter.";
        }
        
        if (!empty($deskripsi) && strlen($deskripsi) > 200) {
            $errors[] = "Deskripsi maksimal 200 karakter.";
        }
        
        if ($status != 'Aktif' && $status != 'Nonaktif') {
            $errors[] = "Status tidak valid.";
        }
        
        if (empty($errors)) {
            
            $checkSql = "SELECT id_kategori 
                         FROM kategori 
                         WHERE kode_kategori = ?";
                         
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("s", $kode);
            $checkStmt->execute();
            
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $errors[] = "Kode kategori sudah digunakan.";
            }
            
            $checkStmt->close();
        }
        
        if (empty($errors)) {
            
            $sql = "INSERT INTO kategori 
                    (kode_kategori, nama_kategori, deskripsi, status) 
                    VALUES (?, ?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssss",
                $kode,
                $nama,
                $deskripsi,
                $status
            );
            
            
            if ($stmt->execute()) {
                
                header(
                    "Location: index.php?message=" .
                    urlencode("Data kategori berhasil ditambahkan.")
                );
                exit;
                
            } else {
                $errors[] = "Gagal menyimpan data.";
            }
            
            $stmt->close();
        }
    }
    ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <div class="card">
                    
                    <div class="card-header">
                        <h4>Tambah Kategori Baru</h4>
                    </div>
                    
                    <div class="card-body">
                        
                        <!-- Tampilkan error -->
                        <?php if(!empty($errors)): ?>
                            
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    
                                    <?php foreach($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                    
                                </ul>
                            </div>
                            
                        <?php endif; ?>
                        
                        
                        <form method="POST">
                            
                            
                            <!-- KODE -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Kode Kategori
                                </label>
                                
                                <input 
                                    type="text"
                                    name="kode"
                                    class="form-control"
                                    value="<?= $kode ?>"
                                    required
                                >
                            </div>
                            
                            
                            <!-- NAMA -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Kategori
                                </label>
                                
                                <input 
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    value="<?= $nama ?>"
                                    required
                                >
                            </div>
                            
                            
                            <!-- DESKRIPSI -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Deskripsi
                                </label>
                                
                                <textarea 
                                    name="deskripsi"
                                    class="form-control"
                                    rows="4"
                                ><?= $deskripsi ?></textarea>
                            </div>
                              
                            <!-- STATUS -->
                            <div class="mb-4">
                                <label class="form-label d-block">Status</label>

                                <div class="form-check form-check-inline">
                                    <input 
                                        type="radio"
                                        name="status"
                                        id="aktif"
                                        value="Aktif"
                                        class="form-check-input"
                                        <?= ($status == 'Aktif') ? 'checked' : '' ?>
                                    >
                                    <label for="aktif" class="form-check-label">
                                        Aktif
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input 
                                        type="radio"
                                        name="status"
                                        id="nonaktif"
                                        value="Nonaktif"
                                        class="form-check-input"
                                        <?= ($status == 'Nonaktif') ? 'checked' : '' ?>
                                    >
                                    <label for="nonaktif" class="form-check-label">
                                        Nonaktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                                
                                <a href="index.php" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>
                            
                        </form>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>