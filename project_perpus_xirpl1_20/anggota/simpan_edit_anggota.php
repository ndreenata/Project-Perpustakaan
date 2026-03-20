<?php
include '../inc/koneksi.php';
$id = $_POST['id_anggota'];
$nama = $_POST['nama_anggota'];
$kelas = $_POST['kelas'];
$tlp = $_POST['no_tlp'];
$status = $_POST['status'];

$query = "UPDATE tbl_anggota SET nama_anggota='$nama', kelas='$kelas', no_tlp='$tlp', status='$status' WHERE id_anggota='$id'";
$hasil = mysqli_query($koneksi, $query);

if ($hasil) {
    echo "<script>alert('Data anggota berhasil diupdate!'); window.location.href = '../dashboard.php?page=anggota';</script>";
} else {
    echo "<script>alert('Gagal update data.'); window.history.back();</script>";
}
?>
