<?php
session_start();
include 'inc/koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    echo "<script>alert('Silakan login dulu untuk booking!'); window.location='login.php';</script>";
    exit;
}

// 2. Tangkap ID
if(!isset($_GET['id'])) {
    header("location:katalog.php");
    exit;
}

$id_buku = $_GET['id'];
$id_anggota = $_SESSION['ses_id'];
$tgl_sekarang = date('Y-m-d');

// 3. Cek Stok (Penting!)
$q_stok = mysqli_query($koneksi, "SELECT jumlah_buku, judul_buku FROM tbl_buku WHERE id_buku='$id_buku'");
$d_stok = mysqli_fetch_array($q_stok);

if ($d_stok['jumlah_buku'] < 1) {
    echo "<script>alert('Maaf, stok buku ini sudah habis!'); window.location='katalog.php';</script>";
    exit;
}

// 4. Cek Double Booking (Biar ga spam)
$cek = mysqli_query($koneksi, "SELECT * FROM tbl_booking WHERE id_buku='$id_buku' AND id_anggota='$id_anggota' AND status='Menunggu'");
if(mysqli_num_rows($cek) > 0){
    echo "<script>alert('Anda sudah membooking buku ini sebelumnya. Cek dashboard member.'); window.location='member.php';</script>";
    exit;
}

// 5. Simpan ke Database
// (Karena kita sudah ubah id_buku jadi VARCHAR di langkah 1, ID 'N_0001' pasti bisa masuk)
$simpan = mysqli_query($koneksi, "INSERT INTO tbl_booking (id_buku, id_anggota, tgl_booking, status) VALUES ('$id_buku', '$id_anggota', '$tgl_sekarang', 'Menunggu')");

if ($simpan) {
    echo "<script>alert('Berhasil Booking! Silakan ambil buku di perpustakaan.'); window.location='member.php';</script>";
} else {
    echo "<script>alert('Gagal: ".mysqli_error($koneksi)."'); window.location='katalog.php';</script>";
}
?>
