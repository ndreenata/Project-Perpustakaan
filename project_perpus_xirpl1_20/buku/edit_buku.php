<?php
// 1. Ambil ID Buku yang mau diedit
$id = $_GET['id']; // Pastikan di view linknya &id=...
$query = mysqli_query($koneksi, "SELECT * FROM tbl_buku WHERE id_buku='$id'");
$data = mysqli_fetch_array($query);

// 2. Ambil Data Kategori & Penerbit buat Dropdown
$kategori_q = mysqli_query($koneksi, "SELECT * FROM tbl_kategori ORDER BY kategori ASC");
$penerbit_q = mysqli_query($koneksi, "SELECT * FROM tbl_penerbit ORDER BY nama_penerbit ASC");
?>

<div class="card fade-in-up" style="max-width: 700px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Edit Data Buku 📚</h1>
        <p>Perbarui informasi buku perpustakaan.</p>
    </div>

    <form method="POST" action="buku/simpan_edit_buku.php"> 
        
        <div class="form-group">
            <label>ID Buku</label>
            <input type="text" name="id_buku" value="<?php echo $data['id_buku']; ?>" readonly style="background: #eee;">
        </div>
        
        <div class="form-group">
            <label>Judul Buku</label>
            <input type="text" name="judul_buku" value="<?php echo $data['judul_buku']; ?>" required>
        </div>

        <div class="form-group">
            <label>Sinopsis</label>
            <textarea name="sinopsis_buku" rows="5" style="width: 100%; padding: 10px; border-radius: 15px; border: 1px solid #ddd;"><?php echo $data['sinopsis_buku']; ?></textarea>
        </div>

        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Jml Halaman</label>
                <input type="number" name="jumlah_halaman" value="<?php echo $data['jumlah_halaman']; ?>">
            </div> 
            <div class="form-group" style="flex:1;">
                <label>Stok Buku</label>
                <input type="number" name="jumlah_buku" value="<?php echo $data['jumlah_buku']; ?>">
            </div> 
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="id_kategori" required style="width: 100%; padding: 14px; border-radius: 15px; border: 1px solid #ddd; background: white;">
                <option value="">-- Pilih Kategori --</option>
                <?php while ($k = mysqli_fetch_assoc($kategori_q)): ?>
                    <option value="<?php echo $k['id_kategori']; ?>" 
                        <?php if($data['id_kategori'] == $k['id_kategori']) echo 'selected'; ?>>
                        <?php echo $k['kategori']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Penerbit</label>
            <select name="id_penerbit" required style="width: 100%; padding: 14px; border-radius: 15px; border: 1px solid #ddd; background: white;">
                <option value="">-- Pilih Penerbit --</option>
                <?php while ($p = mysqli_fetch_assoc($penerbit_q)): ?>
                    <option value="<?php echo $p['id_penerbit']; ?>" 
                        <?php if($data['id_penerbit'] == $p['id_penerbit']) echo 'selected'; ?>>
                        <?php echo $p['nama_penerbit']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit" value="<?php echo $data['tahun_terbit']; ?>">
        </div>

        <button type="submit" class="btn-simpan">Simpan Perubahan</button> 
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=buku" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Batal</a>
        </div>
    </form> 
</div>
