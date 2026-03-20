<?php
// 1. Cek Filter Status
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// 2. Query Data Lengkap dengan JOIN ke tbl_anggota agar nama muncul
$query_sql = "SELECT p.*, a.nama_anggota, a.kelas, b.judul_buku 
              FROM tbl_peminjaman p
              LEFT JOIN tbl_anggota a ON p.id_anggota = a.id_anggota
              LEFT JOIN tbl_buku b ON p.id_buku = b.id_buku";

if ($status_filter == 'pinjam') {
    $query_sql .= " WHERE p.status = 'Dipinjam'";
} elseif ($status_filter == 'kembali') {
    $query_sql .= " WHERE p.status = 'Kembali'";
}

$query_sql .= " ORDER BY p.id_peminjaman DESC";

$data = mysqli_query($koneksi, $query_sql);
?>

<?php if (isset($_SESSION['swal_icon'])) { ?>
    <script>
        Swal.fire({
            icon: '<?php echo $_SESSION['swal_icon']; ?>',
            title: '<?php echo $_SESSION['swal_title']; ?>',
            text: '<?php echo $_SESSION['swal_text']; ?>',
            confirmButtonColor: '#3E2723',
            confirmButtonText: 'Oke'
        });
    </script>
    <?php unset($_SESSION['swal_icon']); unset($_SESSION['swal_title']); unset($_SESSION['swal_text']); ?>
<?php } ?>

<div class="table-container fade-in-up">
    <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin-bottom: 20px;">
        Transaksi Peminjaman 🔄
    </h2>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <a href="?page=tambah_peminjaman" class="btn-tambah" style="margin-bottom: 0; box-shadow: 0 5px 15px rgba(62, 39, 35, 0.2);">
            <i class='bx bx-plus-circle'></i> Pinjam Buku Baru
        </a>

        <div style="display: flex; gap: 10px;">
            <a href="?page=peminjaman" class="filter-btn btn-all <?php echo ($status_filter=='') ? 'active' : 'btn-inactive'; ?>">Semua</a>
            <a href="?page=peminjaman&status=pinjam" class="filter-btn btn-pinjam <?php echo ($status_filter=='pinjam') ? 'active' : 'btn-inactive'; ?>">Dipinjam</a>
            <a href="?page=peminjaman&status=kembali" class="filter-btn btn-kembali <?php echo ($status_filter=='kembali') ? 'active' : 'btn-inactive'; ?>">Kembali</a>
        </div>
    </div>
    
    <table class="styled-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if(mysqli_num_rows($data) > 0){
                while($row = mysqli_fetch_array($data)) { 
            ?>
            <tr>
                <td style="text-align: center; color: #888;"><?= $no++; ?></td>
                <td>
                    <div style="font-weight: 700; color: #3E2723;"><?= htmlspecialchars($row['nama_anggota'] ?? 'Tanpa Nama'); ?></div>
                    <div style="font-size: 11px; color: #8D6E63;"><?= $row['kelas'] ?? '-'; ?></div>
                </td>
                <td style="max-width: 200px; white-space: normal;"><?= htmlspecialchars($row['judul_buku']); ?></td>
                <td style="text-align: center; color: #5D4037;"><?= date('d/m/Y', strtotime($row['tgl_pinjam'])); ?></td>
                <td style="text-align: center; color: #C62828; font-weight: 600;">
                    <?= date('d/m/Y', strtotime($row['jatuh_tempo'])); ?>
                </td>
                <td style="text-align: center;">
                    <?php if($row['status'] == 'Dipinjam') { ?>
                        <span class="badge-stok" style="background: #FFF3E0; color: #EF6C00;">Sedang Dipinjam</span>
                    <?php } else { ?>
                        <span class="badge-stok" style="background: #E8F5E9; color: #2E7D32;">Sudah Kembali</span>
                    <?php } ?>
                </td>
                <td style="text-align: center;">
                    <?php if($row['status'] == 'Dipinjam') { ?>
                        <a href="peminjaman/proses_kembali.php?id=<?= $row['id_peminjaman']?>&buku=<?= $row['id_buku']?>" 
                           onclick="return confirm('Apakah buku ini dikembalikan? Sistem akan menghitung denda otomatis.')"
                           class="btn-kecil" style="background: #3E2723; padding: 8px 15px;">
                           <i class='bx bx-check-double'></i> Kembali
                        </a>
                    <?php } else { ?>
                        <div style="font-size: 11px; line-height: 1.4;">
                            <span style="color: #888;">Kembali:</span> <br>
                            <span style="color: #3E2723; font-weight: 600;"><?= date('d/m/Y', strtotime($row['tgl_kembali'])); ?></span>
                            <?php if(isset($row['denda']) && $row['denda'] > 0) { ?>
                                <div style="margin-top: 4px; color: #C62828; font-weight: bold; font-size: 11px;">
                                    <i class='bx bx-error-circle'></i> Denda: Rp <?= number_format($row['denda']); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding:30px; color:#888;'>Belum ada data transaksi.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>