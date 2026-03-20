<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="card fade-in-up" style="max-width: 600px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Transaksi Baru 📚</h1>
        <p>Pilih Anggota & Buku, Atur Tanggal</p>
    </div>

    <form method="POST" action="peminjaman/simpan_peminjaman.php">
        
        <div class="form-group">
            <label>Pilih Peminjam (Siswa)</label>
            <select name="id_anggota" id="pilih_anggota" style="width: 100%;" required>
                <option value="">-- Cari Nama Siswa --</option>
                <?php
                $q_anggota = mysqli_query($koneksi, "SELECT * FROM tbl_anggota ORDER BY nama_anggota ASC");
                while ($a = mysqli_fetch_array($q_anggota)) {
                    echo "<option value='$a[id_anggota]'> $a[nama_anggota] (ID: $a[id_anggota]) - $a[kelas] </option>";
                }
                ?>
            </select>
        </div>
        
        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 25px 0;">

        <div class="form-group">
            <label>Pilih Buku</label>
            <select name="id_buku" id="pilih_buku" style="width: 100%;" required>
                <option value="">-- Cari Judul Buku --</option>
                <?php
                $q_buku = mysqli_query($koneksi, "SELECT * FROM tbl_buku WHERE jumlah_buku > 0 ORDER BY judul_buku ASC");
                while ($b = mysqli_fetch_array($q_buku)) {
                    echo "<option value='$b[id_buku]'> $b[judul_buku] (Stok: $b[jumlah_buku]) </option>";
                }
                ?>
            </select>
            <small style="color: #8D6E63; font-style: italic; margin-top: 5px; display: block;">*Hanya menampilkan buku yang tersedia.</small>
        </div>

        <div class="row" style="display: flex; gap: 15px; margin-top: 20px;">
            <div class="form-group" style="flex:1;">
                <label>Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 15px;">
            </div>
            <div class="form-group" style="flex:1;">
                <label>Jatuh Tempo</label>
                <input type="date" name="jatuh_tempo" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 15px;">
            </div>
        </div>

        <button type="submit" class="btn-simpan">Proses Pinjam</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=peminjaman" style="color: #8D6E63; text-decoration: none; font-size: 13px; font-weight: 500;">&larr; Kembali ke Data</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#pilih_anggota').select2({
            placeholder: "Ketik Nama atau Kelas...",
            allowClear: true,
            width: '100%'
        });

        $('#pilih_buku').select2({
            placeholder: "Ketik Judul Buku...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
