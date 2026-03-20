<?php
$data = mysqli_query($koneksi, "SELECT * FROM tbl_kategori ORDER BY id_kategori DESC");
?>

<div class="table-container fade-in-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin: 0;">Daftar Kategori Buku 📚</h2>
        <a href="?page=tambah_kategori" class="btn-tambah" style="text-decoration: none; background: #3E2723; color: #fff; padding: 10px 20px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-plus'></i> Tambah Kategori
        </a>
    </div>
    
    <table class="styled-table"> 
        <thead>
            <tr>
                <th style="width: 60px; text-align: center;">No</th>
                <th>Nama Kategori</th>
                <th style="width: 120px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if(mysqli_num_rows($data) > 0){
                while ($row = mysqli_fetch_array($data)) { 
            ?>
                <tr>
                    <td style="text-align: center; color: #888;"><?= $no++; ?></td>
                    <td style="font-weight: 600; color: #3E2723;"><?= htmlspecialchars($row['kategori']); ?></td>

                    <td style="text-align: center;">
                        <div style="display: flex; gap: 15px; justify-content: center; align-items: center;">
                            <a href="?page=edit_kategori&id_kategori=<?= $row['id_kategori']?>" 
                               title="Edit Kategori"
                               style="color: #8D6E63; font-size: 22px; text-decoration: none; opacity: 0.8;">
                               <i class='bx bx-edit-alt'></i>
                            </a>
                            <a href="kategori/hapus_kategori.php?id_kategori=<?= $row['id_kategori']?>"
                               onclick="return confirm('Yakin ingin menghapus kategori ini?')"
                               title="Hapus Kategori"
                               style="color: #C62828; font-size: 22px; text-decoration: none; opacity: 0.8;">
                               <i class='bx bx-trash'></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='3' style='text-align:center; padding: 40px; color: #999;'>Belum ada data kategori.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>