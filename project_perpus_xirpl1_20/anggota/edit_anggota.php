<?php
$id = $_GET['id_anggota'];
$query = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE id_anggota='$id'");
$data = mysqli_fetch_array($query);
?>
<div class="card fade-in-up" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Edit Data Anggota ✏️</h1>
    </div>
    <form method="POST" action="anggota/simpan_edit_anggota.php">
        <div class="form-group">
            <label>ID Anggota</label>
            <input type="text" name="id_anggota" value="<?php echo $data['id_anggota']; ?>" readonly style="background: #eee;">
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_anggota" value="<?php echo $data['nama_anggota']; ?>" required>
        </div>
        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Kelas</label>
                <input type="text" name="kelas" value="<?php echo $data['kelas']; ?>" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label>No Telepon</label>
                <input type="text" name="no_tlp" value="<?php echo $data['no_tlp']; ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" style="width: 100%; padding: 10px; border-radius: 15px; border: 1px solid #ccc;">
                <option value="Aktif" <?php if($data['status']=='Aktif') echo 'selected'; ?>>Aktif</option>
                <option value="Tidak Aktif" <?php if($data['status']=='Tidak Aktif') echo 'selected'; ?>>Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" class="btn-simpan">Simpan Perubahan</button>
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=anggota" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Batal</a>
        </div>
    </form>
</div>
