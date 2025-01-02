<?php
session_start();

// Menghubungkan ke database
include('../includes/db.php');

// Menangani proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk mengecek apakah username dan password ada dalam database
    // Sesuaikan nama kolom dan tabel sesuai dengan database Anda
    $query = "SELECT * FROM users WHERE username = ? AND role = 'dokter'"; // Ganti 'username' jika kolomnya berbeda
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika ditemukan, ambil data pengguna
        $row = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Menyimpan informasi pengguna dalam sesi
            $_SESSION['user_id'] = $row['id'];  // Ganti 'id' dengan nama kolom ID pengguna yang benar
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_role'] = $row['role'];
            $_SESSION['id_dokter'] = $row['id'];

            // Redirect ke dashboard dokter setelah login berhasil
            header("Location: ../dokter/dashboard_dokter.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Poli Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full">
        <h2 class="text-2xl font-bold text-center mb-6">Login Dokter</h2>
        
        <!-- Menampilkan pesan error jika login gagal -->
        <?php if (isset($error)): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form action="login_dokter.php" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-semibold">Username</label>
                <input type="text" name="username" id="username" class="w-full p-3 mt-2 border border-gray-300 rounded" required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold">Password</label>
                <input type="password" name="password" id="password" class="w-full p-3 mt-2 border border-gray-300 rounded" required>
            </div>
            
            <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded-lg font-semibold hover:bg-blue-600">Login</button>
        </form>

        <!-- Tautan ke halaman registrasi -->
        <div class="mt-4 text-center">
            <a href="../dokter/register_dokter.php" class="text-blue-500 hover:text-blue-700 font-semibold">Belum punya akun? Daftar di sini</a>
        </div>
    </div>
</body>
</html>
