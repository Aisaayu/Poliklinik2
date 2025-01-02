<?php
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $waktu_mulai = isset($_POST['waktu_mulai']) ? $_POST['waktu_mulai'] : '';
    $waktu_selesai = isset($_POST['waktu_selesai']) ? $_POST['waktu_selesai'] : '';
    $hari_periksa = isset($_POST['hari_periksa']) ? $_POST['hari_periksa'] : '';

    // Validasi status dan waktu
    $valid_status = ['aktif', 'nonaktif'];
    if (!in_array($status, $valid_status) || $id <= 0 || empty($waktu_mulai) || empty($waktu_selesai) || empty($hari_periksa)) {
        echo "<script>alert('Data tidak valid.'); window.location='jadwal_periksa.php';</script>";
        exit();
    }

    // Cek jika sudah ada jadwal yang bertumpukan
    $dokter_id = $_SESSION['dokter_id']; // Ambil dokter_id dari session atau cara yang sesuai
    $check_query = "SELECT * FROM jadwal_periksa WHERE id != ? AND dokter_id = ? AND hari_periksa = ? 
                    AND ((waktu_mulai < ? AND waktu_selesai > ?) OR (waktu_mulai < ? AND waktu_selesai > ?)) 
                    AND is_aktif = 'aktif'";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("iisssss", $id, $dokter_id, $hari_periksa, $waktu_mulai, $waktu_mulai, $waktu_selesai, $waktu_selesai);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        echo "<script>alert('Jadwal sudah ada, silakan pilih waktu yang lain.'); window.location='jadwal_periksa.php';</script>";
        exit();
    }

    // Query untuk update data
    $query = "UPDATE jadwal_periksa SET status = ?, waktu_mulai = ?, waktu_selesai = ?, hari_periksa = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameter
    $stmt->bind_param("ssssi", $status, $waktu_mulai, $waktu_selesai, $hari_periksa, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil diperbarui.'); window.location='jadwal_periksa.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui jadwal: " . $stmt->error . "'); window.location='jadwal_periksa.php';</script>";
    }

    // Menutup statement dan koneksi
    $stmt->close();
    $conn->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Metode tidak diizinkan.');
}

$jadwal_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk mendapatkan data jadwal berdasarkan ID
$query = "SELECT waktu_mulai, waktu_selesai, hari_periksa FROM jadwal_periksa WHERE id = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $jadwal_id);
$stmt->execute();
$result = $stmt->get_result();
$jadwal = $result->fetch_assoc();

if (!$jadwal) {
    echo "<script>alert('Jadwal tidak ditemukan.'); window.location='jadwal_periksa.php';</script>";
    exit();
}

$waktu_mulai = $jadwal['waktu_mulai'];
$waktu_selesai = $jadwal['waktu_selesai'];
$hari_periksa = $jadwal['hari_periksa']; // Menambahkan hari_periksa
?>
