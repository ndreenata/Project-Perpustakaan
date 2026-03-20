<div class="card" style="max-width: 500px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Tambah Kategori 🏷️</h1>
    </div>

    <form method="POST" action="kategori/simpan_kategori.php">
        
        <div class="form-group">
            <label>ID Kategori</label>
            <input type="text" name="id_kategori" placeholder="Contoh: K01" required>
        </div>
        
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="kategori" placeholder="Contoh: Novel, Komik" required>
        </div>
        
        <button type="submit" class="btn-simpan">Simpan</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=kategori" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Kembali</a>
        </div>
    </form>
</div>
