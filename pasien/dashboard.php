<?php 
session_start();

// Periksa apakah pengguna telah login
if (!isset($_SESSION['username'])) {
    header("Location: login_pasien.php");
    exit;
}

$username = $_SESSION['username'];

// Koneksi ke database
$host = 'localhost';
$dbname = 'poliklinik';
$username_db = 'root'; // Ganti dengan username database Anda
$password_db = ''; // Ganti dengan password database Anda
$conn = new mysqli($host, $username_db, $password_db, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mendapatkan jumlah pasien
$query_pasien = "SELECT COUNT(*) AS jumlah_pasien FROM pasien";
$result_pasien = $conn->query($query_pasien);
$row_pasien = $result_pasien->fetch_assoc();
$jumlah_pasien = $row_pasien['jumlah_pasien'];

// Query untuk mendapatkan jumlah dokter aktif
$query_dokter = "SELECT COUNT(*) AS jumlah_dokter FROM dokter3 WHERE status = 'aktif'";
$result_dokter = $conn->query($query_dokter);
$row_dokter = $result_dokter->fetch_assoc();
$jumlah_dokter = $row_dokter['jumlah_dokter'];

// Query untuk mendapatkan jumlah poli
$query_poli = "SELECT COUNT(*) AS jumlah_poli FROM poli1";
$result_poli = $conn->query($query_poli);
$row_poli = $result_poli->fetch_assoc();
$jumlah_poli = $row_poli['jumlah_poli'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <title>Dashboard Pasien</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 40px;
        }
        .content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .welcome-message {
    font-size: 30px;
    text-align: center;  /* Menyusun teks di tengah secara horizontal */
    display: flex;
    font-weight: bold;
    justify-content: center; /* Menyusun teks di tengah secara horizontal */
    align-items: center; /* Menyusun teks di tengah secara vertikal */
    height: 100px; /* Tentukan tinggi agar teks terlihat di tengah secara vertikal */
}
        .info-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
            margin-right: 20px;
        }
        .info-card:last-child {
            margin-right: 0;
        }
        .info-card h3 {
            margin: 0;
            color: #4682B4;
        }
        .info-card p {
            font-size: 24px;
            margin: 10px 0;
        }
        .card-chart {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .card-chart canvas {
            width: 100%;
            height: 300px; /* Ukuran grafik yang lebih kecil */
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2><i class="fas fa-hospital"></i> Poli Klinik</h2>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="pendaftaran_pasien_baru.php"><i class="fas fa-user-plus"></i> Pendaftaran Pasien Baru</a>
        <a href="../pasien/pendaftaran_poli.php"><i class="fas fa-clinic-medical"></i> Pendaftaran Poli</a>
        <a href="logout_pasien.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main" id="mainContent">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Dashboard Pasien</h1>
            <div class="user-info">
                Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="welcome-message">Selamat Datang di Sistem Pendaftaran Pasien Poliklinik</p>

            <!-- Info Boxes -->
            <div class="info-box">
                <div class="info-card">
                    <h3>Jumlah Pasien</h3>
                    <p><?php echo $jumlah_pasien; ?></p>
                </div>
                <div class="info-card">
                    <h3>Dokter Aktif</h3>
                    <p><?php echo $jumlah_dokter; ?></p>
                </div>
                <div class="info-card">
                    <h3>Jumlah Poli</h3>
                    <p><?php echo $jumlah_poli; ?></p>
                </div>
            </div>

            <!-- Grafik -->
            <div class="card-chart">
                <h3>Statistik Klinik</h3>
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for toggling sidebar
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleButton = document.getElementById('toggle-button');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('full');
        });

        // Chart.js for Dashboard Stats
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jumlah Pasien', 'Dokter Aktif', 'Jumlah Poli'],
                datasets: [{
                    label: 'Data Klinik',
                    data: [<?php echo $jumlah_pasien; ?>, <?php echo $jumlah_dokter; ?>, <?php echo $jumlah_poli; ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
