<div class="card fade-in-up" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Tambah Anggota Baru 🎓</h1>
    </div>
    <form method="POST" action="anggota/simpan_anggota.php">
        <div class="form-group">
            <label>ID Anggota (NIS/NISN)</label>
            <input type="text" name="id_anggota" placeholder="Contoh: 12345678" required>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_anggota" placeholder="Nama Siswa..." required>
        </div>
        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: XII RPL 1" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label>No Telepon / WA</label>
                <input type="text" name="no_tlp" placeholder="08..." required>
            </div>
        </div>
        <button type="submit" class="btn-simpan">Simpan Data</button>
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=anggota" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Kembali</a>
        </div>
    </form>
</div>
