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
}

// Biaya Jasa Dokter
$biaya_jasa_dokter = 150000;

// Mendapatkan daftar pasien yang akan diperiksa
$query_pasien = "SELECT * FROM pasien WHERE status_pemeriksaan = 'Belum Diperiksa'";
$result_pasien = mysqli_query($conn, $query_pasien);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari form
    $id_pasien = $_POST['id'];
    $catatan_kesehatan = $_POST['catatan'];
    $obat_terpilih = $_POST['obat'];

    // Mengambil harga obat
    $total_biaya_obat = 0;
    foreach ($obat_terpilih as $id_obat) {
        $query_obat = "SELECT harga FROM obat WHERE id_obat = $id_obat";
        $result_obat = mysqli_query($conn, $query_obat);
        $obat = mysqli_fetch_assoc($result_obat);
        $total_biaya_obat += $obat['harga'];
    }

    // Menghitung total biaya pemeriksaan
    $total_biaya_periksa = $biaya_jasa_dokter + $total_biaya_obat;

    // Menyimpan riwayat pemeriksaan
    $query_pemeriksaan = "INSERT INTO pemeriksaan (id_pasien, catatan, biaya_jasa, total_biaya, total_harga, tanggal) 
                          VALUES ('$id_pasien', '$catatan_kesehatan', '$biaya_jasa_dokter', '$total_biaya_obat', '$total_biaya_periksa', NOW())";
    mysqli_query($conn, $query_pemeriksaan);

    // Mendapatkan ID pemeriksaan yang baru saja disimpan
    $id_pemeriksaan = mysqli_insert_id($conn);

    // Menyimpan resep obat
    foreach ($obat_terpilih as $id_obat) {
        $query_resep = "INSERT INTO resep_obat (id_pemeriksaan, id_obat, jumlah) VALUES ('$id_pemeriksaan', '$id_obat', 1)";
        mysqli_query($conn, $query_resep);
    }

    // Update status pasien menjadi sudah diperiksa
    $query_update_status = "UPDATE pasien SET status_pemeriksaan = 'Sudah Diperiksa' WHERE id = '$id_pasien'";
    mysqli_query($conn, $query_update_status);

   // echo "Pemeriksaan selesai dan biaya pemeriksaan: Rp. " . number_format($total_biaya_periksa, 0, ',', '.');
}
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
    color: black; /* Warna teks hitam */
}

.table td button.btn-warning:hover {
    background-color: #e0a800; /* Warna kuning lebih gelap saat hover */
    border-color: #d39e00; /* Border kuning lebih gelap saat hover */
}
.btn-simpan {
    width: 100%; /* Mengatur lebar tombol */
    padding: 12px; /* Memberikan padding */
    background-color: #0056b3; /* Warna biru tua */
    color: white; /* Warna teks putih */
    border: none; /* Menghilangkan border */
    border-radius: 5px; /* Membuat sudut tombol melengkung */
    cursor: pointer; /* Mengubah kursor menjadi pointer */
    font-size: 1rem; /* Ukuran font tombol */
    transition: background-color 0.3s ease; /* Menambahkan transisi warna latar belakang */
}

.btn-simpan:hover {
    background-color: #003f7f; /* Warna biru yang lebih gelap saat di-hover */
}

.btn-simpan:active {
    background-color: #002b5c; /* Warna biru yang lebih gelap saat tombol diklik */
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
            <h1>Pemeriksaan Pasien</h1>
            <div>
                <span class="font-semibold">Selamat datang, <?php echo htmlspecialchars($dokter['nama_dokter']); ?></span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
        <div class="container">
            <h2>Daftar Pasien Belum Diperiksa</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nomor Rekam Medis</th>
                        <th>Nama Pasien</th>
                        <th>Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($pasien = mysqli_fetch_assoc($result_pasien)): ?>
</tr>
<td><?= htmlspecialchars($pasien['nomor_rekam_medis']) ?></td>
                                <td><?= htmlspecialchars($pasien['nama']) ?></td>
                                <td><?= htmlspecialchars($pasien['keluhan']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" onclick="editPasien(<?= $pasien['id'] ?>)">
                                    <i class="fas fa-stethoscope"></i> Periksa
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                             
            <!-- Modal for editing -->
            <div id="popupCard">
                <div class="popup-card">
                    <div class="popup-header">
                        <h5>Periksa Pasien</h5>
                        <button class="btn-close" onclick="closePopup()">&times;</button>
                    </div>
                    <div class="popup-body">
                        <form method="POST" action="periksa_pasien.php">
                            <input type="hidden" name="id" id="pasien_id">
                            <div class="form-group">
                                <label for="catatan">Catatan Kesehatan:</label>
                                <textarea name="catatan" id="catatan" rows="4" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
    <label for="obat">Pilih Obat:</label><br>
    <?php
    $query_obat = "SELECT * FROM obat";
    $result_obat = mysqli_query($conn, $query_obat);
    while ($obat = mysqli_fetch_assoc($result_obat)): ?>
        <input type="checkbox" class="obat-checkbox" name="obat[]" value="<?= $obat['id_obat'] ?>" data-harga="<?= $obat['harga'] ?>"> 
        <?= $obat['nama_obat'] ?> - Rp. <?= number_format($obat['harga'], 0, ',', '.') ?><br>
    <?php endwhile; ?>
</div>
<div class="form-group">
    <label for="total_harga">Total Harga:</label>
    <input type="text" name="total_harga" id="total_harga" class="form-control" readonly>
</div>

<button type="submit" class="btn-simpan">
    <i class="fas fa-save"></i> Simpan
</button>
                        </form>
                    </div>
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
        function calculateTotal() {
    let totalObat = 0; // Inisialisasi total harga obat
    const biayaDokter = 150000; // Biaya jasa dokter
    const checkboxes = document.querySelectorAll('.obat-checkbox:checked'); // Pilih semua checkbox yang dipilih

    // Loop melalui checkbox yang dipilih dan tambahkan harga obat ke totalObat
    checkboxes.forEach((checkbox) => {
        totalObat += parseFloat(checkbox.getAttribute('data-harga')); // Ambil atribut data-harga dari checkbox
    });

    // Hitung total biaya dengan menambahkan biaya dokter
    const totalBiaya = biayaDokter + totalObat;

    // Format total biaya dalam mata uang Indonesia (IDR)
    document.getElementById('total_harga').value = 'Rp. ' + totalBiaya.toLocaleString('id-ID');
}


// Tambahkan event listener untuk setiap checkbox
document.querySelectorAll('.obat-checkbox').forEach((checkbox) => {
    checkbox.addEventListener('change', calculateTotal);
});

function editPasien(id) {
    // Tampilkan modal dan isi formulir dengan data pasien
    document.getElementById('popupCard').style.display = 'flex';
    document.getElementById('pasien_id').value = id;
    calculateTotal(); // Hitung total saat modal dibuka
}

function closePopup() {
    document.getElementById('popupCard').style.display = 'none';
}

       
    </script>
</body>
</html>
