<?php
session_start();
include('../includes/db.php');

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login_pasien.php");
    exit;
}

$username = $_SESSION['username'];

// Ambil data Poli
$poli_query = "SELECT * FROM poli1";
$poli_result = mysqli_query($conn, $poli_query);

// Check for database errors
if (!$poli_result) {
    die("Error retrieving poli data: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Pendaftaran Pasien Baru</title>
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
    font-size: 20px; /* Menambah ukuran font untuk tombol */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 40px; 
}
.content {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
            overflow-y: auto;
        }
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 auto;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            padding: 10px 15px;
            background-color: #4682B4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #5a9bd3;
        }    </style>
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

    <div class="main" id="mainContent">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Pendaftaran Pasien Baru</h1>
            <div class="user-info">
                Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong>
            </div>
        </div>

        <div class="content">
            <div class="form-container">
                <h2>Formulir Pendaftaran Pasien Baru</h2>
                <form action="proses_pendaftaran_pasien.php" method="POST">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>

                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>

                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat" required>

                    <label for="no_hp">Nomor HP</label>
                    <input type="text" id="no_hp" name="no_hp" required>

                    <label for="no_ktp">Nomor KTP</label>
                    <input type="text" id="no_ktp" name="no_ktp" required>

                    <label for="poli">Pilih Poli</label>
                    <select id="poli" name="poli" required>
                        <option value="">Pilih Poli</option>
                        <?php while ($row = mysqli_fetch_assoc($poli_result)): ?>
                            <option value="<?php echo $row['id_poli']; ?>"><?php echo $row['nama_poli']; ?></option>
                        <?php endwhile; ?>
                    </select>

                    <label for="dokter">Pilih Dokter</label>
                    <select id="dokter" name="dokter" required>
                        <option value="">Pilih Dokter</option>
                    </select>

                    <label for="tanggal_pendaftaran">Tanggal Pendaftaran</label>
                    <input type="date" id="tanggal_pendaftaran" name="tanggal_pendaftaran" required>

                    <label for="waktu_pendaftaran">Waktu Pendaftaran</label>
                    <input type="time" id="waktu_pendaftaran" name="waktu_pendaftaran" required>

                    <button type="submit">Daftar</button>
                    <button type="reset">Reset</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Handling dynamic doctor selection based on poli selection
        document.getElementById('poli').addEventListener('change', function() {
            var poliId = this.value;

            // Ajax request for doctors based on poli
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_dokter.php?poli_id=" + poliId, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    var dokterSelect = document.getElementById('dokter');
                    dokterSelect.innerHTML = '<option value="">Pilih Dokter</option>'; // Reset dokter options

                    var response = JSON.parse(xhr.responseText);
                    response.forEach(function(dokter) {
                        var option = document.createElement('option');
                        option.value = dokter.id_dokter;
                        option.textContent = dokter.nama_dokter;
                        dokterSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        });

        document.getElementById('toggle-button').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main');

            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('full');
        });
    </script>
</body>
</html>
