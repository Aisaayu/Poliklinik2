<?php
session_start();
include('../includes/db.php');

// Validasi session
if (!isset($_SESSION['user_id'])) {
    // Jika session tidak ditemukan, redirect ke halaman login
    header("Location: login_pasien.php");
    exit;
}

// Ambil data session
$user_id = $_SESSION['user_id'];

// Ambil data dari form
$poli_id = $_POST['poli'];
$dokter_id = $_POST['dokter'];
$tanggal_pendaftaran = $_POST['tanggal_pendaftaran'];
$waktu_pendaftaran = $_POST['waktu_pendaftaran'];
$keluhan = $_POST['keluhan'];

// Query untuk menyimpan pendaftaran poli ke tabel pendaftaran_poli
$query_pendaftaran = "INSERT INTO pendaftaran_poli (id_pasien, id_poli, id_dokter, tanggal_pendaftaran, waktu_pendaftaran, keluhan) 
                      VALUES (?, ?, ?, ?, ?, ?)";

// Persiapkan statement
$stmt_pendaftaran = $conn->prepare($query_pendaftaran);
$stmt_pendaftaran->bind_param("iiisss", $user_id, $poli_id, $dokter_id, $tanggal_pendaftaran, $waktu_pendaftaran, $keluhan);

// Eksekusi statement
if ($stmt_pendaftaran->execute()) {
    echo "Pendaftaran Poli berhasil!";
} else {
    echo "Terjadi kesalahan: " . $conn->error;
}

// Tutup koneksi
$stmt_pendaftaran->close();
$conn->close();
?>
