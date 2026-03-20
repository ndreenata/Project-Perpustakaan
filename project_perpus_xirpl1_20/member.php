<?php
session_start();
include 'inc/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php");
    exit;
}

// Ambil Nama atau Username dari session untuk mencari data di tbl_anggota
$ses_nama = $_SESSION['nama']; 
$ses_username = $_SESSION['username'];

// Ambil data lengkap dari tabel anggota berdasarkan Nama atau Username yang sedang login
$q_user = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE nama_anggota='$ses_nama' OR id_anggota='$ses_username'");
$d_user = mysqli_fetch_array($q_user);

// Jika data tidak ditemukan di tbl_anggota, kita buat fallback (cadangan) agar tidak error
$nama_tampil = ($d_user) ? $d_user['nama_anggota'] : $_SESSION['nama'];
$kontak_tampil = ($d_user) ? $d_user['no_tlp'] : "Belum diatur";
$id_anggota_tampil = ($d_user) ? $d_user['id_anggota'] : $_SESSION['username'];
$id_user = $id_anggota_tampil; // Untuk keperluan query ulasan

// --- LOGIKA INISIAL NAMA (FOTO PROFIL) ---
$inisial = strtoupper(substr($nama_tampil, 0, 1));

// --- LOGIKA STATISTIK PREMIUM ---
$res_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE id_anggota='$id_anggota_tampil' AND status='Kembali'");
$data_total = mysqli_fetch_assoc($res_total);
$total_selesai = $data_total['total'] ?? 0;

if($total_selesai < 5) { $level = "Pembaca Pemula"; $icon_lvl = "🌱"; $next = 5; $color = "#81C784"; }
elseif($total_selesai < 15) { $level = "Kutu Buku"; $icon_lvl = "📖"; $next = 15; $color = "#64B5F6"; }
else { $level = "Master Literasi"; $icon_lvl = "🏆"; $next = 50; $color = "#FFD54F"; }

$res_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM tbl_peminjaman WHERE id_anggota = '$id_anggota_tampil'");
$data_pinjam_total = mysqli_fetch_assoc($res_pinjam);

// 3. AMBIL DATA BOOKING
$q_booking = mysqli_query($koneksi, "SELECT b.*, k.judul_buku FROM tbl_booking b JOIN tbl_buku k ON b.id_buku = k.id_buku WHERE b.id_anggota = '$id_anggota_tampil' ORDER BY b.id_booking DESC");

// 4. AMBIL RIWAYAT PEMINJAMAN
$q_pinjam = mysqli_query($koneksi, "SELECT p.*, b.judul_buku FROM tbl_peminjaman p JOIN tbl_buku b ON p.id_buku = b.id_buku WHERE p.id_anggota = '$id_anggota_tampil' ORDER BY p.id_peminjaman DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Area | <?php echo $nama_tampil; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root { --primary: #3E2723; --secondary: #5D4037; --accent: #8D6E63; --bg-light: #FDFBF9; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: var(--bg-light); color: var(--primary); min-height: 100vh; display: flex; justify-content: center; padding: 40px 20px; position: relative; overflow-x: hidden; }
        
        @keyframes slideInLeftSmooth { from { opacity: 0; transform: translateX(-100px); filter: blur(10px); } to { opacity: 1; transform: translateX(0); filter: blur(0); } }
        @keyframes slideInUpSmooth { from { opacity: 0; transform: translateY(100px); filter: blur(10px); } to { opacity: 1; transform: translateY(0); filter: blur(0); } }
        @keyframes fadeInOut { from { opacity: 0; } to { opacity: 1; } }
        
        /* ANIMASI BARU UNTUK NOTIFIKASI */
        @keyframes blink-ready { 0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); } 100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); } }
        /* ANIMASI DARURAT TERLAMBAT */
        @keyframes warning-pulse { 0% { background: #fff; } 50% { background: #FFEBEE; } 100% { background: #fff; } }

        .aside-anim { animation: slideInLeftSmooth 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .main-anim { animation: slideInUpSmooth 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
        .stat-anim { opacity: 0; animation: slideInUpSmooth 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

        .blob { position: fixed; border-radius: 50%; filter: blur(90px); opacity: 0.3; z-index: -1; animation: fadeInOut 3s ease-in-out infinite alternate; }
        .blob-1 { width: 500px; height: 500px; background: #FFCCBC; top: -150px; left: -150px; }
        .blob-2 { width: 400px; height: 400px; background: #D1C4E9; bottom: -100px; right: -100px; }

        .dashboard-grid { display: grid; grid-template-columns: 320px 1fr; gap: 30px; width: 100%; max-width: 1200px; z-index: 1; }
        .card { background: #fff; border-radius: 24px; padding: 35px; box-shadow: 0 20px 50px rgba(62, 39, 35, 0.04); border: 1px solid rgba(255,255,255,0.8); }
        
        .avatar-container { text-align: center; margin-bottom: 25px; }
        .avatar-box { width: 120px; height: 120px; margin: 0 auto 15px; background: linear-gradient(135deg, #3E2723, #5D4037); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 50px; border: 6px solid #fff; box-shadow: 0 15px 30px rgba(62, 39, 35, 0.1); font-family: 'Playfair Display', serif; font-weight: bold; }
        .user-badge { background: #EFEBE9; color: #5D4037; padding: 6px 16px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-field { margin-bottom: 18px; text-align: left; }
        .info-label { font-size: 10px; font-weight: 800; color: #A1887F; text-transform: uppercase; margin-bottom: 6px; display: block; letter-spacing: 0.5px; }
        .info-value { background: #F8F9FA; padding: 14px; border-radius: 12px; font-size: 13px; color: #5D4037; font-weight: 500; display: flex; justify-content: space-between; align-items: center; border: 1px solid #F1F1F1; cursor: not-allowed; }

        .stats-wrapper { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card-premium { padding: 25px; border-radius: 20px; border: 1px solid #F1F1F1; transition: 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); position: relative; overflow: hidden; }
        .stat-card-premium:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .stat-card-premium i { font-size: 35px; position: absolute; right: -10px; bottom: -10px; opacity: 0.05; transform: rotate(-15deg); }
        .progress-container { background: rgba(255,255,255,0.2); height: 7px; border-radius: 10px; margin-top: 15px; }
        .progress-bar { background: #fff; height: 100%; border-radius: 10px; box-shadow: 0 0 10px rgba(255,255,255,0.5); transition: 2s ease-in-out; }
        
        .section-header { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; color: #3E2723; }
        .history-item { display: flex; align-items: center; justify-content: space-between; padding: 20px; background: #fff; border-radius: 18px; margin-bottom: 15px; border: 1px solid #F5F5F5; transition: 0.4s ease; }
        .history-item:hover { border-color: #D7CCC8; transform: translateX(10px); background: #FFFBFA; }
        .status-badge { padding: 6px 14px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .btn-action { background: #FFF8E1; color: #FFA000; padding: 6px 12px; border-radius: 8px; text-decoration: none; font-size: 11px; font-weight: 700; border: 1px solid #FFECB3; transition: 0.3s; }
        .btn-action:hover { background: #FFA000; color: #fff; transform: scale(1.05); }
        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="blob blob-1"></div><div class="blob blob-2"></div>

    <div class="dashboard-grid">
        <aside class="card aside-anim">
            <div class="avatar-container">
                <div class="avatar-box"><?php echo $inisial; ?></div>
                <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 5px;"><?php echo $nama_tampil; ?></h2>
                <span class="user-badge">Anggota Perpustakaan</span>
            </div>

            <div style="margin-top: 30px;">
                <div class="info-field">
                    <span class="info-label">Nama Lengkap</span>
                    <div class="info-value">
                        <?php echo $nama_tampil; ?> 
                        <i class='bx bxs-lock-alt' style="opacity:0.3;" title="Data Terkunci"></i>
                    </div>
                </div>
                <div class="info-field">
                    <span class="info-label">Kontak Siswa</span>
                    <div class="info-value">
                        <?php echo $kontak_tampil; ?> 
                        <i class='bx bxs-lock-alt' style="opacity:0.3;" title="Data Terkunci"></i>
                    </div>
                </div>
                <div class="info-field">
                    <span class="info-label">Keamanan Akun</span>
                    <div class="info-value">
                        •••••••• 
                        <i class='bx bxs-lock-alt' style="opacity:0.3;" title="Data Terkunci"></i>
                    </div>
                </div>
            </div>

            <div style="margin-top: 40px; text-align: center;">
                <a href="#" onclick="konfirmasiLogout(event)" style="color: #E53935; text-decoration: none; font-size: 13px; font-weight: 700;">KELUAR AKUN</a><br>
                <a href="katalog.php" style="color: var(--accent); text-decoration: none; font-size: 12px; display: inline-block; margin-top: 15px;">&larr; Kembali ke Katalog</a>
            </div>
        </aside>

        <main class="main-anim">
            <div class="stats-wrapper">
                <div class="stat-card-premium stat-anim" style="background: linear-gradient(135deg, #3E2723, #5D4037); color: #fff; grid-column: span 1; border: none; animation-delay: 0.2s;">
                    <span style="font-size: 10px; font-weight: 700; opacity: 0.7;">LEVEL LITERASI</span>
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 22px; margin: 5px 0;"><?php echo $icon_lvl . " " . $level; ?></h3>
                    <div class="progress-container">
                        <div class="progress-bar" style="width: <?php echo min(($total_selesai/$next)*100, 100); ?>%;"></div>
                    </div>
                    <p style="font-size: 10px; margin-top: 10px; opacity: 0.7;"><?php echo $total_selesai; ?> dari <?php echo $next; ?> buku untuk level berikutnya</p>
                </div>

                <div class="stat-card-premium stat-anim" style="background: #fff; animation-delay: 0.4s;">
                    <i class='bx bxs-book-open'></i>
                    <span style="font-size: 10px; font-weight: 700; color: #A1887F;">TOTAL BACA</span>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 32px; color: #3E2723; margin-top: 5px;"><?php echo $total_selesai; ?></h2>
                    <p style="font-size: 10px; color: #81C784; font-weight: 700; margin-top: 5px;"><i class='bx bx-check-double'></i> Selesai Dibaca</p>
                </div>

                <div class="stat-card-premium stat-anim" style="background: #fff; animation-delay: 0.6s;">
                    <i class='bx bxs-time-five'></i>
                    <span style="font-size: 10px; font-weight: 700; color: #A1887F;">AKTIVITAS PINJAM</span>
                    <h2 style="font-family: 'Playfair Display', serif; font-size: 32px; color: #3E2723; margin-top: 5px;"><?php echo $data_pinjam_total['jml'] ?? 0; ?></h2>
                    <p style="font-size: 10px; color: var(--accent); font-weight: 700; margin-top: 5px;">Total Transaksi</p>
                </div>
            </div>

            <div class="card">
                <h3 class="section-header"><i class='bx bx-bookmarks' style="color:#D1C4E9;"></i> Status Booking</h3>
                <?php if(mysqli_num_rows($q_booking) > 0) { ?>
                    <?php while($b = mysqli_fetch_array($q_booking)) { 
                        $st = $b['status'];
                        $id_buku_antre = $b['id_buku'];
                        $q_stok = mysqli_query($koneksi, "SELECT jumlah_buku FROM tbl_buku WHERE id_buku='$id_buku_antre'");
                        $d_stok = mysqli_fetch_array($q_stok);
                        $is_ready = ($st == 'Antre' && $d_stok['jumlah_buku'] > 0);
                        $cls = ($st == 'Menunggu') ? 'background:#E3F2FD; color:#1976D2;' : 'background:#E8F5E9; color:#2E7D32;';
                        $extra_style = $is_ready ? 'border: 2px solid #2ecc71; animation: blink-ready 2s infinite;' : '';
                    ?>
                    <div class="history-item" style="<?php echo $extra_style; ?>">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 45px; height: 45px; background: #FDF5F2; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #FFAB91; font-size: 20px;"><i class='bx bxs-bookmark-star'></i></div>
                            <div>
                                <h4 style="font-size: 15px; color: #3E2723;"><?php echo $b['judul_buku']; ?></h4>
                                <span style="font-size: 11px; color: #A1887F;">Dipesan pada: <?php echo date('d M Y', strtotime($b['tgl_booking'])); ?></span>
                                <?php if($is_ready): ?>
                                    <div style="margin-top: 5px; color: #27ae60; font-size: 10px; font-weight: 800; display: flex; align-items: center; gap: 5px;"><i class='bx bxs-bell-ring bx-tada'></i> STOK TERSEDIA! SEGERA KE PERPUS</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="status-badge" style="<?php echo $cls; ?>"><?php echo $st; ?></span>
                    </div>
                    <?php } ?>
                <?php } else { echo "<p style='text-align:center; padding: 20px; color:#CCC; font-size:13px;'>Belum ada buku yang dibooking.</p>"; } ?>

                <h3 class="section-header" style="margin-top: 40px;"><i class='bx bx-history' style="color:#FFCCBC;"></i> Riwayat Peminjaman</h3>
                <?php if(mysqli_num_rows($q_pinjam) > 0) { ?>
                    <?php while($p = mysqli_fetch_array($q_pinjam)) { 
                        $st = $p['status'];
                        $cls = ($st == 'Dipinjam') ? 'background:#FFF3E0; color:#EF6C00;' : 'background:#E8F5E9; color:#2E7D32;';
                        
                        // --- LOGIKA BARU: COUNTDOWN PENGEMBALIAN ---
                        $extra_item_style = "";
                        $notif_countdown = "";
                        
                        if($st == 'Dipinjam') {
                            $tgl_kembali = new DateTime($p['tgl_kembali']);
                            $tgl_sekarang = new DateTime();
                            $diff = $tgl_sekarang->diff($tgl_kembali);
                            $hari_sisa = $diff->days;
                            
                            if ($tgl_sekarang > $tgl_kembali) {
                                // Terlambat
                                $extra_item_style = "border: 2px solid #e74c3c; animation: warning-pulse 2s infinite;";
                                $notif_countdown = "<div style='color:#e74c3c; font-size:10px; font-weight:800; margin-top:5px;'><i class='bx bxs-error-circle bx-tada'></i> TERLAMBAT " . $hari_sisa . " HARI!</div>";
                            } else {
                                // Masih ada waktu
                                $warna_hari = ($hari_sisa <= 2) ? "#e67e22" : "#3498db";
                                $notif_countdown = "<div style='color:".$warna_hari."; font-size:10px; font-weight:800; margin-top:5px;'><i class='bx bxs-time-five'></i> SISA " . $hari_sisa . " HARI LAGI</div>";
                            }
                        }
                    ?>
                    <div class="history-item" style="<?php echo $extra_item_style; ?>">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 45px; height: 45px; background: #F5F5F5; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #BDBDBD; font-size: 20px;"><i class='bx bxs-book-open'></i></div>
                            <div>
                                <h4 style="font-size: 15px; color: #3E2723;"><?php echo $p['judul_buku']; ?></h4>
                                <span style="font-size: 11px; color: #A1887F;">Tgl Pinjam: <?php echo date('d M Y', strtotime($p['tgl_pinjam'])); ?></span>
                                <?php if($st == 'Kembali') { ?>
                                    <span style="display:block; font-size:10px; color:#4CAF50; font-weight:700; margin-top:2px;">Selesai: <?php echo date('d M Y', strtotime($p['tgl_kembali'])); ?></span>
                                <?php } else { ?>
                                    <span style="display:block; font-size:10px; color:#E53935; font-weight:700; margin-top:2px;">Deadline: <?php echo date('d M Y', strtotime($p['tgl_kembali'])); ?></span>
                                    <?php echo $notif_countdown; ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span class="status-badge" style="<?php echo $cls; ?>"><?php echo $st; ?></span>
                            <?php if($st == 'Kembali') { 
                                $id_b = $p['id_buku'];
                                $cek = mysqli_query($koneksi, "SELECT * FROM tbl_ulasan WHERE id_buku='$id_b' AND id_anggota='$id_user'");
                                if(mysqli_num_rows($cek) > 0) {
                                    echo "<div style='font-size:9px; color:#8D6E63; font-weight:700; margin-top:8px;'><i class='bx bxs-check-circle'></i> SUDAH DIULAS</div>";
                                } else { ?>
                                    <br><a href="ulasan/tambah_ulasan.php?id=<?php echo $id_b; ?>" class="btn-action" style="margin-top:8px; display:inline-block;">BERI RATING</a>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <?php } ?>
                <?php } else { echo "<p style='text-align:center; padding: 20px; color:#CCC; font-size:13px;'>Belum ada riwayat bacaan.</p>"; } ?>
            </div>
        </main>
    </div>

    <script>
    function konfirmasiLogout(event) {
        event.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Logout',
            html: "Apakah Anda yakin ingin keluar dari sistem?",
            icon: 'warning',
            iconColor: '#8D6E63', 
            showCancelButton: true,
            confirmButtonColor: '#3E2723',
            cancelButtonColor: '#A1887F', 
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            didOpen: () => {
                const popup = Swal.getPopup();
                popup.style.fontFamily = "'Montserrat', sans-serif";
                const title = Swal.getTitle();
                title.style.color = "#3E2723";
                popup.style.borderRadius = "25px";
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let timerInterval
                Swal.fire({
                    html: 'Sedang mencatat log keluar...',
                    timer: 1000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => { b.textContent = Swal.getTimerLeft() }, 100)
                    },
                    willClose: () => { clearInterval(timerInterval) }
                }).then(() => { window.location.href = 'logout.php'; })
            }
        })
    }
    </script>
</body>
</html>