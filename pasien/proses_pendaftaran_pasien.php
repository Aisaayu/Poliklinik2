<?php
session_start();
include('../includes/db.php');

// Ambil data dari formulir pendaftaran
$nama = $_POST['nama'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$alamat = $_POST['alamat'];
$no_hp = $_POST['no_hp'];
$no_ktp = $_POST['no_ktp'];
$id_poli = $_POST['poli']; // ID poli
$id_dokter = $_POST['dokter']; // ID dokter
$tanggal_pendaftaran = $_POST['tanggal_pendaftaran'];
$waktu_pendaftaran = $_POST['waktu_pendaftaran']; // Pastikan waktu_pendaftaran ada dalam form

// Periksa apakah pasien dengan No KTP sudah terdaftar
$query = "SELECT * FROM pasien WHERE no_ktp = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $no_ktp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Pasien sudah terdaftar
    $_SESSION['error_message'] = "Pasien dengan No KTP $no_ktp sudah terdaftar!";
    header("Location: pendaftaran_pasien_baru.php");
    exit;
} else {
    // Pasien belum terdaftar, lanjutkan pendaftaran
    // Ambil jumlah pasien yang sudah terdaftar pada bulan dan tahun yang sama
    $year_month = date('Ym', strtotime($tanggal_pendaftaran));
    $query = "SELECT COUNT(*) AS jumlah_pasien FROM pasien WHERE DATE_FORMAT(tanggal_daftar, '%Y%m') = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $year_month);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $jumlah_pasien = $row['jumlah_pasien'] + 1; // Urutan pasien baru

    // Generate No RM
    $no_rm = $year_month . '-' . str_pad($jumlah_pasien, 3, '0', STR_PAD_LEFT);

    // Simpan data pasien baru ke database
    $query = "INSERT INTO pasien (nama, tanggal_lahir, jenis_kelamin, alamat, no_hp, no_ktp, id_poli, id_dokter, tanggal_daftar, waktu_pendaftaran, nomor_rekam_medis) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssss", $nama, $tanggal_lahir, $jenis_kelamin, $alamat, $no_hp, $no_ktp, $id_poli, $id_dokter, $tanggal_pendaftaran, $waktu_pendaftaran, $no_rm);
    $stmt->execute();

    // Ambil No Antrian berdasarkan ID terakhir
    $no_antri = $conn->insert_id; // ID terakhir yang dimasukkan, bisa digunakan sebagai No Antrian

    // Set pesan sukses
    $_SESSION['success_message'] = "Pendaftaran berhasil! No RM Anda adalah $no_rm, dan No Antrian Anda adalah $no_antri.";
    
    // Redirect ke halaman pendaftaran dengan pesan sukses
    header("Location: pendaftaran_pasien_baru.php");
    exit;
}
?>
