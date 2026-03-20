<?php
// =========================================================
// 1. LOGIKA PROSES ACC (SETUJUI BOOKING)
// =========================================================
if(isset($_GET['acc_id'])){
    $id_booking = $_GET['acc_id'];
    
    // Ambil data booking dulu
    $q_ambil = mysqli_query($koneksi, "SELECT * FROM tbl_booking WHERE id_booking = '$id_booking'");
    $d_booking = mysqli_fetch_array($q_ambil);
    
    if($d_booking){
        $id_buku = $d_booking['id_buku'];
        $id_anggota = $d_booking['id_anggota'];
        $tgl_pinjam = date('Y-m-d');
        $jatuh_tempo = date('Y-m-d', strtotime('+7 days')); // Deadline 7 hari

        // A. Pindahkan ke Tabel Peminjaman
        $ins = mysqli_query($koneksi, "INSERT INTO tbl_peminjaman (id_buku, id_anggota, tgl_pinjam, jatuh_tempo, status) 
                                      VALUES ('$id_buku', '$id_anggota', '$tgl_pinjam', '$jatuh_tempo', 'Dipinjam')");
        
        if($ins){
            // B. Kurangi Stok Buku
            mysqli_query($koneksi, "UPDATE tbl_buku SET jumlah_buku = jumlah_buku - 1 WHERE id_buku = '$id_buku'");
            // C. Hapus data dari tbl_booking (atau ubah status jadi disetujui)
            mysqli_query($koneksi, "DELETE FROM tbl_booking WHERE id_booking = '$id_booking'");
            
            echo "<script>alert('Peminjaman Berhasil Disetujui!'); window.location='?page=booking';</script>";
        }
    }
}

// =========================================================
// 2. LOGIKA PROSES TOLAK/HAPUS
// =========================================================
if(isset($_GET['hapus_id'])){
    $id_hapus = $_GET['hapus_id'];
    $del = mysqli_query($koneksi, "DELETE FROM tbl_booking WHERE id_booking = '$id_hapus'");
    if($del){
        echo "<script>alert('Booking Berhasil Ditolak!'); window.location='?page=booking';</script>";
    }
}
?>

<div class="table-container fade-in-up">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; margin: 0;">Permintaan Booking 📥</h2>
            <p style="color: #8D6E63; font-size: 13px; margin: 5px 0 0 0;">Kelola permintaan peminjaman buku dari siswa</p>
        </div>
    </div>

    <table class="styled-table">
        <thead>
            <tr>
                <th style="text-align: center; width: 50px;">No</th>
                <th>Nama Siswa</th>
                <th>Buku yg Diminta</th>
                <th style="text-align: center;">Tgl Booking</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center; width: 120px;">Aksi Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            /* PERBAIKAN QUERY: 
               Kita pakai LEFT JOIN agar jika ada ID yang agak berbeda sedikit tetap muncul.
               Pastikan kolom id_anggota di tbl_booking matches dengan id_anggota di tbl_anggota.
            */
            $sql_booking = "SELECT b.id_booking, b.id_anggota, b.tgl_booking, b.status, 
                                   a.nama_anggota, k.judul_buku 
                            FROM tbl_booking b
                            LEFT JOIN tbl_anggota a ON b.id_anggota = a.id_anggota
                            LEFT JOIN tbl_buku k ON b.id_buku = k.id_buku
                            WHERE b.status = 'Menunggu'
                            ORDER BY b.id_booking DESC";
            
            $query = mysqli_query($koneksi, $sql_booking);

            if(mysqli_num_rows($query) > 0){
                while($data = mysqli_fetch_array($query)){
                    // Jika nama_anggota kosong (karena salah ID), tampilkan ID-nya saja buat debug
                    $nama_display = !empty($data['nama_anggota']) ? $data['nama_anggota'] : "ID Salah: ".$data['id_anggota'];
            ?>
            <tr>
                <td style="text-align: center; color: #888;"><?php echo $no++; ?></td>
                <td><b style="color: #3E2723;"><?php echo htmlspecialchars($nama_display); ?></b></td>
                <td style="color: #5D4037; font-weight: 500;"><?php echo htmlspecialchars($data['judul_buku']); ?></td>
                <td style="text-align: center; font-size: 13px;"><?php echo date('d M Y', strtotime($data['tgl_booking'])); ?></td>
                <td style="text-align: center;">
                    <span style="background: #FFF3E0; color: #EF6C00; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 10px; text-transform: uppercase;">Menunggu</span>
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 15px; justify-content: center; align-items: center;">
                        <a href="?page=booking&acc_id=<?php echo $data['id_booking']; ?>" 
                           title="Setujui & Pinjamkan"
                           onclick="return confirm('Setujui peminjaman ini?')"
                           style="color: #2E7D32; font-size: 24px; text-decoration: none;">
                           <i class='bx bx-check-circle'></i>
                        </a>
                        
                        <a href="?page=booking&hapus_id=<?php echo $data['id_booking']; ?>" 
                           title="Tolak Booking"
                           onclick="return confirm('Yakin ingin menolak permintaan ini?')"
                           style="color: #C62828; font-size: 24px; text-decoration: none;">
                           <i class='bx bx-x-circle'></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding:50px; color:#aaa; font-style:italic;'>Tidak ada permintaan booking baru.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
