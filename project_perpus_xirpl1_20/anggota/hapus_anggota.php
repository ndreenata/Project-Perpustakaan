<?php
include '../inc/koneksi.php';
$id = $_GET['id_anggota'];
$query = "DELETE FROM tbl_anggota WHERE id_anggota='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>alert('Anggota berhasil dihapus!'); window.location.href = '../dashboard.php?page=anggota';</script>";
} else {
    echo "<script>alert('Gagal menghapus data.'); window.location.href = '../dashboard.php?page=anggota';</script>";
}
?>
