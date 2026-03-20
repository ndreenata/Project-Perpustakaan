<?php
// Tangkap kata kunci pencarian
$keyword = $_GET['keyword'];
?>

<div class="card fade-in-up" style="max-width: 100%;">
    <div class="header-profile" style="margin-bottom: 20px;">
        <h2>Hasil Pencarian 🔍</h2>
        <p>Kata kunci: "<b><?php echo htmlspecialchars($keyword); ?></b>"</p>
    </div>

    <h3 style="color: #5D4037; margin-bottom: 10px; border-bottom: 2px solid #D7CCC8; padding-bottom: 5px; width: fit-content;">
        📚 Data Buku
    </h3>
    
    <?php
    $query_buku = mysqli_query($koneksi, "SELECT * FROM tbl_buku WHERE judul_buku LIKE '%$keyword%' ORDER BY judul_buku ASC");
    
    if(mysqli_num_rows($query_buku) > 0) {
    ?>
    <div class="table-container" style="margin-top: 0; padding: 20px; box-shadow: none; border: 1px solid #eee;">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Judul Buku</th>
                    <th>Stok</th>
                    <th>Tahun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($b = mysqli_fetch_array($query_buku)){ ?>
                <tr>
                    <td><?php echo $b['judul_buku']; ?></td>
                    <td style="text-align: center;">
                        <span class="badge-stok"><?php echo $b['jumlah_buku']; ?></span>
                    </td>
                    <td style="text-align: center;"><?php echo $b['tahun_terbit']; ?></td>
                    <td style="text-align: center;">
                        <a href="?page=edit_buku&id_buku=<?= $b['id_buku']?>" class="btn-kecil btn-edit">Edit</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
        <p style="color: #999; font-style: italic; margin-bottom: 30px;">Tidak ditemukan buku dengan judul tersebut.</p>
    <?php } ?>


    <h3 style="color: #5D4037; margin-top: 40px; margin-bottom: 10px; border-bottom: 2px solid #D7CCC8; padding-bottom: 5px; width: fit-content;">
        👥 Data Anggota
    </h3>

    <?php
    $query_anggota = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE nama_anggota LIKE '%$keyword%' ORDER BY nama_anggota ASC");
    
    if(mysqli_num_rows($query_anggota) > 0) {
    ?>
    <div class="table-container" style="margin-top: 0; padding: 20px; box-shadow: none; border: 1px solid #eee;">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($a = mysqli_fetch_array($query_anggota)){ ?>
                <tr>
                    <td><?php echo $a['nama_anggota']; ?></td>
                    <td><?php echo $a['kelas']; ?></td>
                    <td style="text-align: center;">
                        <span style="background: #E8F5E9; color: #2E7D32; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: bold;">
                            <?php echo $a['status']; ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <a href="?page=edit_anggota&id_anggota=<?= $a['id_anggota']?>" class="btn-kecil btn-edit">Edit</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else { ?>
        <p style="color: #999; font-style: italic;">Tidak ditemukan anggota dengan nama tersebut.</p>
    <?php } ?>

</div>
