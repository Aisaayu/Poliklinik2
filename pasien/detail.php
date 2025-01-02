<?php
include('db.php');
$id_riwayat = $_GET['id_riwayat'];

// Ambil data riwayat pendaftaran
$query = "SELECT rp.id_riwayat, p.nama_pasien, po.nama_poli, d.nama_dokter, jd.hari, jd.jam_mulai, jd.jam_selesai, rp.nomor_antrian, rp.status, p.keluhan 
          FROM riwayat_pendaftaran rp
          JOIN pasien p ON rp.id_pasien = p.id_pasien
          JOIN poli po ON p.id_poli = po.id_poli
          JOIN dokter d ON rp.id_dokter = d.id_dokter
          JOIN jadwal_dokter jd ON rp.id_jadwal = jd.id_jadwal
          WHERE rp.id_riwayat = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_riwayat);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<h3>Detail Pendaftaran</h3>
<p><strong>Nama Pasien:</strong> <?php echo $row['nama_pasien']; ?></p>
<p><strong>Poli:</strong> <?php echo $row['nama_poli']; ?></p>
<p><strong>Dokter:</strong> <?php echo $row['nama_dokter']; ?></p>
<p><strong>Hari:</strong> <?php echo $row['hari']; ?></p>
<p><strong>Jam Mulai:</strong> <?php echo $row['jam_mulai']; ?></p>
<p><strong>Jam Selesai:</strong> <?php echo $row['jam_selesai']; ?></p>
<p><strong>Nomor Antrian:</strong> <?php echo $row['nomor_antrian']; ?></p>
<p><strong>Status:</strong> <?php echo $row['status']; ?></p>
<p><strong>Keluhan:</strong> <?php echo $row['keluhan']; ?></p>
