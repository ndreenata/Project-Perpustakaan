<?php
include '../inc/koneksi.php';
$id = $_GET['id_penerbit'];

$query = "DELETE FROM tbl_penerbit WHERE id_penerbit='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Penerbit berhasil dihapus!');
        window.location.href = '../dashboard.php?page=penerbit';
    </script>";
} else {
    echo "<script>
        alert('Gagal! Penerbit ini mungkin sedang dipakai di Data Buku.');
        window.location.href = '../dashboard.php?page=penerbit';
    </script>";
}
?>
