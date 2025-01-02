<?php
session_start(); // Memulai sesi

// Periksa apakah pengguna sudah login dan memiliki peran "dokter"
if (!isset($_SESSION['user_id'])) {
    echo "Sesi tidak tersedia. Silakan login ulang.";
    header("Location: login_dokter.php");
    exit();
}

include('../includes/db.php');

// Ambil informasi dokter berdasarkan sesi
$dokter_id = $_SESSION['user_id'];
$query = "SELECT * FROM dokter3 WHERE id_dokter = '$dokter_id'";
$result = mysqli_query($conn, $query);
$nama_dokter = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Nama tidak tersedia';
$gelar_dokter = isset($_SESSION['user_title']) ? $_SESSION['user_title'] : 'Gelar tidak tersedia';
$id_poli = isset($_SESSION['user_poli']) ? $_SESSION['user_poli'] : 'Poli tidak tersedia';

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Data dokter tidak ditemukan. Silakan periksa sesi login.";
    exit();
}

$dokter = mysqli_fetch_assoc($result);

// Ambil daftar pasien yang dijadwalkan untuk hari ini
$tanggal_hari_ini = date('Y-m-d');
$jadwal_query = "SELECT p.nama AS nama_pasien, j.waktu, p.no_hp, p.alamat
                 FROM jadwal j
                 JOIN pasien p ON j.id = p.id
                 WHERE j.dokter_id = '$dokter_id' AND j.tanggal = '$tanggal_hari_ini'";
$jadwal_result = mysqli_query($conn, $jadwal_query);

$query = "SELECT nama_poli FROM poli1 WHERE id_poli = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_poli);
$stmt->execute();
$poli_result = $stmt->get_result();
$poli = $poli_result->fetch_assoc();
$nama_poli = $poli ? $poli['nama_poli'] : 'Poli tidak tersedia';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 250px;
            background-color: #e8f0fe;
            height: 100vh;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .sidebar h2 {
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: #333;
            text-decoration: none;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-left: 250px;
            overflow-y: auto;
            transition: margin-left 0.3s ease-in-out;
        }
        .main.full {
            margin-left: 0;
        }
        .header {
            background-color: #4682B4;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
        }
        .toggle-sidebar {
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
        }
        .content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .card-deck {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px 0;
}
.card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.3s;
}
        .card:hover {
            transform: scale(1.05);
        }
        .card-header {
    background-color: #4682B4;
    color: white;
    padding: 10px;
    font-size: 20px;
}
        .card-body {
            padding: 15px;
            font-size: 16px;
        }
        .card-footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: right;
        }
        .card-footer a {
            color: #4682B4;
            text-decoration: none;
        }
        /* Styling untuk Card Aksi Lainnya */
        .card-aksi-lainnya {
    grid-column: span 2; /* Membuat kartu ini lebih lebar, span 2 kolom */
}

.card.aksi-lainnya .card-body {
    display: flex;
    flex-direction: column;
    gap: 100px; /* Memberikan jarak antar tombol */
}

.card.aksi-lainnya .btn {
    padding: 15px 0; /* Memberikan padding vertikal pada tombol agar lebih besar */
    font-size: 20px; /* Menambah ukuran font pada tombol agar lebih mudah dibaca */
    text-align: center;
}

    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2><i class="fas fa-hospital"></i> Poli Klinik</h2>
        <a href="dashboard_dokter.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="jadwal_periksa.php"><i class="fas fa-calendar-check"></i> Jadwal Periksa</a>
        <a href="periksa_pasien.php"><i class="fas fa-user-injured"></i> Periksa Pasien</a>
        <a href="riwayat_pasien.php"><i class="fas fa-history"></i> Riwayat Pasien</a>
        <a href="../dokter/update_dokter.php"><i class="fas fa-user-md"></i> Profile</a>
        <a href="logout_dokter.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main" id="main-content">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Dashboard Dokter</h1>
            <div>
                <span class="font-semibold">Selamat datang, <?php echo htmlspecialchars($dokter['nama_dokter']); ?></span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="content">
            <div class="card-deck">
                <!-- Informasi Dokter Card -->
                <div class="card">
                    <div class="card-header">Informasi Dokter</div>
                    <div class="card-body">
                    <h6><strong>Nama:</strong> <?php echo htmlspecialchars($nama_dokter); ?></h6>
                    <p><strong>Gelar:</strong> <?php echo htmlspecialchars($gelar_dokter); ?></p>
                    <p><strong>Poli:</strong><?php echo htmlspecialchars($nama_poli); ?></p>
                    <p><strong>No. HP:</strong> <?php echo htmlspecialchars($dokter['no_hp']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($dokter['email']); ?></p>
                    </div>
                </div>

                <!-- Jadwal Hari Ini Card -->
                <div class="card">
                    <div class="card-header">Jadwal Hari Ini</div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($jadwal_result) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Pasien</th>
                                        <th>Waktu</th>
                                        <th>No. HP</th>
                                        <th>Alamat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($jadwal = mysqli_fetch_assoc($jadwal_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($jadwal['nama_pasien']); ?></td>
                                            <td><?php echo htmlspecialchars($jadwal['waktu']); ?></td>
                                            <td><?php echo htmlspecialchars($jadwal['no_hp']); ?></td>
                                            <td><?php echo htmlspecialchars($jadwal['alamat']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>Tidak ada jadwal pasien untuk hari ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Aksi Lainnya Card -->
<div class="card card-aksi-lainnya">
    <div class="card-header">Aksi Lainnya</div>
    <div class="card-body">
        <a href="input_rekam_medis.php" class="btn btn-primary btn-block">Input Rekam Medis</a>
        <a href="jadwal_periksa.php" class="btn btn-success btn-block">Lihat Jadwal Saya</a>
        <a href="../dokter/update_dokter.php" class="btn btn-warning btn-block">Ubah Profil</a>
    </div>
</div>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('toggle-button');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('full');
        });
    </script>
</body>
</html>