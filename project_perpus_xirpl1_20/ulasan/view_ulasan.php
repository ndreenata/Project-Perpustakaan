<?php
$q_ulasan = mysqli_query($koneksi, "
    SELECT u.*, a.nama_anggota, b.judul_buku 
    FROM tbl_ulasan u
    JOIN tbl_anggota a ON u.id_anggota = a.id_anggota
    JOIN tbl_buku b ON u.id_buku = b.id_buku
    ORDER BY u.id_ulasan DESC
");
?>

<div class="table-container fade-in-up">
    <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin-bottom: 25px;">
        Feedback Pembaca 💬
    </h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php while($u = mysqli_fetch_array($q_ulasan)) { ?>
            <div style="background: #fff; border-radius: 20px; padding: 20px; border: 1px solid #EEE; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.02);">
                <div style="position: absolute; top: 15px; right: 15px; color: #FFA000; font-size: 12px; font-weight: 700;">
                    <i class='bx bxs-star'></i> <?= $u['rating']; ?>/5
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <div style="width: 35px; height: 35px; background: #8D6E63; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                        <?= substr($u['nama_anggota'], 0, 1); ?>
                    </div>
                    <div>
                        <h4 style="font-size: 13px; color: #3E2723; margin: 0;"><?= $u['nama_anggota']; ?></h4>
                        <small style="font-size: 10px; color: #AAA;"><?= date('d M Y', strtotime($u['tgl_ulasan'])); ?></small>
                    </div>
                </div>

                <p style="font-size: 12px; color: #5D4037; font-style: italic; line-height: 1.6; background: #F9F7F5; padding: 10px; border-radius: 10px;">
                    "<?= htmlspecialchars($u['ulasan']); ?>"
                </p>

                <div style="margin-top: 15px; font-size: 11px; color: #8D6E63; border-top: 1px dashed #EEE; padding-top: 10px;">
                    <i class='bx bx-book'></i> <?= $u['judul_buku']; ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>