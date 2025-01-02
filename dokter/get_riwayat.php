<?php
include('../includes/db.php');

// Ambil parameter id pasien dari URL
$idPasien = $_GET['id'];

// Query untuk mengambil riwayat pemeriksaan pasien
$query = "SELECT * FROM pemeriksaan WHERE id_pasien = '$idPasien'";
$result = mysqli_query($conn, $query);

// Cek apakah ada data
if (mysqli_num_rows($result) > 0) {
    $riwayat = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Menyusun data dalam array
        $riwayat[] = [
            'tanggal_periksa' => $row['tanggal_periksa'],
            'nama_pasien' => $row['nama_pasien'],
            'nama_dokter' => $row['nama_dokter'],
            'keluhan' => $row['keluhan'],
            'catatan' => $row['catatan'],
            'obat' => $row['obat'],
            'biaya_periksa' => $row['biaya_periksa']
        ];
    }
    // Mengirim data dalam format JSON
    echo json_encode($riwayat);
} else {
    // Jika tidak ada data, kirimkan array kosong
    echo json_encode([]);
}
?>
