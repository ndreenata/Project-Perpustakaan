<?php
include '../inc/koneksi.php';

$id = $_POST['id_kategori'];
$kategori = $_POST['kategori'];

$query = "INSERT INTO tbl_kategori VALUES ('$id', '$kategori')";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Sukses! Kategori berhasil ditambahkan.');
        window.location.href = '../dashboard.php?page=kategori';
    </script>";
} else {
    echo "<script>
        alert('Gagal! ID Kategori mungkin sudah ada.');
        window.history.back();
    </script>";
}
?>
