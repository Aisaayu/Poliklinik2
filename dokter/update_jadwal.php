<?php
// Ambil data dari form
$jadwal_id = $_POST['id'];
$dokter_id = $_POST['dokter_id'];
$tanggal_periksa = $_POST['tanggal_periksa'];
$waktu = $_POST['waktu'];
$jam_selesai = $_POST['jam_selesai'];
$status = ($_POST['status'] == 'aktif') ? 1 : 0;

// Validasi apakah jadwal yang diubah tumpang tindih dengan jadwal lain
$query = "SELECT * FROM jadwal_periksa 
          WHERE dokter_id = ? 
          AND tanggal_periksa = ? 
          AND id != ? 
          AND (
              (waktu >= ? AND waktu < ?) OR 
              (jam_selesai > ? AND jam_selesai <= ?)
          )";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssss", $dokter_id, $tanggal_periksa, $jadwal_id, $waktu, $jam_selesai, $waktu, $jam_selesai);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Jadwal ini bertabrakan dengan jadwal yang sudah ada!";
    exit();
}

// Update jadwal
$query = "UPDATE jadwal_periksa 
          SET dokter_id = ?, tanggal_periksa = ?, waktu = ?, jam_selesai = ?, is_aktif = ? 
          WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $dokter_id, $tanggal_periksa, $waktu, $jam_selesai, $status, $jadwal_id);
$stmt->execute();

// Beri notifikasi bahwa jadwal berhasil diupdate
echo "Jadwal berhasil diperbarui!";
?>
