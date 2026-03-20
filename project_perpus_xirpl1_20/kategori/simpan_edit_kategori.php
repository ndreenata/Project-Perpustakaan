<?php
include '../inc/koneksi.php';

$id = $_POST['id_kategori'];
$kategori = $_POST['kategori'];

$query = "UPDATE tbl_kategori SET kategori='$kategori' WHERE id_kategori='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Data kategori berhasil diupdate.');
        window.location.href = '../dashboard.php?page=kategori';
    </script>";
} else {
    echo "<script>
        alert('Gagal update data.');
        window.history.back();
    </script>";
}
?>
