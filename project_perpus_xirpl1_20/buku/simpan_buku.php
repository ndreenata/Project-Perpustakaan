<?php
include '../inc/koneksi.php';

$id = $_POST['id_buku'];
$judul = $_POST['judul_buku'];
$sinopsis = $_POST['sinopsis_buku'];
$halaman = $_POST['jumlah_halaman'];
$jumlah = $_POST['jumlah_buku'];
$kategori = $_POST['id_kategori'];
$penerbit = $_POST['id_penerbit'];
$tahun = $_POST['tahun_terbit'];

$query = "INSERT INTO tbl_buku VALUES ('$id', '$judul', '$sinopsis', '$halaman', '$jumlah', '$kategori', '$penerbit', '$tahun')";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Sukses! Buku berhasil ditambahkan.');
        window.location.href = '../dashboard.php?page=buku';
    </script>";
} else {
    echo "<script>
        alert('Gagal! " . mysqli_error($koneksi) . "');
        window.history.back();
    </script>";
}
?>
