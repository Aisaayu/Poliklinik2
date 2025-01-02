-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jan 2025 pada 13.28
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `poliklinik`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'aisaayu', '$2y$10$MuiNm/Sdz4irg.0xrnMiqOTqDa2IoTW0lnBoyPT6uDi5Z4XSpj9H6'),
(2, 'admin', '$2y$10$ICjkpQxQtrKKCIH./320beDFO9c2o8aS91DqJgS9WJYJyL3AXgb.W');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokter3`
--

CREATE TABLE `dokter3` (
  `id_dokter` int(11) UNSIGNED NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialis` varchar(100) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `id_poli` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokter3`
--

INSERT INTO `dokter3` (`id_dokter`, `nama_dokter`, `spesialis`, `no_hp`, `email`, `id_poli`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Dr. Ari Djoko', 'Gigi', '08134578902', 'aridjo@gmail.com', 2, 'aktif', '2024-12-11 05:34:42', '2024-12-11 14:56:22'),
(8, 'Dr. Ayu', 'Jantung', '0812345678', 'ayuuaisaa@gmail.com', 5, 'aktif', '2024-12-11 13:02:48', '2024-12-11 13:02:48'),
(14, 'Dr. Andi', 'Penyakit Dalam', '08134578902', 'Andidian@gmail.com', 4, 'aktif', '2024-12-12 17:44:58', '2024-12-12 17:44:58'),
(16, 'Dr. Jessie', 'THT', '081345617289', 'jessue@gmail.com', 7, 'aktif', '2024-12-29 17:57:36', '2024-12-29 17:57:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `dokter_id` int(11) NOT NULL,
  `waktu` time NOT NULL,
  `tanggal` date NOT NULL,
  `id_pasien` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_dokter`
--

CREATE TABLE `jadwal_dokter` (
  `id_jadwal` int(10) UNSIGNED NOT NULL,
  `id_dokter` int(10) UNSIGNED NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_periksa`
--

CREATE TABLE `jadwal_periksa` (
  `id` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `tanggal_periksa` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `dokter_id` int(10) UNSIGNED DEFAULT NULL,
  `nama_dokter` varchar(255) DEFAULT NULL,
  `poli` varchar(255) DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT 0,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `hari_periksa` varchar(10) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `id_poli` int(10) UNSIGNED DEFAULT NULL,
  `id_dokter` int(10) UNSIGNED DEFAULT NULL,
  `jadwal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal_periksa`
--

INSERT INTO `jadwal_periksa` (`id`, `id_pasien`, `tanggal_periksa`, `waktu`, `dokter_id`, `nama_dokter`, `poli`, `status_aktif`, `is_aktif`, `waktu_mulai`, `waktu_selesai`, `hari_periksa`, `status`, `id_poli`, `id_dokter`, `jadwal_id`) VALUES
(25, NULL, NULL, NULL, 8, 'Dr. Ayu', NULL, 0, 0, '09:00:00', '17:00:00', 'Senin', 'aktif', NULL, NULL, NULL),
(31, NULL, NULL, NULL, 8, NULL, NULL, 0, 0, '09:00:00', '17:00:00', 'Selasa', 'nonaktif', NULL, NULL, NULL),
(32, NULL, NULL, NULL, 8, NULL, NULL, 0, 0, '13:00:00', '17:00:00', 'Rabu', 'nonaktif', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `tanggal_ditambahkan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `deskripsi`, `harga`, `stok`, `tanggal_ditambahkan`) VALUES
(1, 'Paracetamol', 'Obat penurun demam dan pereda nyeri.', 5000.00, 100, '2024-12-12 07:06:23'),
(2, 'Amoxicillin', 'Antibiotik untuk infeksi bakteri.', 15000.00, 50, '2024-12-12 07:06:23'),
(3, 'Vitamin C', 'Suplemen untuk meningkatkan daya tahan tubuh.', 10000.00, 200, '2024-12-12 07:06:23'),
(4, 'Ibuprofen', 'Obat untuk mengurangi peradangan dan nyeri.', 8000.00, 120, '2024-12-12 07:06:23'),
(5, 'Antasida', 'Obat untuk mengatasi gangguan asam lambung.', 7000.00, 150, '2024-12-12 07:06:23'),
(6, 'Loratadine', 'Antihistamin untuk alergi.', 12000.00, 80, '2024-12-12 07:06:23'),
(7, 'Salbutamol', 'Obat untuk mengatasi sesak napas dan asma.', 20000.00, 40, '2024-12-12 07:06:23'),
(8, 'Metformin', 'Obat untuk mengontrol kadar gula darah.', 25000.00, 60, '2024-12-12 07:06:23'),
(9, 'Cetirizine', 'Obat untuk meredakan gejala alergi.', 11000.00, 90, '2024-12-12 07:06:23'),
(10, 'Ranitidine', 'Obat untuk mengatasi gangguan pencernaan.', 13000.00, 70, '2024-12-12 07:06:23'),
(11, 'Omeprazole', 'Obat untuk mengurangi produksi asam lambung.', 15000.00, 50, '2024-12-12 07:06:23'),
(12, 'Dexamethasone', 'Obat untuk mengurangi peradangan.', 18000.00, 30, '2024-12-12 07:06:23'),
(13, 'Aspirin', 'Obat untuk meredakan nyeri dan mencegah pembekuan darah.', 5000.00, 200, '2024-12-12 07:06:23'),
(14, 'Clindamycin', 'Antibiotik untuk infeksi bakteri.', 30000.00, 25, '2024-12-12 07:06:23'),
(15, 'Erythromycin', 'Antibiotik untuk berbagai jenis infeksi bakteri.', 28000.00, 35, '2024-12-12 07:06:23'),
(16, 'Hydrocortisone', 'Obat untuk mengatasi peradangan dan alergi kulit.', 15000.00, 45, '2024-12-12 07:06:23'),
(17, 'Azithromycin', 'Antibiotik untuk infeksi saluran pernapasan.', 35000.00, 20, '2024-12-12 07:06:23'),
(18, 'Tetracycline', 'Antibiotik untuk berbagai infeksi bakteri.', 24000.00, 50, '2024-12-12 07:06:23'),
(19, 'Insulin', 'Obat untuk mengontrol kadar gula darah pada diabetes.', 50000.00, 10, '2024-12-12 07:06:23'),
(20, 'Amiodarone', 'Obat untuk mengatasi gangguan irama jantung.', 45000.00, 15, '2024-12-12 07:06:23'),
(25, 'Furosemide', NULL, 10000.00, 0, '2025-01-02 01:35:02'),
(26, 'Lisinopril', NULL, 15000.00, 0, '2025-01-02 01:36:29'),
(27, 'Enalapril', NULL, 10000.00, 0, '2025-01-02 01:36:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pasien`
--

CREATE TABLE `pasien` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `no_ktp` varchar(20) NOT NULL,
  `nomor_rekam_medis` varchar(20) NOT NULL,
  `tanggal_daftar` datetime NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `id_poli` int(10) UNSIGNED DEFAULT NULL,
  `id_dokter` int(10) UNSIGNED DEFAULT NULL,
  `waktu_pendaftaran` datetime DEFAULT NULL,
  `status_pemeriksaan` enum('Belum Diperiksa','Sudah Diperiksa') DEFAULT 'Belum Diperiksa',
  `keluhan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pasien`
--

INSERT INTO `pasien` (`id`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `no_hp`, `no_ktp`, `nomor_rekam_medis`, `tanggal_daftar`, `email`, `id_poli`, `id_dokter`, `waktu_pendaftaran`, `status_pemeriksaan`, `keluhan`) VALUES
(14, 'Aisa Ayu Rizky', '2003-04-09', 'Perempuan', 'Jalan Griya', '0817171171771', '337456710992828', '202501-001', '2025-01-01 06:55:53', 'ayuuaisaa@gmail.com', 5, 8, '2025-01-02 12:53:02', 'Belum Diperiksa', 'Jantung Berdebar'),
(15, 'Jessie', '1998-09-01', 'Perempuan', 'Jalan Sunter', '0817287323921', '337309090003', '202501-002', '2025-01-01 06:57:14', 'jessuke@gmail.com', 5, 8, '2025-01-02 12:53:02', 'Belum Diperiksa', 'Batuk'),
(18, 'Seroja', '2009-09-09', 'Perempuan', 'Jalanin aja dulu', '0819289289289', '18398927923181', '202502-003', '2025-02-02 00:00:00', 'ahmadjalu@gmail.com', 5, 8, '0000-00-00 00:00:00', 'Belum Diperiksa', 'sesak napas, sesak di dingin, aritmia.'),
(19, 'Raffi Ahmad', '2003-04-01', '', 'Jalan Sambiroto', '0813456789023', '33730909000233', '202502-004', '2025-01-01 06:57:14', 'raffiahmad@gmail.com', 5, 8, '2025-01-02 15:08:09', 'Belum Diperiksa', 'Sesak Nafas disertai kaki membengkak '),
(21, 'ahmadahmad', '1998-01-01', '', 'Jalan Griya', '01829189128911', '3374674628729392', '202501-005', '2025-01-02 00:00:00', 'ahmadahmad@gmail.com', 5, 8, '0000-00-00 00:00:00', 'Belum Diperiksa', 'Sesak Nafas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `biaya_jasa` decimal(10,2) DEFAULT 150000.00,
  `total_biaya` decimal(10,2) DEFAULT NULL,
  `total_harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id`, `id_pasien`, `tanggal`, `catatan`, `biaya_jasa`, `total_biaya`, `total_harga`) VALUES
(19, 14, '2025-01-02', '', 150000.00, 67000.00, 217000),
(20, 14, '2025-01-02', '', 150000.00, 67000.00, 217000),
(21, 14, '2025-01-02', '', 150000.00, 67000.00, 217000),
(22, 14, '2025-01-02', '', 150000.00, 67000.00, 217000),
(23, 15, '2025-01-02', 'minum obat aja', 150000.00, 27000.00, 177000),
(24, 15, '2025-01-02', 'minum obat aja', 150000.00, 27000.00, 177000),
(25, 19, '2025-01-02', 'Sebaiknya segera di bawa ke IGD, sehingga mendapatkan perawatan lebih lanjut lagi', 150000.00, 27000.00, 177000),
(26, 19, '2025-01-02', 'Sebaiknya segera di bawa ke IGD, sehingga mendapatkan perawatan lebih lanjut lagi', 150000.00, 27000.00, 177000),
(27, 19, '2025-01-02', 'Sebaiknya segera di bawa ke IGD, sehingga mendapatkan perawatan lebih lanjut lagi', 150000.00, 27000.00, 177000),
(28, 18, '2025-01-02', 'Segera di bawa ke Rumah Sakit terdekat', 150000.00, 25000.00, 175000),
(29, 18, '2025-01-02', 'Segera di bawa ke Rumah Sakit terdekat', 150000.00, 25000.00, 175000),
(30, 18, '2025-01-02', 'Segera di bawa ke Rumah Sakit terdekat', 150000.00, 25000.00, 175000),
(31, 19, '2025-01-02', 'Kemungkinan kebanyakan cairan sehingga terjadinya penyempitan paru-paru dan terjadinya banyak cairan yang berkumpul diparu paru itu yang menyebabkan sesak nafas dan juga pompa jantung yang melemah juga dapat menyebabkan kaki membengkak karena pompa jantung itu tidak memompa aliran darah dengan maksimal. Jadi saya kasih feurosemide untuk mengeluarkan cairan yaitu berupa kencing untuk mengurangi cairan. Dan jika dalam waktu seminggu tidak ada perubahan segera dibawa ke rumah sakit terdekat ', 150000.00, 10000.00, 160000),
(32, 19, '2025-01-02', '\r\nKemungkinan kebanyakan cairan sehingga terjadinya penyempitan paru-paru dan terjadinya banyak cairan yang berkumpul diparu paru itu yang menyebabkan sesak nafas dan juga pompa jantung yang melemah juga dapat menyebabkan kaki membengkak karena pompa jantung itu tidak memompa aliran darah dengan maksimal. Jadi saya kasih feurosemide untuk mengeluarkan cairan yaitu berupa kencing untuk mengurangi cairan. Dan jika dalam waktu seminggu tidak ada perubahan segera dibawa ke rumah sakit terdekat ', 150000.00, 10000.00, 160000),
(33, 19, '2025-01-02', '\r\nKemungkinan kebanyakan cairan sehingga terjadinya penyempitan paru-paru dan terjadinya banyak cairan yang berkumpul diparu paru itu yang menyebabkan sesak nafas dan juga pompa jantung yang melemah juga dapat menyebabkan kaki membengkak karena pompa jantung itu tidak memompa aliran darah dengan maksimal. Jadi saya kasih feurosemide untuk mengeluarkan cairan yaitu berupa kencing untuk mengurangi cairan. Dan jika dalam waktu seminggu tidak ada perubahan segera dibawa ke rumah sakit terdekat ', 150000.00, 10000.00, 160000),
(34, 19, '2025-01-02', '\r\nKemungkinan kebanyakan cairan sehingga terjadinya penyempitan paru-paru dan terjadinya banyak cairan yang berkumpul diparu paru itu yang menyebabkan sesak nafas dan juga pompa jantung yang melemah juga dapat menyebabkan kaki membengkak karena pompa jantung itu tidak memompa aliran darah dengan maksimal. Jadi saya kasih feurosemide untuk mengeluarkan cairan yaitu berupa kencing untuk mengurangi cairan. Dan jika dalam waktu seminggu tidak ada perubahan segera dibawa ke rumah sakit terdekat ', 150000.00, 10000.00, 160000),
(35, 21, '2025-01-02', 'Sebaik nya di berikan bantuan oksigen', 150000.00, 50000.00, 200000),
(36, 19, '2025-01-02', '\r\nKemungkinan kebanyakan cairan sehingga terjadinya penyempitan paru-paru dan terjadinya banyak cairan yang berkumpul diparu paru itu yang menyebabkan sesak nafas dan juga pompa jantung yang melemah juga dapat menyebabkan kaki membengkak, karena pompa jantung itu tidak memompa aliran darah dengan maksimal. Jadi saya kasih feurosemide untuk mengeluarkan cairan yaitu berupa kencing untuk mengurangi cairan. Dan jika dalam waktu seminggu tidak ada perubahan segera dibawa ke rumah sakit terdekat ', 150000.00, 35000.00, 185000),
(37, 18, '2025-01-02', 'Segera datang kerumah sakit', 150000.00, 10000.00, 160000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` int(11) NOT NULL,
  `pasien_id` int(11) DEFAULT NULL,
  `poli` varchar(255) DEFAULT NULL,
  `dokter` varchar(255) DEFAULT NULL,
  `jadwal` date DEFAULT NULL,
  `nomor_antrian` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_pasien`
--

CREATE TABLE `pendaftaran_pasien` (
  `id` int(11) NOT NULL,
  `no_rm` varchar(50) NOT NULL,
  `alergi` varchar(50) NOT NULL,
  `alergi_jenis` text DEFAULT NULL,
  `poli` varchar(50) NOT NULL,
  `dokter` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` varchar(10) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftaran_poli`
--

CREATE TABLE `pendaftaran_poli` (
  `id` int(11) NOT NULL,
  `pasien_id` int(11) NOT NULL,
  `poli_id` int(10) DEFAULT NULL,
  `dokter_id` int(11) NOT NULL,
  `jadwal_id` int(11) NOT NULL,
  `nomor_antrian` int(11) NOT NULL,
  `keluhan` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Menunggu',
  `hari` date DEFAULT NULL,
  `id_jadwal` int(11) NOT NULL,
  `nomor_rekam_medis` varchar(255) NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `poli1`
--

CREATE TABLE `poli1` (
  `id_poli` int(10) UNSIGNED NOT NULL,
  `nama_poli` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `poli1`
--

INSERT INTO `poli1` (`id_poli`, `nama_poli`, `deskripsi`, `created_at`) VALUES
(1, 'Kesehatan Jiwa ', 'Layanan pemeriksaan dan perawatan untuk kesehatan jiwa', '2024-12-11 19:40:23'),
(2, 'Gigi', 'Poli yang menangani pemeriksaan dan perawatan kesehatan gigi dan mulut, termasuk perawatan gigi berlubang, pencabutan gigi, pemasangan kawat gigi, dan perawatan lainnya', '2024-12-11 19:10:28'),
(3, 'Anak', 'Poli yang khusus untuk memeriksa dan merawat kesehatan anak, termasuk imunisasi, pemeriksaan tumbuh kembang, dan penanganan penyakit pada anak-anak.', '2024-12-11 19:10:28'),
(4, ' Penyakit Dalam', 'Poli yang menangani berbagai penyakit dalam tubuh, seperti gangguan pada sistem pencernaan, jantung, ginjal, dan pernapasan.', '2024-12-11 19:10:28'),
(5, ' Jantung', ' Poli yang berfokus pada pemeriksaan dan pengobatan penyakit jantung, seperti hipertensi, penyakit arteri koroner, gagal jantung, dan masalah jantung lainnya.', '2024-12-11 19:10:28'),
(6, ' Kandungan', 'Poli yang menangani masalah kesehatan terkait dengan kehamilan, persalinan, serta pemeriksaan dan perawatan kesehatan wanita selama dan setelah masa kehamilan.', '2024-12-11 19:10:28'),
(7, ' THT', 'Poli yang menangani masalah kesehatan pada telinga, hidung, dan tenggorokan, seperti infeksi telinga, sinusitis, gangguan pendengaran, dan penyakit tenggorokan.', '2024-12-11 19:10:28'),
(8, ' Saraf', 'Poli yang berfokus pada masalah kesehatan yang terkait dengan sistem saraf, seperti stroke, epilepsi, gangguan saraf perifer, dan penyakit saraf lainnya.', '2024-12-11 19:10:28'),
(9, ' Orthopedi', ' Poli yang menangani masalah kesehatan pada tulang, sendi, otot, dan ligamen, termasuk cedera tulang, perawatan patah tulang, serta penyakit seperti arthritis.', '2024-12-11 19:10:28'),
(10, ' Mata', 'Poli yang menangani masalah kesehatan mata, seperti gangguan penglihatan, penyakit mata, perawatan lensa kontak, dan operasi mata.', '2024-12-11 19:10:28'),
(11, ' Bedah', 'Poli yang berfokus pada tindakan operasi untuk mengatasi berbagai jenis masalah medis, termasuk operasi pengangkatan tumor, trauma fisik, dan masalah lainnya yang memerlukan prosedur bedah.', '2024-12-11 19:10:28'),
(12, ' Psikiatri', ' Poli yang menangani masalah kesehatan mental, termasuk gangguan kecemasan, depresi, gangguan bipolar, dan gangguan psikologis lainnya.', '2024-12-11 19:10:28'),
(13, ' Gizi', ' Poli yang berfokus pada masalah gizi dan diet, termasuk konsultasi mengenai pola makan sehat, penurunan berat badan, dan masalah gizi lainnya.', '2024-12-11 19:10:28'),
(14, ' Rehabilitasi Medik', ' Poli yang membantu pasien dalam proses pemulihan setelah cedera atau penyakit, seperti terapi fisik, okupasi, atau terapi bicara.', '2024-12-11 19:10:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `resep_obat`
--

CREATE TABLE `resep_obat` (
  `id` int(11) NOT NULL,
  `id_pemeriksaan` int(11) DEFAULT NULL,
  `id_obat` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `resep_obat`
--

INSERT INTO `resep_obat` (`id`, `id_pemeriksaan`, `id_obat`, `jumlah`) VALUES
(1, 19, 1, 1),
(2, 19, 9, 1),
(3, 19, 10, 1),
(4, 19, 11, 1),
(5, 19, 12, 1),
(6, 19, 13, 1),
(7, 20, 1, 1),
(8, 20, 9, 1),
(9, 20, 10, 1),
(10, 20, 11, 1),
(11, 20, 12, 1),
(12, 20, 13, 1),
(13, 21, 1, 1),
(14, 21, 9, 1),
(15, 21, 10, 1),
(16, 21, 11, 1),
(17, 21, 12, 1),
(18, 21, 13, 1),
(19, 22, 1, 1),
(20, 22, 9, 1),
(21, 22, 10, 1),
(22, 22, 11, 1),
(23, 22, 12, 1),
(24, 22, 13, 1),
(25, 23, 1, 1),
(26, 23, 3, 1),
(27, 23, 6, 1),
(28, 24, 1, 1),
(29, 24, 3, 1),
(30, 24, 6, 1),
(31, 25, 25, 1),
(32, 25, 26, 1),
(33, 25, 27, 1),
(34, 26, 25, 1),
(35, 26, 26, 1),
(36, 26, 27, 1),
(37, 27, 25, 1),
(38, 27, 26, 1),
(39, 27, 27, 1),
(40, 28, 26, 1),
(41, 28, 27, 1),
(42, 29, 26, 1),
(43, 29, 27, 1),
(44, 30, 26, 1),
(45, 30, 27, 1),
(46, 31, 25, 1),
(47, 32, 25, 1),
(48, 33, 25, 1),
(49, 34, 25, 1),
(50, 35, 19, 1),
(51, 36, 25, 1),
(52, 36, 26, 1),
(53, 36, 27, 1),
(54, 37, 27, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_pendaftaran`
--

CREATE TABLE `riwayat_pendaftaran` (
  `id_riwayat` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(10) UNSIGNED NOT NULL,
  `id_poli` int(10) UNSIGNED NOT NULL,
  `id_jadwal` int(10) UNSIGNED DEFAULT NULL,
  `nomor_antrian` int(11) NOT NULL,
  `keluhan` text NOT NULL,
  `status` enum('Belum Diperiksa','Sudah Diperiksa') DEFAULT 'Belum Diperiksa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_periksa`
--

CREATE TABLE `riwayat_periksa` (
  `id` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `tanggal_periksa` date DEFAULT NULL,
  `dokter` varchar(255) DEFAULT NULL,
  `poli` varchar(255) DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `biaya_periksa` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pasien','dokter','admin') NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `gelar` varchar(100) DEFAULT NULL,
  `id_poli` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama`, `email`, `no_telp`, `gelar`, `id_poli`) VALUES
(4, 'aisaayu', '$2y$10$DXxTgx/RhvKulwHwYwXtD.TeHlER48WFJzsV4U4lKjTLjwi7DChTS', 'admin', 'aisaayu', 'aisaayu@gmail.com', '0817171278291', NULL, NULL),
(5, 'ahmadrafi', '$2y$10$1pD1DaBSTHRBjOPryIV4POaQr5CXTPlzxZv/EQp/K8BJaU2N29uE6', 'pasien', 'ahmad rafi', 'raffiahmad@gmail.com', '08123445667', NULL, NULL),
(6, 'ayu', '$2y$10$Tt2DFElM9A4pfmdseFbxgukxtEEJTDxZYrTkX3Bu2OdSLLnxJM.wy', 'pasien', 'ayu', 'ayuuaisa@gmail.com', '082929292', NULL, NULL),
(8, 'drayu', '$2y$10$x0pTjRmHTBf1hiWitsloXu11LMBiSTUgiBf7Vsy/mXrceTpiRjJ7q', 'dokter', 'Aisa Ayu Rizky', 'aisaayu@gmail.com', '081672451901', 'Dr. Aisa Ayu., Sp.JP', 5),
(9, 'DrJessie', '$2y$10$L0.J8KB3rytCobR.m4595uJWbKCO2JVyrtXeZ6l1YiG1bW1LPD0b.', 'dokter', 'Jessie', 'jessuke@gmail.com', '081765463829', NULL, NULL),
(10, 'raffi', '$2y$10$N.qO0CC54mU140vNQhV9yuARr.MI5M/Oh5FsB45jVuBiQcjylYZiy', 'pasien', 'Ahmad Rafi', 'raffiahmad@gmail.com', '0813456789023', NULL, NULL),
(11, 'Raffi Ahmad', '$2y$10$zRWt90YnYRCjxo/RQ4mlEuwA3ftIrCdyBkgHX/cf8fhpFkD2yarUu', 'pasien', 'Raffi Ahmad', 'raffiahmad@gmail.com', '0813456789023', NULL, NULL),
(12, 'Rogi', '$2y$10$p56drQHiLY8kAoBy5NUB9exDpbGi2lDylNNGt09HmJHX32E384v8a', 'dokter', 'ROG', 'reno@gmail.com', '081717281902', NULL, NULL),
(13, 'Dr. Ari Djoko', '$2y$10$.d6j5HebrV358/sYwjZcn.AedhnuYbsNISbaw6PiRrXB50KIWZmZS', 'dokter', 'Dr. Ari Djoko', 'aridjo@gmail.com', '08134578902', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokter3`
--
ALTER TABLE `dokter3`
  ADD PRIMARY KEY (`id_dokter`),
  ADD KEY `id_poli` (`id_poli`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokter_id` (`dokter_id`);

--
-- Indeks untuk tabel `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indeks untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `dokter_id` (`dokter_id`),
  ADD KEY `fk_jadwal_periksa_jadwal_id` (`jadwal_id`);

--
-- Indeks untuk tabel `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indeks untuk tabel `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_ktp` (`no_ktp`),
  ADD UNIQUE KEY `nomor_rekam_medis` (`nomor_rekam_medis`),
  ADD KEY `nomor_rekam_medis_2` (`nomor_rekam_medis`);

--
-- Indeks untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_pasien` (`id_pasien`);

--
-- Indeks untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pasien_id` (`pasien_id`);

--
-- Indeks untuk tabel `pendaftaran_pasien`
--
ALTER TABLE `pendaftaran_pasien`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pendaftaran_poli`
--
ALTER TABLE `pendaftaran_poli`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pasien_id` (`pasien_id`),
  ADD KEY `poli_id` (`poli_id`),
  ADD KEY `dokter_id` (`dokter_id`),
  ADD KEY `idx_jadwal_id` (`jadwal_id`);

--
-- Indeks untuk tabel `poli1`
--
ALTER TABLE `poli1`
  ADD PRIMARY KEY (`id_poli`);

--
-- Indeks untuk tabel `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_pemeriksaan` (`id_pemeriksaan`);

--
-- Indeks untuk tabel `riwayat_pendaftaran`
--
ALTER TABLE `riwayat_pendaftaran`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_poli` (`id_poli`),
  ADD KEY `id_jadwal` (`id_jadwal`);

--
-- Indeks untuk tabel `riwayat_periksa`
--
ALTER TABLE `riwayat_periksa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_poli` (`id_poli`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `dokter3`
--
ALTER TABLE `dokter3`
  MODIFY `id_dokter` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `id_jadwal` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_pasien`
--
ALTER TABLE `pendaftaran_pasien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pendaftaran_poli`
--
ALTER TABLE `pendaftaran_poli`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `poli1`
--
ALTER TABLE `poli1`
  MODIFY `id_poli` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `resep_obat`
--
ALTER TABLE `resep_obat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `riwayat_pendaftaran`
--
ALTER TABLE `riwayat_pendaftaran`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `riwayat_periksa`
--
ALTER TABLE `riwayat_periksa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokter3`
--
ALTER TABLE `dokter3`
  ADD CONSTRAINT `dokter3_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli1` (`id_poli`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_poli_id` FOREIGN KEY (`id_poli`) REFERENCES `poli1` (`id_poli`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `fk_id_pasien` FOREIGN KEY (`id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`);

--
-- Ketidakleluasaan untuk tabel `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD CONSTRAINT `jadwal_dokter_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter3` (`id_dokter`);

--
-- Ketidakleluasaan untuk tabel `jadwal_periksa`
--
ALTER TABLE `jadwal_periksa`
  ADD CONSTRAINT `fk_jadwal_periksa_jadwal_id` FOREIGN KEY (`jadwal_id`) REFERENCES `pendaftaran_poli` (`jadwal_id`),
  ADD CONSTRAINT `jadwal_periksa_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jadwal_periksa_ibfk_2` FOREIGN KEY (`dokter_id`) REFERENCES `dokter3` (`id_dokter`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `FK_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftaran_poli`
--
ALTER TABLE `pendaftaran_poli`
  ADD CONSTRAINT `fk_jadwal_id` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_periksa` (`id`),
  ADD CONSTRAINT `pendaftaran_poli_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`),
  ADD CONSTRAINT `pendaftaran_poli_ibfk_4` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_periksa` (`id`);

--
-- Ketidakleluasaan untuk tabel `resep_obat`
--
ALTER TABLE `resep_obat`
  ADD CONSTRAINT `fk_id_pemeriksaan` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `pemeriksaan` (`id`);

--
-- Ketidakleluasaan untuk tabel `riwayat_pendaftaran`
--
ALTER TABLE `riwayat_pendaftaran`
  ADD CONSTRAINT `riwayat_pendaftaran_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`),
  ADD CONSTRAINT `riwayat_pendaftaran_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter3` (`id_dokter`),
  ADD CONSTRAINT `riwayat_pendaftaran_ibfk_3` FOREIGN KEY (`id_poli`) REFERENCES `poli1` (`id_poli`),
  ADD CONSTRAINT `riwayat_pendaftaran_ibfk_4` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_dokter` (`id_jadwal`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_poli` FOREIGN KEY (`id_poli`) REFERENCES `poli1` (`id_poli`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
