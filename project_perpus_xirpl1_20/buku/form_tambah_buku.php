<?php
// Tidak perlu include koneksi lagi (karena sudah ada di dashboard)
// Langsung ambil data untuk dropdown
$kategori_q = mysqli_query($koneksi, "SELECT id_kategori, kategori FROM tbl_kategori ORDER BY kategori ASC");
$penerbit_q = mysqli_query($koneksi, "SELECT id_penerbit, nama_penerbit FROM tbl_penerbit ORDER BY nama_penerbit ASC");
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Tambah Buku Baru 📚</h1>
        <p>Silakan isi data buku dengan lengkap</p>
    </div>

    <form method="POST" action="buku/simpan_buku.php" class="form-tambah"> 
        
        <div class="form-group">
            <label>ID Buku</label>
            <input type="text" name="id_buku" placeholder="Contoh: B001" required>
        </div>
        
        <div class="form-group">
            <label>Judul Buku</label>
            <input type="text" name="judul_buku" placeholder="Judul Buku" required>
        </div>

        <div class="form-group">
            <label>Sinopsis</label>
            <textarea name="sinopsis_buku" rows="4" placeholder="Ringkasan cerita..." required></textarea>
        </div>

        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Jml Halaman</label>
                <input type="number" name="jumlah_halaman">
            </div> 
            <div class="form-group" style="flex:1;">
                <label>Stok Buku</label>
                <input type="number" name="jumlah_buku">
            </div> 
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="id_kategori" required style="width: 100%; padding: 10px; border-radius: 10px;">
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($kategori_q)): ?>
                    <option value="<?php echo $k['id_kategori']; ?>"><?php echo htmlspecialchars($k['kategori']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Penerbit</label>
            <select name="id_penerbit" required style="width: 100%; padding: 10px; border-radius: 10px;">
                <option value="">-- Pilih Penerbit --</option>
                <?php while ($p = mysqli_fetch_assoc($penerbit_q)): ?>
                    <option value="<?php echo $p['id_penerbit']; ?>"><?php echo htmlspecialchars($p['nama_penerbit']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit" placeholder="2024">
        </div>

        <button type="submit" class="btn-simpan">Simpan Data</button> 
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=buku" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Kembali ke Daftar Buku</a>
        </div>
    </form> 
</div>
