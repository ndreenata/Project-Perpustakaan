<?php
session_start();
include 'inc/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    echo "login";
    exit;
}

$id_user = $_SESSION['ses_id'];
$id_buku = $_GET['id'];

// Cek apakah sudah ada di favorit
$cek = mysqli_query($koneksi, "SELECT * FROM tbl_favorit WHERE id_anggota='$id_user' AND id_buku='$id_buku'");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah ada, hapus
    mysqli_query($koneksi, "DELETE FROM tbl_favorit WHERE id_anggota='$id_user' AND id_buku='$id_buku'");
    echo "hapus";
} else {
    // Jika belum ada, tambah
    mysqli_query($koneksi, "INSERT INTO tbl_favorit (id_anggota, id_buku) VALUES ('$id_user', '$id_buku')");
    echo "tambah";
}
?>