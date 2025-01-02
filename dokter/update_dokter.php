<?php
session_start();
include('../includes/db.php');

// Pastikan dokter sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'dokter') {
    header("Location: login_dokter.php");
    exit();
}

// Ambil ID dokter dari sesi
$user_id = $_SESSION['user_id'];

// Ambil data dokter beserta nama poli yang terkait
$query = "SELECT u.id, u.nama, u.gelar, u.password, u.id_poli, p.nama_poli FROM users u LEFT JOIN poli1 p ON u.id_poli = p.id_poli WHERE u.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows > 0) {
    $dokter = $result->fetch_assoc(); // Simpan hasil query ke dalam array $dokter
} else {
    // Jika data tidak ditemukan, redirect atau beri pesan error
    echo "Data dokter tidak ditemukan!";
    exit();
}

// Ambil daftar poli yang ada di database
$poli_query = "SELECT id_poli, nama_poli FROM poli1";
$poli_result = $conn->query($poli_query);
$poli_list = [];
if ($poli_result->num_rows > 0) {
    while ($row = $poli_result->fetch_assoc()) {
        $poli_list[] = $row; // Menyimpan daftar poli ke dalam array
    }
}

// Proses pembaruan data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $gelar = $_POST['gelar'];
    $password = $_POST['password'];
    $poli1 = $_POST['poli1']; // ID poli yang dipilih

    // Update data dokter
    if (!empty($password)) {
        // Jika password diubah
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET nama = ?, gelar = ?, password = ?, id_poli = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $nama, $gelar, $hashed_password, $poli1, $user_id);
    } else {
        // Jika password tidak diubah
        $query = "UPDATE users SET nama = ?, gelar = ?, id_poli = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $nama, $gelar, $poli1, $user_id);
    }

    // Eksekusi query dan cek apakah berhasil
  // Jika pembaruan berhasil, simpan data terbaru ke dalam session
if ($stmt->execute()) {
    // Update data session
    $_SESSION['user_name'] = $nama;   // Menyimpan nama dokter yang baru
    $_SESSION['user_title'] = $gelar; // Menyimpan gelar dokter yang baru
    $_SESSION['user_poli'] = $poli1;  // Menyimpan id poli yang baru

    // Redirect ke dashboard
    header("Location: dashboard_dokter.php");
    exit();
} else {
    echo "Error updating record: " . $stmt->error;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data Dokter</title>
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
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .form-container {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-box {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
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
        <a href="update_dokter.php"><i class="fas fa-user-md"></i> Profile</a>
        <a href="logout_dokter.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main" id="main-content">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Profile</h1>
            <div>
                <span class="font-semibold">Selamat datang, <?php echo htmlspecialchars($dokter['nama']); ?></span>
            </div>
        </div>

        <!-- Update Data Form -->
        <div class="form-container">
            <div class="form-box">
                <h1>Update Data Dokter</h1>
                <form action="update_dokter.php" method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($dokter['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gelar" class="form-label">Gelar</label>
                        <input type="text" class="form-control" id="gelar" name="gelar" value="<?php echo htmlspecialchars($dokter['gelar']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti password</small>
                    </div>
                    <div class="mb-3">
                        <label for="poli1" class="form-label">Poli</label>
                        <select class="form-control" id="poli1" name="poli1" required>
                            <?php
                            // Menampilkan pilihan poli yang ada
                            foreach ($poli_list as $poli) {
                                $selected = ($dokter['id_poli'] == $poli['id_poli']) ? 'selected' : ''; // Menandai poli yang saat ini dipilih
                                echo "<option value='{$poli['id_poli']}' {$selected}>{$poli['nama_poli']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Perbarui Data</button>
                </form>
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
