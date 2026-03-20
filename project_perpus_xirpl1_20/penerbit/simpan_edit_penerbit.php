<?php 
include '../inc/koneksi.php';

$id = $_POST['id_penerbit'];
$nama = $_POST['nama_penerbit'];
$tlp = $_POST['notlp_penerbit'];
$sales = $_POST['nama_sales'];
$tlpsales = $_POST['notlp_sales'];

$query = "UPDATE tbl_penerbit SET 
            nama_penerbit='$nama',
            notlp_penerbit='$tlp',
            nama_sales='$sales',
            notlp_sales='$tlpsales' 
          WHERE id_penerbit='$id'";

$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Data Penerbit berhasil diupdate!');
        window.location.href = '../dashboard.php?page=penerbit';
    </script>";
} else {
    echo "<script>
        alert('Gagal update data.');
        window.history.back();
    </script>";
}
?>
