<?php
include '../inc/koneksi.php';

$id = $_POST['id_penerbit'];
$nama = $_POST['nama_penerbit'];
$tlp = $_POST['notlp_penerbit'];
$sales = $_POST['nama_sales'];
$tlpsales = $_POST['notlp_sales'];

$query = "INSERT INTO tbl_penerbit VALUES ('$id', '$nama', '$tlp', '$sales', '$tlpsales')";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Sukses! Penerbit berhasil ditambahkan.');
        window.location.href = '../dashboard.php?page=penerbit';
    </script>";
} else {
    echo "<script>
        alert('Gagal menyimpan data.');
        window.history.back();
    </script>";
}
?>
