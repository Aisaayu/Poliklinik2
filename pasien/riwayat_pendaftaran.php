<?php
session_start();
include('../includes/db.php');

// Pastikan pasien sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'pasien') {
    header("Location: login_pasien.php");
    exit();
}

// Ambil data rekam medis pasien
$user_id = $_SESSION['user_id'];
$rekam_medis = getRekamMedis($user_id);  // Fungsi untuk mendapatkan rekam medis pasien

// Ambil riwayat pendaftaran poli pasien
$query = "SELECT pp.id, p.nama_poli, d.nama_dokter, pp.hari, pp.waktu_mulai, pp.waktu_selesai, pp.nomor_antrian, pp.status 
          FROM pendaftaran_poli pp
          JOIN poli1 p ON pp.id_poli = p.id_poli
          JOIN dokter3 d ON pp.id_dokter = d.id_dokter
          WHERE pp.id_rekam_medis = ? ORDER BY pp.hari DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $rekam_medis);
$stmt->execute();
$result = $stmt->get_result();
$pendaftaran_list = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendaftaran_list[] = $row;
    }
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

// Fungsi untuk mendapatkan rekam medis pasien
function getRekamMedis($user_id) {
    global $conn;
    $query = "SELECT id_rekam_medis FROM pasien WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['id_rekam_medis'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pendaftaran Poli</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2>Riwayat Pendaftaran Poli</h2>
    <?php if (empty($pendaftaran_list)) { ?>
        <p class="alert alert-warning">Anda belum mendaftar poli apapun.</p>
    <?php } else { ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Antrian</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendaftaran_list as $index => $pendaftaran) { ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $pendaftaran['nama_poli'] ?></td>
                        <td><?= $pendaftaran['nama_dokter'] ?></td>
                        <td><?= date("d-m-Y", strtotime($pendaftaran['hari'])) ?></td>
                        <td><?= $pendaftaran['mulai'] ?></td>
                        <td><?= $pendaftaran['selesai'] ?></td>
                        <td><?= $pendaftaran['antrian'] ?></td>
                        <td><?= $pendaftaran['status'] ?></td>
                        <td><a href="detail_pendaftaran.php?id=<?= $pendaftaran['id'] ?>" class="btn btn-info">Detail</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
</body>
</html>
