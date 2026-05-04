<?php
require_once 'config/database.php';

// Validasi ID dari GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php?message=" . urlencode("ID kategori tidak valid."));
    exit;
}


// Cek apakah data ada di database
$checkSql = "SELECT id_kategori 
             FROM kategori 
             WHERE id_kategori = ?";

$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $id);
$checkStmt->execute();

$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows == 0) {
    header("Location: index.php?message=" . urlencode("Data kategori tidak ditemukan."));
    exit;
}

$checkStmt->close();


// Hapus data berdasarkan ID
$deleteSql = "DELETE FROM kategori 
              WHERE id_kategori = ?";

$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();


// Cek apakah delete berhasil
if ($deleteStmt->affected_rows > 0) {
    $message = "Data kategori berhasil dihapus.";
} else {
    $message = "Gagal menghapus data.";
}

$deleteStmt->close();
$conn->close();


// Redirect dengan pesan
header("Location: index.php?message=" . urlencode($message));
exit;
?>