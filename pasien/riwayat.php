<?php
include('db.php');

// Ambil riwayat pendaftaran pasien
$query = "SELECT rp.id_riwayat, p.nama_pasien, po.nama_poli, d.nama_dokter, jd.hari, jd.jam_mulai, jd.jam_selesai, rp.nomor_antrian, rp.status 
          FROM riwayat_pendaftaran rp
          JOIN pasien p ON rp.id_pasien = p.id_pasien
          JOIN poli po ON p.id_poli = po.id_poli
          JOIN dokter d ON rp.id_dokter = d.id_dokter
          JOIN jadwal_dokter jd ON rp.id_jadwal = jd.id_jadwal";
$riwayatResult = $conn->query($query);
?>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Poli</th>
            <th>Dokter</th>
            <th>Hari</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Antrian</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $riwayatResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nama_poli']; ?></td>
                <td><?php echo $row['nama_dokter']; ?></td>
                <td><?php echo $row['hari']; ?></td>
                <td><?php echo $row['jam_mulai']; ?></td>
                <td><?php echo $row['jam_selesai']; ?></td>
                <td><?php echo $row['nomor_antrian']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><a href="detail.php?id_riwayat=<?php echo $row['id_riwayat']; ?>">Detail</a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
