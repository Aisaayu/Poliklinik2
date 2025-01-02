<?php
include('../includes/db.php');

// Periksa apakah ada parameter poli_id
if (isset($_GET['poli_id'])) {
    $poli_id = $_GET['poli_id'];

    // Ambil dokter berdasarkan poli
    $dokter_query = "SELECT id, nama_dokter, jadwal_praktek FROM dokter3 WHERE id_poli = ?";
    $stmt = $conn->prepare($dokter_query);
    $stmt->bind_param("i", $poli_id);  // Binding parameter untuk mencegah SQL injection
    $stmt->execute();
    $result = $stmt->get_result();

    // Menyusun data dokter ke dalam array
    $dokter_list = [];
    while ($row = $result->fetch_assoc()) {
        $dokter_list[] = $row;  // Menambahkan dokter ke dalam array
    }

    // Kirimkan response dalam format JSON
    echo json_encode($dokter_list);
}
?>
