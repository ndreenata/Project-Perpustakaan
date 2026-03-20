<?php
$data = mysqli_query($koneksi, "SELECT * FROM tbl_penerbit ORDER BY id_penerbit DESC");
?>

<div class="table-container fade-in-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin: 0;">Daftar Data Penerbit 🏢</h2>
        <a href="?page=tambah_penerbit" class="btn-tambah" style="text-decoration: none; background: #3E2723; color: #fff; padding: 10px 20px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-plus'></i> Tambah Penerbit
        </a>
    </div>
    
    <table class="styled-table">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px;">No</th>
                <th>Nama Penerbit</th>
                <th>No Tlp</th>
                <th>Nama Sales</th>
                <th>No Tlp Sales</th>
                <th style="text-align: center; width: 120px;">Aksi</th>
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
                <td style="font-weight: 600; color: #3E2723;"><?= htmlspecialchars($row['nama_penerbit']); ?></td> 
                <td><?= htmlspecialchars($row['notlp_penerbit']); ?></td> 
                <td><?= htmlspecialchars($row['nama_sales']); ?></td> 
                <td><?= htmlspecialchars($row['notlp_sales']); ?></td>
                
                <td style="text-align: center;">
                    <div style="display: flex; gap: 15px; justify-content: center; align-items: center;">
                        <a href="?page=edit_penerbit&id_penerbit=<?= $row['id_penerbit']?>"
                           title="Edit Penerbit"
                           style="color: #8D6E63; font-size: 22px; text-decoration: none; opacity: 0.8;">
                           <i class='bx bx-edit-alt'></i>
                        </a>
                        <a href="penerbit/hapus_penerbit.php?id_penerbit=<?= $row['id_penerbit']?>" 
                           onclick="return confirm('Yakin ingin menghapus penerbit ini?')"
                           title="Hapus Penerbit"
                           style="color: #C62828; font-size: 22px; text-decoration: none; opacity: 0.8;">
                           <i class='bx bx-trash'></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php 
                } 
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding:40px; color:#999;'>Belum ada data penerbit.</td></tr>";
            }
            ?> 
        </tbody>
    </table> 
</div>