<?php
include '../inc/koneksi.php'; 
$id = $_GET['id_buku']; 

$query = "DELETE FROM tbl_buku WHERE id_buku='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Data berhasil dihapus!');
        window.location.href = '../dashboard.php?page=buku';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus data!');
        window.location.href = '../dashboard.php?page=buku';
    </script>";
}
?>
