<?php
session_start();
include('../includes/db.php');

// Cek apakah pengguna telah login sebagai pasien
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'pasien') {
    header("Location: login_pasien.php");
    exit();
}

$username = $_SESSION['username'];

// Fungsi untuk mendapatkan nomor rekam medis pasien berdasarkan username
function getRekamMedis($username)
{
    global $conn;
    $sql = "SELECT nomor_rekam_medis FROM pasien WHERE nama = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "<script>alert('Kesalahan pada query: " . $conn->error . "');</script>";
        return null;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($rekam_medis);
    $stmt->fetch();
    $stmt->close();

    return $rekam_medis;
}

$rekam_medis = getRekamMedis($username);
if (!$rekam_medis) {
    echo "<script>alert('Nomor rekam medis tidak ditemukan. Harap hubungi administrasi.');</script>";
    exit();
}

// Ambil daftar poli
$poli_query = "SELECT id_poli, nama_poli FROM poli1";
$poli_result = $conn->query($poli_query);
$poli_list = [];
if ($poli_result->num_rows > 0) {
    while ($row = $poli_result->fetch_assoc()) {
        $poli_list[] = $row;
    }
}

if (isset($_POST['submit'])) {
    $dokter_id = $_POST['dokter_id'] ?? null;
    $poli_id = $_POST['poli_id'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $waktu_mulai = $_POST['waktu_mulai'] ?? null;
    $waktu_selesai = $_POST['waktu_selesai'] ?? null;
    $keluhan = $_POST['keluhan'] ?? null;

    if (!$dokter_id || !$poli_id || !$tanggal || !$waktu_mulai || !$waktu_selesai || !$keluhan) {
        echo "<script>alert('Semua field harus diisi.');</script>";
        exit();
    }

    // Konversi tanggal menjadi hari
    $timestamp = strtotime($tanggal);
    $hari_periksa = strtolower(date('l', $timestamp));
    $waktu_mulai = date('H:i:s', strtotime($waktu_mulai));
    $waktu_selesai = date('H:i:s', strtotime($waktu_selesai));

    // Cek validitas jadwal
    $query_jadwal = "SELECT id FROM jadwal_periksa WHERE dokter_id = ? AND poli_id = ? AND hari_periksa = ? AND waktu_mulai <= ? AND waktu_selesai >= ?";
    $stmt_jadwal = $conn->prepare($query_jadwal);
    $stmt_jadwal->bind_param("iisss", $dokter_id, $poli_id, $hari_periksa, $waktu_mulai, $waktu_selesai);
    $stmt_jadwal->execute();
    $result = $stmt_jadwal->get_result();

    if ($result->num_rows > 0) {
        // Jadwal valid, lakukan pendaftaran
        $jadwal = $result->fetch_assoc();
        $jadwal_id = $jadwal['id'];

        $query_pasien = "SELECT id FROM pasien WHERE nomor_rekam_medis = ?";
        $stmt_pasien = $conn->prepare($query_pasien);
        $stmt_pasien->bind_param("s", $rekam_medis);
        $stmt_pasien->execute();
        $result_pasien = $stmt_pasien->get_result();
        $pasien = $result_pasien->fetch_assoc();

        if ($pasien) {
            $pasien_id = $pasien['id'];
            $status = 'belum diperiksa';

            $insert = "INSERT INTO pendaftaran_poli (pasien_id, poli_id, dokter_id, jadwal_id, keluhan, status) 
                       VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($insert);
            $stmt_insert->bind_param("iiisss", $pasien_id, $poli_id, $dokter_id, $jadwal_id, $keluhan, $status);

            if ($stmt_insert->execute()) {
                echo "<script>alert('Pendaftaran berhasil!');</script>";
            } else {
                echo "<script>alert('Gagal menyimpan pendaftaran.');</script>";
            }
        } else {
            echo "<script>alert('Pasien tidak ditemukan.');</script>";
        }
    } else {
        echo "<script>alert('Jadwal tidak valid. Silakan pilih jadwal yang sesuai.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Pendaftaran Poli</title>
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
.table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 0 10px;
}

.table th, .table td {
    padding: 10px 15px; 
    border: 1px solid #ddd; 
}

.table th {
    background-color: #4682B4;
    color: white;
    text-align: center;
}

.table td {
    text-align: center;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f2f2f2; 
}

.table .btn {
    padding: 5px 10px;
    font-size: 14px;
}

.table td button {
    margin-right: 5px;
}
#popupCard {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    max-width: 600px;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    display: none; /* Memastikan pop-up tidak terlihat saat pertama kali */
}

.popup-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%; /* Pastikan card menyesuaikan lebar sesuai dengan container */
}


    .popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .popup-header h5 {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .popup-body {
        max-height: 400px;
        overflow-y: auto;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
        display: block;
    }
    .form-group .form-control {
        width: 100%;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
     /* Button styling */
     button[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* Close button styling */
    .btn-close {
        font-size: 1.5rem;
        background: none;
        border: none;
        cursor: pointer;
    }

    /* Overlay background */
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
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

    <div class="main" id="mainContent">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Pendaftaran Poli</h1>
            <div class="user-info">
                Selamat Datang, <strong><?php echo htmlspecialchars($username); ?></strong>
            </div>
        </div>
        <!-- Content -->
        <div class="content">
            <div class="container">
               <!-- Button untuk menambah pendaftaran yang akan membuka pop-up card -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" id="openPopupBtn">
    Tambah Pendaftaran
</button>
<div id="overlay"></div>
<!-- Pop-up Card untuk Tambah Pendaftaran -->
<div id="popupCard" style="display: none;">
                    <div class="popup-card">
                        <div class="popup-header">
                            <h2>Daftar Poli</h2>
                            <button id="closePopupBtn" class="btn-close">×</button>
                        </div>
                        <div class="popup-body">
                            <form action="pendaftaran_poli.php" method="POST">
                                <div class="form-group">
                                    <label for="rekam_medis">Nomor Rekam Medis:</label>
                                    <input type="text" name="rekam_medis" class="form-control" value="<?= $rekam_medis; ?>" readonly>
                                </div>
                <div class="form-group">
                    <label for="poli_id">Pilih Poli:</label>
                    <select name="poli_id" class="form-control" required id="poli_id">
                        <option value="">Pilih Poli</option>
                        <?php foreach ($poli_list as $poli) { ?>
                            <option value="<?= $poli['id_poli'] ?>"><?= $poli['nama_poli'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dokter_id">Pilih Dokter:</label>
                    <select name="dokter_id" class="form-control" required id="dokter_id">
                        <option value="">Pilih Dokter</option>
                    </select>

                </div>

                <div class="form-group">
                    <label for="hari">Hari:</label>
                    <input type="text" name="hari" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="waktu_mulai">Jam Mulai:</label>
                    <input type="time" name="waktu_mulai" required class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="waktu_selesai">Jam Selesai:</label>
                    <input type="time" name="waktu_selesai" required class="form-control" readonly>
                </div>
                <div class="form-group">
                    <label for="keluhan">Keluhan:</label>
                    <textarea name="keluhan" class="form-control" rows="3" required></textarea>
                </div>
                <input type="hidden" name="jadwal_id" value="" id="jadwal_id">
<input type="hidden" name="nomor_antrian" value="" id="nomor_antrian">
                                <button type="submit" class="btn btn-primary" name="submit">Tambah</button>
            </form>
        </div>
    </div>
</div>

                <!-- Tabel Jadwal Periksa -->
                <table class="table table-bordered">
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
        <?php
        $no = 1;
        $query_tampil_pendaftaran = "SELECT * FROM pendaftaran_poli pp
                                    JOIN poli1 p ON pp.poli_id = p.id_poli
                                    JOIN dokter3 d ON pp.dokter_id = d.id_dokter";
        $result_pendaftaran = $conn->query($query_tampil_pendaftaran);
        
        while ($pendaftaran = $result_pendaftaran->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($pendaftaran['nama_poli']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['nama_dokter']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['hari_periksa']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['waktu_mulai']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['waktu_selesai']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['nomor_antrian']) . "</td>
                    <td>" . htmlspecialchars($pendaftaran['status']) . "</td>
                    <td>
                        <button class='btn btn-info btn-sm'>Detail</button>
                    </td>
                </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

            </div>
        </div>
    </div>

    <script>
    // Toggle sidebar
    const toggleButton = document.getElementById('toggle-button');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('full');
    });
    document.getElementById('openPopupBtn').addEventListener('click', function() {
        document.getElementById('popupCard').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    });

    // Close the pop-up card when the close button is clicked
    document.getElementById('closePopupBtn').addEventListener('click', function() {
        document.getElementById('popupCard').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    });

    // Close the pop-up card when clicking the overlay area
    document.getElementById('overlay').addEventListener('click', function() {
        document.getElementById('popupCard').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    });
    
    document.getElementById('poli_id').addEventListener('change', function() {
    var poliId = this.value;  // Ambil nilai poli yang dipilih

    // Pastikan poli dipilih sebelum melakukan request
    if (poliId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_dokter.php?poli_id=' + poliId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);  // Mengambil data dokter

                var dokterSelect = document.getElementById('dokter_id');
                dokterSelect.innerHTML = '<option value="">Pilih Dokter</option>'; // Reset opsi dokter

                // Isi dropdown dokter dengan dokter yang aktif pada poli tersebut
                response.forEach(function(dokter) {
                    var option = document.createElement('option');
                    option.value = dokter.id_dokter;
                    option.textContent = dokter.nama_dokter;
                    dokterSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    }
});
document.getElementById('dokter_id').addEventListener('change', function() {
    var dokterId = this.value; // Ambil nilai dokter yang dipilih

    if (dokterId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_jadwal.php?dokter_id=' + dokterId, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText); // Ambil data jadwal

                var hariInput = document.querySelector('input[name="hari"]');
                var waktuMulaiInput = document.querySelector('input[name="waktu_mulai"]');
                var waktuSelesaiInput = document.querySelector('input[name="waktu_selesai"]');
                var jadwalIdInput = document.getElementById('jadwal_id');

                // Reset input sebelum mengisi data baru
                hariInput.value = '';
                waktuMulaiInput.value = '';
                waktuSelesaiInput.value = '';
                jadwalIdInput.value = ''; // Reset jadwal_id

                // Jika ada jadwal, ambil yang pertama
                if (response.length > 0) {
                    hariInput.value = response[0].hari_periksa || ''; // Ambil hari
                    waktuMulaiInput.value = response[0].waktu_mulai || ''; // Ambil waktu mulai
                    waktuSelesaiInput.value = response[0].waktu_selesai || ''; // Ambil waktu selesai
                    jadwalIdInput.value = response[0].id; // Set jadwal_id
                }
            }
        };
        xhr.send();
    }
});
</script>

</body>
</html>

