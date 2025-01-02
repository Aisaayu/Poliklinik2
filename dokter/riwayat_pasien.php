<?php
// Koneksi ke database
include('../includes/db.php');
session_start();

// Pastikan dokter_id ada di session
if (isset($_SESSION['user_id'])) {
    $dokter_id = $_SESSION['user_id'];

    // Query untuk mendapatkan data dokter
    $query_dokter = "SELECT * FROM dokter3 WHERE id_dokter = '$dokter_id'";
    $result_dokter = mysqli_query($conn, $query_dokter);

    // Jika data dokter ditemukan, simpan dalam variabel $dokter
    if (mysqli_num_rows($result_dokter) > 0) {
        $dokter = mysqli_fetch_assoc($result_dokter);
    } else {
        echo "Data dokter tidak ditemukan.";
        exit();
    }
} else {
    // Jika session user_id tidak ada
    echo "Anda belum login.";
    exit();
}// Ambil id_pasien dari parameter GET

// SQL query untuk menggabungkan data pemeriksaan dan pasien
$sql = "SELECT p.id, p.tanggal, p.catatan, p.total_harga, 
               ps.id_dokter, ps.nama
        FROM pemeriksaan p
        JOIN pasien ps ON p.id = ps.id
        WHERE p.id_pasien = ?";

// Persiapkan statement dan bind parameter
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_pasien);  // Bind parameter id_pasien dengan tipe integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $riwayat = [];
        while ($row = $result->fetch_assoc()) {
            $riwayat[] = [
                'id_pasien' => $row['id_pasien'],
                'tanggal' => $row['tanggal'],
                'catatan' => $row['catatan'],
                'total_harga' => $row['total_harga'],
                'id_dokter' => $row['id_dokter'],
                'nama' => $row['nama']
            ];
        }
        echo json_encode($riwayat);  // Kirim data dalam format JSON
    } else {
        echo json_encode([]);  // Jika tidak ada data
    }
} else {
    echo json_encode(['error' => 'Gagal menyiapkan query']);
}
// Query untuk mengambil data pasien
$query_pasien = "SELECT * FROM pasien"; // Sesuaikan query dengan kebutuhan Anda
$result_pasien = mysqli_query($conn, $query_pasien);

// Cek jika query berhasil
if (!$result_pasien) {
    die("Query gagal: " . mysqli_error($conn));
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien</title>
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
        .table {
    width: 80%; /* Adjust the width of the table */
    margin: 20px auto; /* Center the table */
    text-align: center; /* Center the table content */
    border-collapse: collapse; /* Remove spacing between table cells */
}

.table th, .table td {
    padding: 12px; /* Add padding for readability */
    border: 1px solid #ddd; /* Add a border around the cells */
}

.table th {
    background-color: #4682B4; /* Set background color for headers */
    color: white; /* Set text color for headers */
}

.table td {
    background-color: #f9f9f9; /* Set background color for table rows */
}

.table td button {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
}

.table td button:hover {
    background-color: #0056b3;
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
    .table td button.btn-warning {
    background-color: #ffc107; /* Warna kuning */
    border-color: #ffc107; /* Border kuning */
    color: white; /* Warna teks putih */
}

.table td button.btn-warning:hover {
    background-color: #e0a800; /* Warna kuning lebih gelap saat hover */
    border-color: #d39e00; /* Border kuning lebih gelap saat hover */
}
.btn-info {
    display: flex;
    align-items: center;
    gap: 8px; /* Jarak antara ikon dan teks */
    padding: 8px 12px;
    background-color: #17a2b8;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-info:active {
    background-color: #117a8b;
}

.btn-info i {
    font-size: 18px; /* Ukuran ikon */
}

/* CSS untuk modal */
.modal {
    display: none; /* Modal tidak terlihat secara default */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
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

    <!-- Main content -->
    <div class="main" id="main-content">
        <!-- Header -->
        <div class="header">
            <button class="toggle-sidebar" id="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Riwayat Pasien</h1>
            <div>
                <span class="font-semibold">Selamat datang, <?php echo htmlspecialchars($dokter['nama_dokter']); ?></span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
        <div class="container">
            <h2>Daftar Riwayat Pasien</h2>
            <table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Pasien</th>
            <th>Alamat</th>
            <th>No. KTP</th>
            <th>No. Telepon</th>
            <th>No. RM</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
       $no = 1;
       while ($pasien = mysqli_fetch_assoc($result_pasien)) {
           echo "<tr>
                   <td>{$no}</td>
                   <td>{$pasien['nama']}</td>
                   <td>{$pasien['alamat']}</td>
                   <td>{$pasien['no_ktp']}</td>
                   <td>{$pasien['no_hp']}</td>
                   <td>{$pasien['nomor_rekam_medis']}</td>
                   <td>
                       <button class='btn btn-info btn-sm' onclick='showDetailRiwayat({$pasien['id']})'><i class='fas fa-history'></i> Lihat Riwayat</button>
                   </td>
               </tr>";
           $no++;
       }
        ?>
    </tbody>
</table>

<!-- Modal Detail Riwayat Pemeriksaan -->
<div id="modalDetailRiwayat" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Riwayat Pemeriksaan Pasien</h2>
        <table id="riwayatTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Periksa</th>
                    <th>Nama Pasien</th>
                    <th>Nama Dokter</th>
                    <th>Keluhan</th>
                    <th>Catatan</th>
                    <th>Obat</th>
                    <th>Biaya Pemeriksaan</th>
                </tr>
            </thead>
            <tbody id="riwayatContent"></tbody>
        </table>
    </div>
</div>


<script>
       document.getElementById('toggle-button').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var mainContent = document.getElementById('main-content');

            // Toggle class 'hidden' pada sidebar
            sidebar.classList.toggle('hidden');

            // Toggle margin-left pada main content
            mainContent.classList.toggle('full');
        });
       // Fungsi untuk menutup modal
function closeModal() {
    document.getElementById('modalDetailRiwayat').style.display = 'none';
}

// Fungsi untuk menampilkan riwayat pemeriksaan pasien
function showDetailRiwayat(idPasien) {
    fetch(`get_riwayat.php?id=${idPasien}`)
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            const riwayatContent = document.getElementById('riwayatContent');
            riwayatContent.innerHTML = ''; // Bersihkan data sebelumnya
            data.forEach((item, index) => {
                riwayatContent.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.tanggal}</td>
                        <td>${item.catatan}</td>
                        <td>Rp. ${item.total_harga.toLocaleString()}</td>
                        <td>${item.nama}</td>
                    </tr>`;
            });
            document.getElementById('modalDetailRiwayat').style.display = 'block';
        } else {
            Swal.fire('Data Tidak Ditemukan', 'Riwayat periksa tidak ditemukan.', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Terjadi kesalahan', 'Gagal memuat data riwayat periksa.', 'error');
    });

}

    </script>
</body>
</html>
