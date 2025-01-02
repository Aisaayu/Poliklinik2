<?php
session_start();
include('../includes/db.php');

// Pastikan pasien sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pasien') {
    header("Location: login_pasien.php");
    exit();
}
// Update status pemeriksaan
if (isset($_POST['update_status'])) {
    $pendaftaran_id = $_POST['pendaftaran_id'];
    $status = $_POST['status'];

    $update_query = "UPDATE pendaftaran_poli SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $pendaftaran_id);
    if ($stmt->execute()) {
        header("Location: riwayat_pendaftaran.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}


// Ambil ID pendaftaran dari URL
if (isset($_GET['id'])) {
    $pendaftaran_id = $_GET['id'];

    // Ambil data pendaftaran poli berdasarkan ID
    $query = "SELECT pp.*, p.nama_poli, d.nama_dokter 
              FROM pendaftaran_poli pp
              JOIN poli1 p ON pp.id_poli = p.id_poli
              JOIN dokter3 d ON pp.id_dokter = d.id_dokter
              WHERE pp.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pendaftaran_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pendaftaran = $result->fetch_assoc();

    if (!$pendaftaran) {
        echo "Pendaftaran tidak ditemukan.";
        exit();
    }
} else {
    echo "ID pendaftaran tidak valid.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pendaftaran Poli</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Detail Pendaftaran Poli</h2>
    <table class="table table-bordered">
        <tr>
            <th>Poli</th>
            <td><?= $pendaftaran['nama_poli'] ?></td>
        </tr>
        <tr>
            <th>Dokter</th>
            <td><?= $pendaftaran['nama_dokter'] ?></td>
        </tr>
        <tr>
            <th>Keluhan</th>
            <td><?= $pendaftaran['keluhan'] ?></td>
        </tr>
        <tr>
            <th>Hari</th>
            <td><?= date("d-m-Y", strtotime($pendaftaran['hari'])) ?></td>
        </tr>
        <tr>
            <th>Jam Mulai</th>
            <td><?= $pendaftaran['mulai'] ?></td>
        </tr>
        <tr>
            <th>Jam Selesai</th>
            <td><?= $pendaftaran['selesai'] ?></td>
        </tr>
        <tr>
            <th>Antrian</th>
            <td><?= $pendaftaran['antrian'] ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= $pendaftaran['status'] ?></td>
        </tr>
    </table>
    <a href="riwayat_pendaftaran.php" class="btn btn-secondary">Kembali</a>
</div>
</body>
</html>
