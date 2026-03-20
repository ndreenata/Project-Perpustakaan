<?php
$id = $_GET['id_penerbit'];
$query = mysqli_query($koneksi, "SELECT * FROM tbl_penerbit WHERE id_penerbit='$id'");
$data = mysqli_fetch_array($query);
?>

<div class="card fade-in-up" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Edit Penerbit 🏢</h1>
    </div>

    <form method="POST" action="penerbit/simpan_edit_penerbit.php"> 
        
        <div class="form-group">
            <label>ID Penerbit</label>
            <input type="text" name="id_penerbit" value="<?php echo $data['id_penerbit']; ?>" readonly style="background: #eee;">
        </div>
        
        <div class="form-group">
            <label>Nama Penerbit</label>
            <input type="text" name="nama_penerbit" value="<?php echo $data['nama_penerbit']; ?>" required>
        </div>

        <div class="form-group">
            <label>No Telp Penerbit</label>
            <input type="text" name="notlp_penerbit" value="<?php echo $data['notlp_penerbit']; ?>">
        </div>

        <div class="row" style="display: flex; gap: 10px;">
            <div class="form-group" style="flex:1;">
                <label>Nama Sales</label>
                <input type="text" name="nama_sales" value="<?php echo $data['nama_sales']; ?>">
            </div> 
            <div class="form-group" style="flex:1;">
                <label>No Telp Sales</label>
                <input type="text" name="notlp_sales" value="<?php echo $data['notlp_sales']; ?>">
            </div>
        </div>

        <button type="submit" class="btn-simpan">Simpan Perubahan</button> 
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=penerbit" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Batal</a>
        </div>
    </form> 
</div>
