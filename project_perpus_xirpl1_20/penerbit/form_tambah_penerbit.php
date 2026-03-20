<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Tambah Penerbit 🏢</h1>
    </div>

    <form method="POST" action="penerbit/simpan_penerbit.php"> 
        
        <div class="form-group">
            <label>ID Penerbit</label>
            <input type="text" name="id_penerbit" placeholder="Contoh: P001" required>
        </div>
        
        <div class="form-group">
            <label>Nama Penerbit</label>
            <input type="text" name="nama_penerbit" required>
        </div>

        <div class="form-group">
            <label>No Telp Penerbit</label>
            <input type="text" name="notlp_penerbit">
        </div>

        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Nama Sales</label>
                <input type="text" name="nama_sales">
            </div> 
            <div class="form-group" style="flex:1;">
                <label>No Telp Sales</label>
                <input type="text" name="notlp_sales">
            </div>
        </div>

        <button type="submit" class="btn-simpan">Simpan</button> 
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=penerbit" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Kembali</a>
        </div>
    </form> 
</div>
