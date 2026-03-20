<?php
include '../inc/koneksi.php'; 
$id = $_GET['id_kategori']; 

$query = "DELETE FROM tbl_kategori WHERE id_kategori='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>
        alert('Kategori berhasil dihapus!');
        window.location.href = '../dashboard.php?page=kategori';
    </script>";
} else {
    echo "<script>
        alert('Gagal! Kategori ini mungkin sedang dipakai di Data Buku.');
        window.location.href = '../dashboard.php?page=kategori';
    </script>";
}
?>
