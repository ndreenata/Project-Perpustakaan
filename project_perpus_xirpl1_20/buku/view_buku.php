<?php
$data = mysqli_query($koneksi, "
    SELECT b.*, k.kategori, p.nama_penerbit      
    FROM tbl_buku b
    LEFT JOIN tbl_kategori k ON b.id_kategori = k.id_kategori
    LEFT JOIN tbl_penerbit p ON b.id_penerbit = p.id_penerbit
    ORDER BY b.judul_buku ASC
");
?>

<div class="table-container fade-in-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin: 0;">Daftar Koleksi Buku 📚</h2>
        <a href="?page=tambah_buku" class="btn-tambah" style="text-decoration: none; background: #3E2723; color: #fff; padding: 10px 20px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class='bx bx-book-add'></i> Tambah Buku Baru
        </a>
    </div>
    
    <table class="styled-table"> 
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th>Judul Buku</th>
                <th style="width: 250px;">Sinopsis</th>
                <th style="text-align: center;">Hal</th>
                <th style="text-align: center;">Stok</th>
                <th>Kategori</th>
                <th>Penerbit</th>
                <th style="text-align: center;">Tahun</th>
                <th style="text-align: center; width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($data) > 0) {
                while ($row = mysqli_fetch_array($data)) { ?>
                    <tr>
                        <td style="text-align: center; color: #888;"><?= $no++; ?></td> 
                        <td style="font-weight: 700; color: #3E2723;"><?= htmlspecialchars($row['judul_buku']); ?></td>
                        <td>
                            <div class="long-text" title="<?= strip_tags($row['sinopsis_buku']); ?>" style="font-size: 12px; color: #666;">
                                <?= strip_tags($row['sinopsis_buku']); ?>
                            </div>
                        </td> 
                        <td style="text-align:center; font-weight: 500;"><?= $row['jumlah_halaman']; ?></td>
                        <td style="text-align:center;">
                            <span class="badge-stok" style="background: #EFEBE9; color: #8D6E63; padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 700; border: 1px solid #D7CCC8;">
                                <?= $row['jumlah_buku']; ?> Pcs
                            </span>
                        </td>
                        <td><span style="color: #8D6E63; font-weight: 500;"><?= $row['kategori'] ? $row['kategori'] : '-'; ?></span></td> 
                        <td><?= $row['nama_penerbit'] ? $row['nama_penerbit'] : '<span style="color:#EF5350; font-size:10px;">(Data Kosong)</span>'; ?></td>
                        <td style="text-align:center; color: #8D6E63; font-weight: 600;"><?= $row['tahun_terbit']; ?></td>
                        <td style="text-align:center;">
                            <div style="display: flex; gap: 12px; justify-content: center; align-items: center;">
                                <a href="?page=edit_buku&id=<?= $row['id_buku']?>" 
                                   title="Edit Buku"
                                   style="color: #8D6E63; font-size: 22px; text-decoration: none; opacity: 0.8;">
                                   <i class='bx bx-edit-alt'></i>
                                </a>
                                <a href="buku/hapus_buku.php?id_buku=<?= $row['id_buku']?>"
                                   onclick="return confirm('Yakin ingin menghapus buku ini?')" 
                                   title="Hapus Buku"
                                   style="color: #C62828; font-size: 22px; text-decoration: none; opacity: 0.8;">
                                   <i class='bx bx-trash'></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } 
            } else {
                echo "<tr><td colspan='9' style='text-align:center; padding: 40px; color:#999;'>Data masih kosong di database.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>