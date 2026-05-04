<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    require_once 'config/database.php';
    
    // Query data kategori menggunakan prepared statement
    $sql = "SELECT * FROM kategori ORDER BY id_kategori DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Ambil hasil query
    $result = $stmt->get_result();
    ?>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Kategori Buku</h2>
            <a href="create.php" class="btn btn-primary">Tambah Kategori</a>
        </div>
        
        <!-- Pesan sukses/error -->
        <?php if(isset($_GET['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($_GET['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="100">Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($result->num_rows > 0){
                            $no = 1;

                            while($row = $result->fetch_assoc()){
                                
                                $badge = (trim($row['status']) === 'Aktif')
                                    ? '<span class="badge bg-success">Aktif</span>'
                                    : '<span class="badge bg-danger">Nonaktif</span>';
                                ?>
                                
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['kode_kategori']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                    <td><?= $badge ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id_kategori'] ?>" 
                                           class="btn btn-warning btn-sm">
                                           Edit
                                        </a>

                                        <button onclick="confirmDelete(<?= $row['id_kategori'] ?>)" 
                                                class="btn btn-danger btn-sm">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>

                                <?php
                            }
                        }else{
                            echo '
                            <tr>
                                <td colspan="6" class="text-center">
                                    Data kategori belum tersedia
                                </td>
                            </tr>';
                        }

                        $stmt->close();
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
    function confirmDelete(id) {
        if (confirm('Yakin ingin menghapus kategori ini?')) {
            window.location.href = 'delete.php?id=' + id;
        }
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>