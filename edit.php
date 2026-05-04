<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php
require_once 'config/database.php';

$errors = [];


/*
=====================================
AMBIL ID DARI GET
=====================================
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header(
        "Location: index.php?message=" .
        urlencode("ID kategori tidak valid.")
    );
    exit;
}


/*
=====================================
RETRIEVE DATA BERDASARKAN ID
=====================================
*/

$sql = "SELECT * FROM kategori WHERE id_kategori = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header(
        "Location: index.php?message=" .
        urlencode("Data kategori tidak ditemukan.")
    );
    exit;
}

$data = $result->fetch_assoc();

$stmt->close();


/*
=====================================
SET VALUE DEFAULT
=====================================
*/

$kode = $data['kode_kategori'];
$nama = $data['nama_kategori'];
$deskripsi = $data['deskripsi'];
$status = $data['status'];



/*
=====================================
PROSES UPDATE
=====================================
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    
    // Sanitasi input
    $kode = htmlspecialchars(trim($_POST['kode'] ?? ''));
    $nama = htmlspecialchars(trim($_POST['nama'] ?? ''));
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi'] ?? ''));
    $status = $_POST['status'] ?? 'Aktif';
    
    
    /*
    =====================================
    VALIDASI KODE
    =====================================
    */
    
    if (empty($kode)) {
        $errors[] = "Kode kategori wajib diisi.";
        
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors[] = "Kode kategori harus 4-10 karakter.";
        
    } elseif (!preg_match('/^KAT\-/', $kode)) {
        $errors[] = "Kode kategori harus diawali 'KAT-'.";
    }
    
    
    
    /*
    =====================================
    VALIDASI NAMA
    =====================================
    */
    
    if (empty($nama)) {
        $errors[] = "Nama kategori wajib diisi.";
        
    } elseif (strlen($nama) < 3) {
        $errors[] = "Nama kategori minimal 3 karakter.";
        
    } elseif (strlen($nama) > 50) {
        $errors[] = "Nama kategori maksimal 50 karakter.";
    }
    
    
    
    /*
    =====================================
    VALIDASI DESKRIPSI
    =====================================
    */
    
    if (!empty($deskripsi) && strlen($deskripsi) > 200) {
        $errors[] = "Deskripsi maksimal 200 karakter.";
    }
    
    
    
    /*
    =====================================
    VALIDASI STATUS
    =====================================
    */
    
    if ($status != 'Aktif' && $status != 'Nonaktif') {
        $errors[] = "Status tidak valid.";
    }
    
    
    
    /*
    =====================================
    CEK DUPLIKASI KODE
    EXCLUDE DATA YANG DIEDIT
    =====================================
    */
    
    if (empty($errors)) {
        
        $checkSql = "SELECT id_kategori
                     FROM kategori
                     WHERE kode_kategori = ?
                     AND id_kategori != ?";
                     
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("si", $kode, $id);
        $checkStmt->execute();
        
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $errors[] = "Kode kategori sudah digunakan.";
        }
        
        $checkStmt->close();
    }
    
    
    
    /*
    =====================================
    UPDATE DATA
    =====================================
    */
    
    if (empty($errors)) {
        
        $updateSql = "UPDATE kategori
                      SET
                        kode_kategori = ?,
                        nama_kategori = ?,
                        deskripsi = ?,
                        status = ?
                      WHERE id_kategori = ?";
                      
        $updateStmt = $conn->prepare($updateSql);
        
        $updateStmt->bind_param(
            "ssssi",
            $kode,
            $nama,
            $deskripsi,
            $status,
            $id
        );
        
        
        if ($updateStmt->execute()) {
            
            header(
                "Location: index.php?message=" .
                urlencode("Data kategori berhasil diupdate.")
            );
            exit;
            
        } else {
            $errors[] = "Gagal mengupdate data.";
        }
        
        $updateStmt->close();
    }
}
?>



<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            
            <div class="card">
                
                
                <div class="card-header">
                    <h4>Edit Kategori</h4>
                </div>
                
                
                <div class="card-body">
                    
                    
                    <!-- ERROR -->
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

                            <div class="form-check">
                                <input 
                                    type="radio"
                                    name="status"
                                    value="Aktif"
                                    <?php if($status == 'Aktif') echo 'checked'; ?>
                                >
                                Aktif
                            </div>

                            <div class="form-check">
                                <input 
                                    type="radio"
                                    name="status"
                                    value="Nonaktif"
                                    <?php if($status == 'Nonaktif') echo 'checked'; ?>
                                >
                                Nonaktif
                            </div>
                        </div>
                        
                        
                        <div class="d-flex gap-2">
                            
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Update
                            </button>
                            
                            
                            <a
                                href="index.php"
                                class="btn btn-secondary"
                            >
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