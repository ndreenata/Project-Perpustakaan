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

$query = "UPDATE tbl_buku SET 
            judul_buku='$judul', 
            sinopsis_buku='$sinopsis', 
            jumlah_halaman='$halaman', 
            jumlah_buku='$jumlah', 
            id_kategori='$kategori', 
            id_penerbit='$penerbit', 
            tahun_terbit='$tahun' 
          WHERE id_buku='$id'";

$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Sukses! Data buku berhasil diperbarui.');
        window.location.href = '../dashboard.php?page=buku';
    </script>";
} else {
    echo "<script>
        alert('Gagal update! " . mysqli_error($koneksi) . "');
        window.history.back();
    </script>";
}
?>
