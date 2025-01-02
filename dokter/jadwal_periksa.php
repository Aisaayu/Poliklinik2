<?php
include('../includes/db.php');

session_start();

$dokter_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Gantilah dengan session ID yang sesuai

if (isset($_POST['submit'])) {
    $hari_periksa = $_POST['hari_periksa'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $status = $_POST['status'];

    $query_check = "SELECT * FROM jadwal_periksa WHERE dokter_id = ? AND hari_periksa = ? AND status = 'aktif' 
    AND ((waktu_mulai BETWEEN ? AND ?) OR (waktu_selesai BETWEEN ? AND ?))";
 
 $stmt_check = $conn->prepare($query_check);
    $stmt_check->bind_param("issssss", $dokter_id, $hari_periksa, $waktu_mulai, $waktu_selesai, $waktu_mulai, $waktu_selesai);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Schedule conflict found
        echo "<script>Swal.fire('Error', 'Jadwal periksa ini bertabrakan dengan jadwal yang sudah ada.', 'error');</script>";
    } else {
        // Insert new schedule
        $query_insert = "INSERT INTO jadwal_periksa (dokter_id, hari_periksa, waktu_mulai, waktu_selesai, status) 
                         VALUES (?, ?, ?, ?, ?)";
        
     
$stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("issss", $dokter_id, $hari_periksa, $waktu_mulai, $waktu_selesai, $status);
        if ($stmt_insert->execute()) {
            echo "<script>Swal.fire('Success', 'Jadwal periksa berhasil ditambahkan.', 'success');</script>";
        } 
        
else {
            echo "<script>Swal.fire('Error', 'Gagal menambahkan jadwal periksa.', 'error');</script>";
        }
    }
}

// Query to display the doctor's schedules
$query_jadwal = "SELECT jp.*, d.nama_dokter FROM jadwal_periksa jp JOIN dokter3 d ON jp.dokter_id = d.id_dokter WHERE jp.dokter_id = ?";
$stmt_jadwal = $conn->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $dokter_id);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();
$query_dokter_info = "SELECT * FROM dokter3 WHERE id_dokter = ?";
$stmt_dokter = $conn->prepare($query_dokter_info);
$stmt_dokter->bind_param("i", $dokter_id);
$stmt_dokter->execute();
$result_dokter_info = $stmt_dokter->get_result();

// Debugging: Cek apakah ada hasil
if ($result_dokter_info->num_rows > 0) {
    // Ambil data dokter
    $dokter = $result_dokter_info->fetch_assoc();
} else {
    echo "Dokter tidak ditemukan. Query: $query_dokter_info, ID Dokter: $dokter_id";
    exit();
}

// Query untuk menampilkan semua jadwal periksa dokter
$query_jadwal = "
    SELECT jp.*, d.nama_dokter 
    FROM jadwal_periksa jp
    JOIN dokter3 d ON jp.dokter_id = d.id_dokter
    WHERE jp.dokter_id = ?
";
$stmt_jadwal = $conn->prepare($query_jadwal);
$stmt_jadwal->bind_param("i", $dokter_id);
$stmt_jadwal->execute();
$result_jadwal = $stmt_jadwal->get_result();
$hari_periksa = isset($jadwal['hari_periksa']) ? $jadwal['hari_periksa'] : ''; 
echo $hari_periksa;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Periksa Dokter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .table th, .table td {
            text-align: center;
        }
        .table th {
            background-color: #4682B4;
            color: white;
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
            <h1>Daftar Jadwal Periksa Dokter</h1>
            <div>
                <span class="font-semibold">Selamat datang, <?php echo htmlspecialchars($dokter['nama_dokter']); ?></span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="container">
                <!-- Button untuk menambah jadwal yang akan membuka modal -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                    Tambah Jadwal Periksa
                </button>

               <!-- Modal Tambah Jadwal -->
               <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addScheduleModalLabel">Tambah Jadwal Periksa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="input_jadwal.php" method="POST">
                                    <div class="form-group">
                                        <label for="hari_periksa">Hari Periksa:</label>
                                        <select name="hari_periksa" class="form-control" required>
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                            <option value="Minggu">Minggu</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="waktu_mulai">Jam Mulai:</label>
                                        <input type="time" name="waktu_mulai" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="waktu_selesai">Jam Selesai:</label>
                                        <input type="time" name="waktu_selesai" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status:</label>
                                        <select name="status" required class="form-control">
                                            <option value="aktif">Aktif</option>
                                            <option value="nonaktif">Nonaktif</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="submit">Tambah Jadwal</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Jadwal Periksa -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Dokter</th>
                            <th>Hari</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($jadwal = $result_jadwal->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$no}</td>
                                    <td>" . htmlspecialchars($jadwal['nama_dokter']) . "</td>
                                    <td>" . htmlspecialchars($jadwal['hari_periksa']) . "</td>
                                    <td>" . htmlspecialchars($jadwal['waktu_mulai']) . "</td>
                                    <td>" . htmlspecialchars($jadwal['waktu_selesai']) . "</td>
                                    <td>" . ucfirst($jadwal['status']) . "</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm edit-button' 
                                                data-id='{$jadwal['id']}' 
                                                data-hari='{$jadwal['hari_periksa']}' 
                                                data-mulai='{$jadwal['waktu_mulai']}' 
                                                data-selesai='{$jadwal['waktu_selesai']}' 
                                                data-status='{$jadwal['status']}' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editScheduleModal'>
                                            <i class='fas fa-edit'></i> Edit
                                        </button>
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

    <!-- Modal Edit Jadwal -->
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editScheduleModalLabel">Edit Jadwal Periksa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="edit_jadwal.php" method="POST">
                    <!-- ID Jadwal -->
                    <input type="hidden" name="id" id="id">

                    <!-- Hari Periksa (Tampilkan Hari Periksa yang Sudah Dipilih) -->
                    <div class="form-group">
                        <label for="hari_periksa">Hari:</label>
                        <!-- Input untuk menampilkan hari periksa yang sudah dipilih -->
                        <input type="text" class="form-control" id="hari_periksa_display" readonly>
                        <!-- Input tersembunyi untuk mengirimkan nilai hari periksa -->
                        <input type="hidden" name="hari_periksa" id="hari_periksa_hidden" value="<?php echo htmlspecialchars($hari_periksa, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <!-- Waktu Mulai -->
                    <div class="form-group">
                        <label for="waktu_mulai">Waktu Mulai:</label>
                        <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" readonly>
                    </div>

                    <!-- Waktu Selesai -->
                    <div class="form-group">
                        <label for="waktu_selesai">Waktu Selesai:</label>
                        <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" readonly>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select class="form-control" name="status" id="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Pastikan nilai hari_periksa diteruskan dengan benar ke input teks
    document.getElementById('hari_periksa_display').value = "<?php echo htmlspecialchars($hari_periksa, ENT_QUOTES, 'UTF-8'); ?>";
</script>


         
<script>
     document.getElementById('toggle-button').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var mainContent = document.getElementById('main-content');

            // Toggle class 'hidden' pada sidebar
            sidebar.classList.toggle('hidden');

            // Toggle margin-left pada main content
            mainContent.classList.toggle('full');
        });
    // Skrip untuk memuat data ke dalam modal edit
    document.addEventListener("DOMContentLoaded", function() {
        const editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const hari = this.getAttribute('data-hari');
                const mulai = this.getAttribute('data-mulai');
                const selesai = this.getAttribute('data-selesai');
                const status = this.getAttribute('data-status');

                document.getElementById('id').value = id;
                document.getElementById('hari_periksa_hidden').value = hari;
                document.getElementById('waktu_mulai').value = mulai;
                document.getElementById('waktu_selesai').value = selesai;
                document.getElementById('status').value = status;
                document.getElementById('hari_periksa_display').value = "<?php echo $hari_periksa; ?>";

            });
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
