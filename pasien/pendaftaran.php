<?php
include('db.php');

// Ambil data poli dan dokter
$query = "SELECT * FROM poli";
$poliResult = $conn->query($query);

// Jika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pasien = $_POST['nama_pasien'];
    $no_ktp = $_POST['no_ktp'];
    $id_poli = $_POST['poli'];
    $id_dokter = $_POST['dokter'];
    $id_jadwal = $_POST['jadwal'];
    $keluhan = $_POST['keluhan'];

    // Generate nomor rekam medis (no_rm)
    $no_rm = "RM-" . rand(1000, 9999);

    // Masukkan data pasien
    $stmt = $conn->prepare("INSERT INTO pasien (nama_pasien, no_ktp, no_rm, id_poli, keluhan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_pasien, $no_ktp, $no_rm, $id_poli, $keluhan);
    $stmt->execute();

    // Ambil ID pasien yang baru saja didaftarkan
    $id_pasien = $conn->insert_id;

    // Masukkan riwayat pendaftaran dengan nomor antrian
    $nomor_antrian = rand(1, 100);
    $stmt = $conn->prepare("INSERT INTO riwayat_pendaftaran (id_pasien, id_dokter, id_jadwal, nomor_antrian) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_pasien, $id_dokter, $id_jadwal, $nomor_antrian);
    $stmt->execute();

    echo "Pendaftaran berhasil! Nomor Rekam Medis Anda: $no_rm, Nomor Antrian: $nomor_antrian";
}
?>

<form method="POST">
    <label>Nama Pasien</label><br>
    <input type="text" name="nama_pasien" required><br>

    <label>No KTP</label><br>
    <input type="text" name="no_ktp" required><br>

    <label>Pilih Poli</label><br>
    <select name="poli" id="poli" required>
        <?php while ($poli = $poliResult->fetch_assoc()) { ?>
            <option value="<?php echo $poli['id_poli']; ?>"><?php echo $poli['nama_poli']; ?></option>
        <?php } ?>
    </select><br>

    <label>Pilih Dokter</label><br>
    <select name="dokter" id="dokter" required></select><br>

    <label>Pilih Jadwal</label><br>
    <select name="jadwal" id="jadwal" required></select><br>

    <label>Keluhan</label><br>
    <textarea name="keluhan" required></textarea><br>

    <button type="submit">Daftar</button>
</form>

<script>
// Mengisi dokter berdasarkan poli yang dipilih
document.getElementById('poli').addEventListener('change', function() {
    var poliId = this.value;
    fetch('get_dokter.php?poli_id=' + poliId)
        .then(response => response.json())
        .then(data => {
            var dokterSelect = document.getElementById('dokter');
            dokterSelect.innerHTML = '';
            data.forEach(dokter => {
                var option = document.createElement('option');
                option.value = dokter.id_dokter;
                option.textContent = dokter.nama_dokter;
                dokterSelect.appendChild(option);
            });
        });
});

// Mengisi jadwal berdasarkan dokter yang dipilih
document.getElementById('dokter').addEventListener('change', function() {
    var dokterId = this.value;
    fetch('get_jadwal.php?dokter_id=' + dokterId)
        .then(response => response.json())
        .then(data => {
            var jadwalSelect = document.getElementById('jadwal');
            jadwalSelect.innerHTML = '';
            data.forEach(jadwal => {
                var option = document.createElement('option');
                option.value = jadwal.id_jadwal;
                option.textContent = jadwal.hari + ' ' + jadwal.jam_mulai + ' - ' + jadwal.jam_selesai;
                jadwalSelect.appendChild(option);
            });
        });
});
</script>
