<?php
session_start();
include('../includes/db.php');

if (isset($_GET['dokter_id'])) {
    $dokter_id = $_GET['dokter_id'];

    $query = "SELECT * FROM jadwal_periksa WHERE dokter_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $jadwal = [];
    while ($row = $result->fetch_assoc()) {
        $jadwal[] = $row;
    }

    // Mengembalikan data dalam format JSON
    echo json_encode($jadwal);
}
?>