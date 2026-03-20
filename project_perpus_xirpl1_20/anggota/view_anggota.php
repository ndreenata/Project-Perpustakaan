<?php
// Pastikan variabel $koneksi tersedia
if (!isset($koneksi)) {
    include 'inc/koneksi.php'; 
}

$query_anggota = "SELECT * FROM tbl_anggota ORDER BY nama_anggota ASC";
$data = mysqli_query($koneksi, $query_anggota);
?>

<div class="table-container fade-in-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin: 0;">Data Anggota Perpustakaan 👥</h2>
        <a href="?page=tambah_anggota" class="btn-tambah" style="text-decoration: none; background: #3E2723; color: #fff; padding: 10px 20px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-plus'></i> Tambah Anggota
        </a>
    </div>
    
    <table class="styled-table">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px;">No</th>
                <th>ID Anggota</th>
                <th>Nama Siswa</th>
                <th style="text-align: center;">Kelas</th> 
                <th>No Telepon</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center; width: 130px;">Aksi</th> </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if($data && mysqli_num_rows($data) > 0){
                while($row = mysqli_fetch_array($data)) { 
            ?>
            <tr>
                <td style="text-align: center; color: #888;"><?= $no++; ?></td>
                <td style="font-weight: 700; color: #3E2723;"><?= htmlspecialchars($row['id_anggota']); ?></td>
                <td style="font-weight: 500;"><?= htmlspecialchars($row['nama_anggota']); ?></td>
                <td style="text-align: center; font-weight: 600; color: #8D6E63;"><?= htmlspecialchars($row['kelas']); ?></td>
                <td><?= htmlspecialchars($row['no_tlp']); ?></td>
                <td style="text-align: center;">
                    <span style="background: #E8F5E9; color: #2E7D32; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 10px; text-transform: uppercase;">
                        <?= htmlspecialchars($row['status']); ?>
                    </span>
                </td>
                
                <td style="text-align: center;">
                    <div style="display: flex; gap: 12px; justify-content: center; align-items: center;">
                        
                        <a href="anggota/cetak_kartu.php?id_anggota=<?= $row['id_anggota']?>" 
                           target="_blank" 
                           title="Cetak Kartu Anggota"
                           style="color: #5D4037; font-size: 22px; text-decoration: none; opacity: 0.9;">
                           <i class='bx bx-id-card'></i>
                        </a>

                        <a href="?page=edit_anggota&id_anggota=<?= $row['id_anggota']?>" 
                           title="Edit Data"
                           style="color: #8D6E63; font-size: 22px; text-decoration: none; opacity: 0.8;">
                           <i class='bx bx-edit-alt'></i>
                        </a>
                        
                        <a href="anggota/hapus_anggota.php?id_anggota=<?= $row['id_anggota']?>" 
                           onclick="return confirm('Yakin ingin menghapus anggota ini?')"
                           title="Hapus Anggota"
                           style="color: #C62828; font-size: 22px; text-decoration: none; opacity: 0.8;">
                           <i class='bx bx-trash'></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center; padding:40px; color:#999; font-style:italic;'>Belum ada data anggota terdaftar.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>