<?php
include('../includes/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pasien = $_POST['id_pasien'];
    $keluhan = $_POST['keluhan'];
    $catatan = $_POST['catatan'];
    $biaya_periksa = $_POST['biaya_periksa'];
    $poli = $_POST['poli'];
    $dokter_id = $_SESSION['user_id']; // ID dokter yang sedang login

    // Simpan data ke tabel `riwayat_periksa`
    $query_insert = "
        INSERT INTO riwayat_periksa (id_pasien, tanggal_periksa, keluhan, catatan, biaya_periksa, poli, dokter)
        VALUES ('$id_pasien', NOW(), '$keluhan', '$catatan', '$biaya_periksa', '$poli', '$dokter_id')
    ";
    $result = mysqli_query($conn, $query_insert);

    if ($result) {
        echo "Data berhasil disimpan ke riwayat periksa.";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
}
?>

<!-- Form untuk Pemeriksaan Pasien -->
<form method="POST">
    <input type="hidden" name="id_pasien" value="<?= $id_pasien; ?>">
    <label>Keluhan:</label>
    <textarea name="keluhan" required></textarea>
    <label>Catatan Dokter:</label>
    <textarea name="catatan" required></textarea>
    <label>Biaya Periksa:</label>
    <input type="number" name="biaya_periksa" step="0.01" required>
    <label>Poli:</label>
    <input type="text" name="poli" required>
    <button type="submit">Simpan</button>
</form>
