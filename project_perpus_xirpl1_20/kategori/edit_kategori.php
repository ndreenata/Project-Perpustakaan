<?php
// Ambil ID dari URL
$id = $_GET['id_kategori'];
$query = mysqli_query($koneksi, "SELECT * FROM tbl_kategori WHERE id_kategori='$id'");
$data = mysqli_fetch_array($query);
?>

<div class="card fade-in-up" style="max-width: 500px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Edit Kategori ✏️</h1>
        <p>Ubah nama kategori buku.</p>
    </div>

    <form method="POST" action="kategori/simpan_edit_kategori.php">
        
        <div class="form-group">
            <label>ID Kategori</label>
            <input type="text" name="id_kategori" value="<?php echo $data['id_kategori']; ?>" readonly style="background: #eee; cursor: not-allowed;">
        </div>
        
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="kategori" value="<?php echo $data['kategori']; ?>" required>
        </div>
        
        <button type="submit" class="btn-simpan">Simpan Perubahan</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=kategori" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Batal</a>
        </div>
    </form>
</div>
