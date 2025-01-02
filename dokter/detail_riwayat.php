<?php
// Koneksi ke database
include('../includes/db.php');
session_start();

if (isset($_GET['id'])) {
    $riwayat_id = $_GET['id'];
    
    // Query untuk mendapatkan detail pemeriksaan pasien
    $query_detail = "SELECT rp.*, p.nama, p.alamat, p.no_ktp, p.no_hp, p.nomor_rekam_medis 
                     FROM riwayat_periksa rp
                     JOIN pasien p ON rp.id_pasien = p.id
                     WHERE rp.id = '$riwayat_id'";

    $result_detail = mysqli_query($conn, $query_detail);

    if (!$result_detail) {
        die("Query failed: " . mysqli_error($conn));
    }
    
    $detail = mysqli_fetch_assoc($result_detail);
} else {
    echo "ID pemeriksaan tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Riwayat Pasien</title>
</head>
<body>
    <h1>Detail Pemeriksaan Pasien</h1>
    <p>Nama Pasien: <?php echo $detail['nama']; ?></p>
    <p>Alamat: <?php echo $detail['alamat']; ?></p>
    <p>No. KTP: <?php echo $detail['no_ktp']; ?></p>
    <p>No. Telepon: <?php echo $detail['no_hp']; ?></p>
    <p>No. RM: <?php echo $detail['nomor_rekam_medis']; ?></p>
    <p>Keluhan: <?php echo $detail['keluhan']; ?></p>
    <p>Catatan Obat: <?php echo $detail['catatan_obat']; ?></p>
    <p>Biaya Pemeriksaan: <?php echo $detail['biaya_periksa']; ?></p>
</body>
</html>
