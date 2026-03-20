-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 04, 2026 at 05:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpustakaan_xirpl1_20`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_anggota`
--

CREATE TABLE `tbl_anggota` (
  `id_anggota` varchar(20) NOT NULL,
  `nama_anggota` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `no_tlp` varchar(20) NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  `password` varchar(255) DEFAULT '123'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_anggota`
--

INSERT INTO `tbl_anggota` (`id_anggota`, `nama_anggota`, `kelas`, `no_tlp`, `status`, `password`) VALUES
('1001', 'Andre', 'XI RPL 1', '08123456789', 'Aktif', '1234'),
('1002', 'Depa', 'XII RPL 2', '085711223344', 'Aktif', '1234'),
('1003', 'Nana', 'X Kuliner 1', '085711223345', 'Aktif', '1234'),
('1004', 'Putri', 'XI Kuliner 5', '085711223346', 'Aktif', '1234'),
('1005', 'Putra', 'XII Perhotelan 1', '085711223347', 'Aktif', '1234'),
('1006', 'Made', 'X DKV 2', '085711223348', 'Aktif', '1234'),
('1007', 'Kadek', 'XI ULW 1', '085711223349', 'Aktif', '1234'),
('1008', 'Gusmang', 'XII Kuliner 3', '085711223350', 'Aktif', '1234'),
('1009', 'Gusdek', 'X Perhotelan 2', '085711223351', 'Aktif', '1234'),
('1010', 'Dyana', 'XI RPL 1', '085711223352', 'Aktif', '1234'),
('1011', 'Laksmitha', 'X RPL 1', '0885647382', 'Aktif', '1234'),
('1012', 'Iyup', 'XI RPL 1', '0877126354', 'Aktif', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_booking`
--

CREATE TABLE `tbl_booking` (
  `id_booking` int(11) NOT NULL,
  `id_buku` varchar(50) DEFAULT NULL,
  `id_anggota` varchar(50) DEFAULT NULL,
  `tgl_booking` date NOT NULL,
  `status` enum('Menunggu','Diambil','Ditolak','Antre') DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_buku`
--

CREATE TABLE `tbl_buku` (
  `id_buku` varchar(10) NOT NULL,
  `judul_buku` varchar(200) NOT NULL,
  `sinopsis_buku` varchar(300) NOT NULL,
  `jumlah_halaman` int(11) NOT NULL,
  `jumlah_buku` int(11) NOT NULL,
  `id_kategori` varchar(10) NOT NULL,
  `id_penerbit` varchar(10) NOT NULL,
  `tahun_terbit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_buku`
--

INSERT INTO `tbl_buku` (`id_buku`, `judul_buku`, `sinopsis_buku`, `jumlah_halaman`, `jumlah_buku`, `id_kategori`, `id_penerbit`, `tahun_terbit`) VALUES
('BK_001', 'Filosofi Teras', 'Buku tentang filsafat Stoisisme untuk mental yang tangguh.', 320, 15, 'KAT_011', 'PUB_001', 2019),
('BK_002', 'Atomic Habits', 'Cara membangun kebiasaan baik dan membuang kebiasaan buruk.', 350, 20, 'KAT_011', 'PUB_001', 2018),
('BK_003', 'Belajar PHP untuk Pemula', 'Panduan lengkap koding web dari nol sampai mahir.', 200, 10, 'KAT_010', 'PUB_004', 2023),
('BK_004', 'Resep Masakan Nusantara', 'Kumpulan resep legendaris dari Sabang sampai Merauke.', 150, 5, 'KAT_013', 'PUB_002', 2020),
('BK_005', 'Sejarah Dunia yang Disembunyikan', 'Mengungkap fakta sejarah yang jarang diketahui publik.', 400, 8, 'N_007', 'PUB_003', 2017),
('BK_006', 'Rich Dad Poor Dad', 'Pelajaran finansial yang tidak diajarkan di sekolah.', 280, 12, 'KAT_014', 'PUB_001', 2000),
('BK_007', 'Dunia Sophie', 'Novel misteri tentang sejarah filsafat.', 500, 6, 'N_002', 'PUB_003', 1991),
('BK_008', 'Naruto Vol. 72', 'Kisah akhir perjuangan ninja Naruto Uzumaki.', 120, 25, 'N_001', 'PUB_002', 2015),
('BK_009', 'Laskar Pelangi', 'Kisah perjuangan anak-anak Belitung mengejar mimpi.', 529, 10, 'N_002', 'PUB_005', 2005),
('BK_010', 'Ensiklopedia Ruang Angkasa', 'Menjelajahi planet, bintang, dan galaksi.', 250, 5, 'KAT_017', 'PUB_002', 2022),
('N_0001', 'Laskar Pelangi', 'Menceritakan kisah sepuluh anak dari keluarga sederhana di Desa Gantung, Belitung, yang berjuang untuk mendapatkan pendidikan di sebuah sekolah yang nyaris ditutup.', 529, 9, 'N_002', 'P_0011', 2005),
('N_0002', 'Guru Aini', 'Novel ini mengangkat tema pendidikan di Indonesia melalui kisah seorang guru bernama Aini, yang seringkali menghadapi tantangan dan keunikan dalam profesinya.', 312, 10, 'N_002', 'PUB_001', 2020);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favorit`
--

CREATE TABLE `tbl_favorit` (
  `id_favorit` int(11) NOT NULL,
  `id_anggota` int(11) DEFAULT NULL,
  `id_buku` varchar(50) DEFAULT NULL,
  `tgl_simpan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_favorit`
--

INSERT INTO `tbl_favorit` (`id_favorit`, `id_anggota`, `id_buku`, `tgl_simpan`) VALUES
(1, 6, 'N_0002', '2026-01-26 12:49:12'),
(4, 6, 'BK_007', '2026-01-26 13:06:28'),
(7, 1002, 'N_0001', '2026-02-19 00:29:11');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_kategori`
--

CREATE TABLE `tbl_kategori` (
  `id_kategori` varchar(10) NOT NULL,
  `kategori` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_kategori`
--

INSERT INTO `tbl_kategori` (`id_kategori`, `kategori`) VALUES
('KAT_010', 'Teknologi'),
('KAT_011', 'Psikologi'),
('KAT_012', 'Agama'),
('KAT_013', 'Masakan'),
('KAT_014', 'Bisnis'),
('KAT_015', 'Kesehatan'),
('KAT_016', 'Seni & Desain'),
('N_001', 'Komik'),
('N_002', 'Novel'),
('N_003', 'Cerita Rakyat'),
('N_006', 'Horror'),
('N_007', 'Sejarah'),
('N_008', 'Anime'),
('N_009', 'Nonfiksi');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_peminjaman`
--

CREATE TABLE `tbl_peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_buku` varchar(20) NOT NULL,
  `id_anggota` varchar(20) NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `status` enum('Dipinjam','Kembali') NOT NULL DEFAULT 'Dipinjam',
  `denda` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_peminjaman`
--

INSERT INTO `tbl_peminjaman` (`id_peminjaman`, `id_buku`, `id_anggota`, `tgl_pinjam`, `jatuh_tempo`, `tgl_kembali`, `status`, `denda`) VALUES
(1, 'N_0001', '1001', '2026-01-22', '2026-01-22', '2026-01-29', 'Kembali', 0),
(2, 'BK_008', '1006', '2026-01-23', '2026-01-30', '2026-01-23', 'Kembali', 0),
(3, 'N_0002', '1002', '2026-01-13', '2026-01-20', '2026-01-23', 'Kembali', 3000),
(4, 'BK_007', '1008', '2026-01-13', '2026-01-20', NULL, 'Dipinjam', 0),
(6, 'N_0001', '1002', '2026-01-29', '2026-02-05', '2026-01-29', 'Kembali', 20481000),
(7, 'BK_001', '1001', '2026-02-04', '2026-02-11', '2026-02-04', 'Kembali', 20487000);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_penerbit`
--

CREATE TABLE `tbl_penerbit` (
  `id_penerbit` varchar(10) NOT NULL,
  `nama_penerbit` varchar(200) NOT NULL,
  `notlp_penerbit` varchar(18) NOT NULL,
  `nama_sales` varchar(200) NOT NULL,
  `notlp_sales` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_penerbit`
--

INSERT INTO `tbl_penerbit` (`id_penerbit`, `nama_penerbit`, `notlp_penerbit`, `nama_sales`, `notlp_sales`) VALUES
('N_0002', 'Nata', '0812345678', 'Alvian', '0812345678'),
('N_0003', 'Alvian', '0812345678', 'Ello', '0812345678'),
('PUB_001', 'Gramedia Pustaka', '02153650110', 'Budi Santoso', '08129998881'),
('PUB_002', 'Penerbit Erlangga', '0218717006', 'Siti Aminah', '08129998882'),
('PUB_003', 'Mizan Pustaka', '0227834310', 'Rahmat Hidayat', '08129998883'),
('PUB_004', 'Deepublish', '0811286364', 'Dewi Sartika', '08129998884'),
('PUB_005', 'Bentang Pustaka', '02747370635', 'Joko Anwar', '08129998885'),
('P_0002', 'Depa', '0812398765', 'Depa', '0812398765'),
('P_0010', 'Nana', '08192837465', 'Nana', '08192837465'),
('P_0011', 'Dimas', '0871625349', 'Dimas', '0871625349');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ulasan`
--

CREATE TABLE `tbl_ulasan` (
  `id_ulasan` int(11) NOT NULL,
  `id_buku` varchar(50) DEFAULT NULL,
  `id_anggota` int(11) DEFAULT NULL,
  `rating` int(1) DEFAULT NULL,
  `ulasan` text DEFAULT NULL,
  `tgl_ulasan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ulasan`
--

INSERT INTO `tbl_ulasan` (`id_ulasan`, `id_buku`, `id_anggota`, `rating`, `ulasan`, `tgl_ulasan`) VALUES
(1, 'N_0001', 1001, 5, '', '2026-01-24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_tlp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(12) NOT NULL,
  `password` varchar(12) NOT NULL,
  `akses` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `no_tlp`, `email`, `username`, `password`, `akses`) VALUES
(1, 'Nata', '0812345678', 'rawr@gmail.com', 'nata', '1234', 'admin'),
(2, 'Ndree', '0823456789', 'rawr@gmail.com', 'ndre', '1234', 'admin'),
(5, 'Laksmitha', '0885647382', 'mita@gmail.com', 'Mitha', '1234', 'anggota'),
(6, 'Iyup', '0877126354', 'Yup@gmail.com', 'Iyup', '1234', 'anggota');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_anggota`
--
ALTER TABLE `tbl_anggota`
  ADD PRIMARY KEY (`id_anggota`);

--
-- Indexes for table `tbl_booking`
--
ALTER TABLE `tbl_booking`
  ADD PRIMARY KEY (`id_booking`);

--
-- Indexes for table `tbl_buku`
--
ALTER TABLE `tbl_buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `tbl_favorit`
--
ALTER TABLE `tbl_favorit`
  ADD PRIMARY KEY (`id_favorit`);

--
-- Indexes for table `tbl_kategori`
--
ALTER TABLE `tbl_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `tbl_peminjaman`
--
ALTER TABLE `tbl_peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`);

--
-- Indexes for table `tbl_penerbit`
--
ALTER TABLE `tbl_penerbit`
  ADD PRIMARY KEY (`id_penerbit`);

--
-- Indexes for table `tbl_ulasan`
--
ALTER TABLE `tbl_ulasan`
  ADD PRIMARY KEY (`id_ulasan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_booking`
--
ALTER TABLE `tbl_booking`
  MODIFY `id_booking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_favorit`
--
ALTER TABLE `tbl_favorit`
  MODIFY `id_favorit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_peminjaman`
--
ALTER TABLE `tbl_peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_ulasan`
--
ALTER TABLE `tbl_ulasan`
  MODIFY `id_ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
