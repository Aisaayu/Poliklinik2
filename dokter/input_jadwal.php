<?php
include('../includes/db.php');
session_start();

$dokter_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Gantilah dengan session ID yang sesuai

// Ambil data dari form
$hari_periksa = isset($_POST['hari_periksa']) ? $_POST['hari_periksa'] : '';
$waktu_mulai = isset($_POST['waktu_mulai']) ? $_POST['waktu_mulai'] : '';
$waktu_selesai = isset($_POST['waktu_selesai']) ? $_POST['waktu_selesai'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

// Validasi waktu mulai dan selesai
if ($waktu_mulai >= $waktu_selesai) {
    echo "Waktu mulai tidak boleh lebih besar atau sama dengan waktu selesai.";
    exit();
}

// Cek jika sudah ada jadwal yang bertumpukan
$check_query = "SELECT * FROM jadwal_periksa WHERE dokter_id = ? AND hari_periksa = ? 
                AND ((waktu_mulai < ? AND waktu_selesai > ?) OR (waktu_mulai < ? AND waktu_selesai > ?)) 
                AND is_aktif = 'aktif'";
$stmt_check = $conn->prepare($check_query);
$stmt_check->bind_param("isssss", $dokter_id, $hari_periksa, $waktu_mulai, $waktu_mulai, $waktu_selesai, $waktu_selesai);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "Jadwal sudah ada, silakan pilih waktu yang lain.";
    exit();
}

// Cek jika ada jadwal aktif lainnya
if ($status === 'aktif') {
    $check_active_query = "SELECT * FROM jadwal_periksa WHERE dokter_id = ? AND is_aktif = 'aktif'";
    $stmt_check_active = $conn->prepare($check_active_query);
    $stmt_check_active->bind_param("i", $dokter_id);
    $stmt_check_active->execute();
    $result_check_active = $stmt_check_active->get_result();

    if ($result_check_active->num_rows > 0) {
        echo "Hanya satu jadwal yang bisa aktif pada satu waktu.";
        exit();
    }
}

// Query untuk memasukkan data ke dalam tabel
$query = "INSERT INTO jadwal_periksa (dokter_id, hari_periksa, waktu_mulai, waktu_selesai, is_aktif) 
          VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("issss", $dokter_id, $hari_periksa, $waktu_mulai, $waktu_selesai, $status);

if ($stmt->execute()) {
    header("Location: jadwal_periksa.php?success=1");
    exit(); // Pastikan untuk keluar setelah redirect
} else {
    echo "Error: " . $stmt->error; // Tampilkan error jika ada
}
?>
