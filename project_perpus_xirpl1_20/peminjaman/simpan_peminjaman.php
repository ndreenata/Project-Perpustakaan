<?php
session_start(); // Wajib start session buat SweetAlert
include '../inc/koneksi.php';

// Cek apakah data dikirim?
if (!isset($_POST['id_anggota']) || !isset($_POST['id_buku'])) {
    die("Error: Data tidak lengkap.");
}

$id_anggota  = $_POST['id_anggota'];
$id_buku     = $_POST['id_buku'];
$tgl_pinjam  = $_POST['tgl_pinjam'];
$jatuh_tempo = $_POST['jatuh_tempo'];
$status      = "Dipinjam";

// ==========================================================
// 👮‍♂️ BAGIAN VALIDASI CANGGIH (POLISI PERPUS) 👮‍♂️
// ==========================================================

// 1. CEK LIMIT PEMINJAMAN (Maksimal 2 Buku)
$cek_limit = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE id_anggota = '$id_anggota' AND status = 'Dipinjam'");
$data_limit = mysqli_fetch_assoc($cek_limit);

if ($data_limit['total'] >= 2) {
    $_SESSION['swal_icon'] = "error";
    $_SESSION['swal_title'] = "Batas Peminjaman Penuh!";
    $_SESSION['swal_text']  = "Siswa ini sedang meminjam 2 buku. Harap kembalikan buku sebelumnya.";
    header("location:../dashboard.php?page=peminjaman");
    exit;
}

// 2. CEK APAKAH ADA BUKU YANG TELAT? (Blokir Siswa Bermasalah)
$hari_ini = date('Y-m-d');
// Logika: Cari buku yang statusnya 'Dipinjam' TAPI Jatuh Temponya sudah lewat dari Hari Ini
$cek_tunggakan = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman WHERE id_anggota = '$id_anggota' AND status = 'Dipinjam' AND jatuh_tempo < '$hari_ini'");

if (mysqli_num_rows($cek_tunggakan) > 0) {
    $_SESSION['swal_icon'] = "warning";
    $_SESSION['swal_title'] = "Peminjaman Ditolak!";
    $_SESSION['swal_text']  = "Siswa ini masih punya buku yang BELUM DIKEMBALIKAN dan sudah LEWAT JATUH TEMPO. Wajib lunas/balik dulu!";
    header("location:../dashboard.php?page=peminjaman");
    exit;
}

// 3. CEK STOK BUKU (Mencegah stok minus)
$cek_buku = mysqli_query($koneksi, "SELECT jumlah_buku FROM tbl_buku WHERE id_buku = '$id_buku'");
$data_buku = mysqli_fetch_assoc($cek_buku);

if ($data_buku['jumlah_buku'] <= 0) {
    $_SESSION['swal_icon'] = "error";
    $_SESSION['swal_title'] = "Stok Habis!";
    $_SESSION['swal_text']  = "Buku ini sedang tidak tersedia.";
    header("location:../dashboard.php?page=peminjaman");
    exit;
}

// ==========================================================
// ✅ LOLOS VALIDASI -> SIMPAN DATA
// ==========================================================

// A. Simpan ke Tabel Peminjaman
// Note: Kolom 'id_peminjaman' auto increment (NULL), 'tgl_kembali' kosong dulu (NULL), 'denda' 0
$query_simpan = "INSERT INTO tbl_peminjaman (id_buku, id_anggota, tgl_pinjam, jatuh_tempo, tgl_kembali, status, denda) 
                 VALUES ('$id_buku', '$id_anggota', '$tgl_pinjam', '$jatuh_tempo', NULL, '$status', 0)";

$simpan = mysqli_query($koneksi, $query_simpan);

if ($simpan) {
    // B. Kurangi Stok Buku (-1)
    mysqli_query($koneksi, "UPDATE tbl_buku SET jumlah_buku = jumlah_buku - 1 WHERE id_buku = '$id_buku'");

    // Pesan Sukses
    $_SESSION['swal_icon'] = "success";
    $_SESSION['swal_title'] = "Berhasil!";
    $_SESSION['swal_text']  = "Transaksi peminjaman berhasil disimpan.";
} else {
    // Pesan Gagal SQL
    $_SESSION['swal_icon'] = "error";
    $_SESSION['swal_title'] = "Gagal Menyimpan!";
    $_SESSION['swal_text']  = "Error Database: " . mysqli_error($koneksi);
}

// Kembalikan ke halaman tabel
header("location:../dashboard.php?page=peminjaman");
exit;
?>
