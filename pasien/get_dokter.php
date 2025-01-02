<?php
include('../includes/db.php');

if (isset($_GET['poli_id'])) {
    $poli_id = intval($_GET['poli_id']);
    $query = "SELECT id_dokter, nama_dokter FROM dokter3 WHERE id_poli = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $poli_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dokter_list = [];

    while ($row = $result->fetch_assoc()) {
        $dokter_list[] = $row;
    }

    echo json_encode($dokter_list);
} else {
    echo json_encode([]);
}
error_log("Poli ID: " . $poli_id);
error_log(print_r($dokter_list, true));

?>
