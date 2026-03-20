<?php
session_start(); // Wajib ada biar bisa kirim pesan ke halaman lain
include '../inc/koneksi.php';

$id_peminjaman = $_GET['id'];
$id_buku       = $_GET['buku'];

$tgl_hari_ini  = date('Y-m-d');
$tarif_denda   = 1000; 

// Ambil Data
$cek_pinjam = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman WHERE id_peminjaman = '$id_peminjaman'");
$data       = mysqli_fetch_array($cek_pinjam);
$jatuh_tempo = $data['tgl_jatuh_tempo'];

// Hitung Denda
$tgl1    = strtotime($tgl_hari_ini);
$tgl2    = strtotime($jatuh_tempo);
$selisih = $tgl1 - $tgl2;
$telat_hari = floor($selisih / (60 * 60 * 24));

if ($telat_hari > 0) {
    $total_denda = $telat_hari * $tarif_denda;
    // Simpan status dan pesan untuk SweetAlert
    $_SESSION['swal_icon'] = "warning"; // Ikon tanda seru
    $_SESSION['swal_title'] = "Terlambat $telat_hari Hari!";
    $_SESSION['swal_text']  = "Siswa ini terkena denda sebesar Rp " . number_format($total_denda);
} else {
    $total_denda = 0;
    $_SESSION['swal_icon'] = "success"; // Ikon centang
    $_SESSION['swal_title'] = "Tepat Waktu!";
    $_SESSION['swal_text']  = "Buku dikembalikan tanpa denda.";
}

// Update Database
$update_pinjam = mysqli_query($koneksi, "UPDATE tbl_peminjaman SET 
    tgl_kembali = '$tgl_hari_ini',
    denda       = '$total_denda',
    status      = 'Kembali' 
    WHERE id_peminjaman = '$id_peminjaman'");

$update_stok = mysqli_query($koneksi, "UPDATE tbl_buku SET jumlah_buku = jumlah_buku + 1 WHERE id_buku = '$id_buku'");

// Redirect balik ke tabel
header("location:../dashboard.php?page=peminjaman");
exit;
?>
