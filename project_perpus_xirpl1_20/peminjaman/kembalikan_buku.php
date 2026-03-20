<?php
include '../inc/koneksi.php';

// Tangkap ID dari URL tombol "Kembali"
$id_peminjaman = $_GET['id'];
$id_buku       = $_GET['id_buku'];

// 1. Ambil Data Peminjaman (Buat cek Jatuh Tempo)
$query = mysqli_query($koneksi, "SELECT * FROM tbl_peminjaman WHERE id_peminjaman='$id_peminjaman'");
$data  = mysqli_fetch_array($query);

$jatuh_tempo = $data['jatuh_tempo']; // Tanggal harusnya kembali
$tgl_sekarang = date('Y-m-d');       // Tanggal hari ini (realtime)

// 2. Logika Hitung Denda
$denda = 0;
$telat = 0;
$denda_per_hari = 1000; // Ganti 1000 jadi berapapun (misal 500 perak)

// Jika tanggal sekarang LEBIH BESAR dari jatuh tempo, berarti TELAT
if ($tgl_sekarang > $jatuh_tempo) {
    $tgl1 = new DateTime($jatuh_tempo);
    $tgl2 = new DateTime($tgl_sekarang);
    
    $selisih = $tgl2->diff($tgl1);
    $telat   = $selisih->d; // Jumlah hari telat
    $denda   = $telat * $denda_per_hari;
}

// 3. Update Data di Database
// a. Ubah Status jadi 'Kembali', isi Tgl Kembali, isi Denda
$update_status = mysqli_query($koneksi, "UPDATE tbl_peminjaman SET status='Kembali', tgl_kembali='$tgl_sekarang', denda='$denda' WHERE id_peminjaman='$id_peminjaman'");

// b. Kembalikan Stok Buku (+1)
$update_stok = mysqli_query($koneksi, "UPDATE tbl_buku SET jumlah_buku = jumlah_buku + 1 WHERE id_buku='$id_buku'");

if ($update_status && $update_stok) {
    if ($denda > 0) {
        // Kalau kena denda, kasih tau nominalnya
        echo "<script>
            alert('Buku Dikembalikan! Anggota Terlambat $telat Hari. Denda: Rp " . number_format($denda) . "');
            window.location.href = '../dashboard.php?page=peminjaman';
        </script>";
    } else {
        // Kalau tepat waktu
        echo "<script>
            alert('Terima kasih! Buku berhasil dikembalikan tepat waktu.');
            window.location.href = '../dashboard.php?page=peminjaman';
        </script>";
    }
} else {
    echo "<script>alert('Gagal memproses pengembalian!'); window.location.href = '../dashboard.php?page=peminjaman';</script>";
}
?>
