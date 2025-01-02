<?php

// Start the session
session_start();

// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "poliklinik";
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM pasien WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data pasien berhasil dihapus.'); window.location='kelola_pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data pasien.'); window.location='kelola_pasien.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>
