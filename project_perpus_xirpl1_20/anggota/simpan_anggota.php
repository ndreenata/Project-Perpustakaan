<?php
include '../inc/koneksi.php';

$id = $_POST['id_anggota'];
$nama = $_POST['nama_anggota'];
$kelas = $_POST['kelas'];
$tlp = $_POST['no_tlp'];
$status = "Aktif";
$password_default = "123"; // Password otomatis untuk member baru

// Query Insert (Pastikan urutan kolom sesuai database kamu)
// Urutan: id_anggota, nama_anggota, kelas, no_tlp, status, password
$query = "INSERT INTO tbl_anggota (id_anggota, nama_anggota, kelas, no_tlp, status, password) 
          VALUES ('$id', '$nama', '$kelas', '$tlp', '$status', '$password_default')";

$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>alert('Sukses! Anggota ditambahkan. Password default: 123'); window.location.href = '../dashboard.php?page=anggota';</script>";
} else {
    echo "<script>alert('Gagal! ID Anggota mungkin sudah ada.'); window.history.back();</script>";
}
?>