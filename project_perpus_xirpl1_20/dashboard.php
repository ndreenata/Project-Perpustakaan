<?php
session_start();
include 'inc/koneksi.php'; 

// Cek Login
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Data User
$user = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username='$user'";
$hasil = mysqli_query($koneksi, $query);
$tampil = mysqli_fetch_array($hasil);
$inisial = strtoupper(substr($tampil['username'], 0, 1));

// ==========================================
// 1. DATA STATISTIK UTAMA
// ==========================================
$buku_stat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_buku"));
$total_buku = $buku_stat['total'];

$anggota_stat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_anggota"));
$total_anggota = $anggota_stat['total'];

$pinjam_stat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_peminjaman WHERE status='Dipinjam'"));
$total_pinjam = $pinjam_stat['total'];

// ==========================================
// 2. HITUNG NOTIFIKASI BOOKING (LONCENG) 🔔
// ==========================================
$notif_q = mysqli_query($koneksi, "SELECT COUNT(*) as jumlah FROM tbl_booking WHERE status='Menunggu'");
$notif_d = mysqli_fetch_assoc($notif_q);
$jml_notif = $notif_d['jumlah'];

// ==========================================
// 3. FITUR BARU: CEK JATUH TEMPO 🚨
// ==========================================
$tgl_sekarang = date('Y-m-d');
$q_telat = mysqli_query($koneksi, "
    SELECT p.*, a.nama_anggota, b.judul_buku 
    FROM tbl_peminjaman p
    JOIN tbl_anggota a ON p.id_anggota = a.id_anggota
    JOIN tbl_buku b ON p.id_buku = b.id_buku
    WHERE p.status = 'Dipinjam' AND p.jatuh_tempo < '$tgl_sekarang'
");
$jml_telat = mysqli_num_rows($q_telat);

// ==========================================
// 4. FITUR BARU: CEK SISTEM ANTREAN ⏳
// ==========================================
$q_cek_antre = mysqli_query($koneksi, "
    SELECT b.*, k.judul_buku, a.nama_anggota, k.jumlah_buku
    FROM tbl_booking b
    JOIN tbl_buku k ON b.id_buku = k.id_buku
    JOIN tbl_anggota a ON b.id_anggota = a.id_anggota
    WHERE b.status = 'Antre' AND k.jumlah_buku > 0
");
$jml_ready_antre = mysqli_num_rows($q_cek_antre);

// ==========================================
// 5. HALL OF FAME QUERY 🏆 (KODE ASLI - LIMIT 1)
// ==========================================
$q_rajin = mysqli_query($koneksi, "
    SELECT a.nama_anggota, COUNT(p.id_anggota) as total 
    FROM tbl_peminjaman p
    JOIN tbl_anggota a ON p.id_anggota = a.id_anggota
    GROUP BY p.id_anggota 
    ORDER BY total DESC LIMIT 1
");
$d_rajin = mysqli_fetch_assoc($q_rajin);

$q_laku = mysqli_query($koneksi, "
    SELECT b.judul_buku, COUNT(p.id_buku) as total 
    FROM tbl_peminjaman p
    JOIN tbl_buku b ON p.id_buku = b.id_buku
    GROUP BY p.id_buku 
    ORDER BY total DESC LIMIT 1
");
$d_laku = mysqli_fetch_assoc($q_laku);

// ==========================================
// 6. QUERY KHUSUS GRAFIK (BARU - LIMIT 5)
// ==========================================
$q_grafik = mysqli_query($koneksi, "
    SELECT b.judul_buku, COUNT(p.id_buku) as total 
    FROM tbl_peminjaman p
    JOIN tbl_buku b ON p.id_buku = b.id_buku
    GROUP BY p.id_buku 
    ORDER BY total DESC LIMIT 5
");
$labels_chart = [];
$data_chart = [];
while($gf = mysqli_fetch_assoc($q_grafik)){
    // Potong judul kalau terlalu panjang biar grafik rapi
    $judul_pendek = (strlen($gf['judul_buku']) > 20) ? substr($gf['judul_buku'], 0, 20) . '...' : $gf['judul_buku'];
    $labels_chart[] = $judul_pendek;
    $data_chart[] = $gf['total'];
}

// Data Tanggal
date_default_timezone_set('Asia/Jakarta'); 
$hari_ini = date('l'); 
$tanggal = date('d'); 
$bulan_tahun = date('F Y');
$hari_indo = ['Sunday'=>'Minggu', 'Monday'=>'Senin', 'Tuesday'=>'Selasa', 'Wednesday'=>'Rabu', 'Thursday'=>'Kamis', 'Friday'=>'Jumat', 'Saturday'=>'Sabtu'];
$hari_fix = $hari_indo[$hari_ini];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" href="https://img.icons8.com/doodle/48/books.png" type="image/png">

    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        /* CSS KHUSUS TAB & CHART LEBAR */
        .tab-menu { display: flex; gap: 10px; margin-bottom: 25px; }
        .tab-btn { padding: 10px 22px; border: none; background: #EFEBE9; color: #8D6E63; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 12px; transition: 0.3s; }
        .tab-btn.active { background: #3E2723; color: #fff; box-shadow: 0 5px 15px rgba(62, 39, 35, 0.1); }
        .tab-content { display: none; animation: fadeIn 0.5s ease; width: 100%; }
        .tab-content.active { display: block; }
        
        /* Container Chart Lebar */
        .chart-full-wrapper { 
            background: #fff; 
            padding: 25px; 
            border-radius: 20px; 
            width: 100%; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.02); 
            border: 1px solid rgba(0,0,0,0.03);
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Dashboard</h2>
                <p>Sistem Informasi Perpustakaan</p>
            </div>
            
            <ul class="nav-links">
                <li><a href="dashboard.php"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
                <li><a href="?page=profile"><i class='bx bxs-user-account'></i> Profile Admin</a></li>
                <li><a href="?page=buku"><i class='bx bxs-book'></i> Data Buku</a></li>
                <li><a href="?page=kategori"><i class='bx bxs-category'></i> Data Kategori</a></li>
                <li><a href="?page=penerbit"><i class='bx bxs-business'></i> Data Penerbit</a></li>
                <li><a href="?page=anggota"><i class='bx bxs-group'></i> Data Anggota</a></li>
                <li><a href="?page=peminjaman"><i class='bx bxs-data'></i> Data Pinjam</a></li>
                <li><a href="?page=ulasan"><i class='bx bxs-chat'></i> Ulasan Siswa</a></li>
                
                <li> 
                    <a href="?page=booking" style="<?php if($jml_notif > 0) echo 'color:#C62828; font-weight:bold;'; ?>">
                        <i class='bx bx-timer'></i> Request Booking 
                        <?php if($jml_notif > 0) echo "($jml_notif)"; ?>
                    </a>
                </li>
                
                <li><a href="#" class="logout-link" onclick="konfirmasiLogout(event)"><i class='bx bx-log-out'></i> Logout</a></li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="calendar-widget">
                    <span class="cal-month"><?php echo $bulan_tahun; ?></span>
                    <h1 class="cal-day"><?php echo $tanggal; ?></h1>
                    <span class="cal-day-name"><?php echo $hari_fix; ?></span>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="top-header">
                <div class="header-left">
                    <div class="breadcrumb">Pages / <span><?php echo isset($_GET['page']) ? ucfirst($_GET['page']) : 'Dashboard'; ?></span></div>
                    
                    <div class="header-welcome">
                        <?php
                        $jam = date('H');
                        if ($jam >= 4 && $jam < 11) { $sapaan = "Selamat Pagi"; $icon = "<i class='bx bxs-leaf mood-icon' style='color: #81C784; font-size: 24px;'></i>"; } 
                        elseif ($jam >= 11 && $jam < 15) { $sapaan = "Selamat Siang"; $icon = "<i class='bx bxs-sun mood-icon' style='color: #FFB74D; font-size: 24px;'></i>"; } 
                        elseif ($jam >= 15 && $jam < 18) { $sapaan = "Selamat Sore"; $icon = "<i class='bx bxs-coffee mood-icon' style='color: #8D6E63; font-size: 24px;'></i>"; } 
                        else { $sapaan = "Selamat Malam"; $icon = "<i class='bx bxs-moon mood-icon' style='color: #7986CB; font-size: 24px;'></i>"; }
                        ?>
                        
                        <div class="greeting-wrapper">
                            <h3 class="greeting-text"><?php echo $sapaan; ?>, <?php echo htmlspecialchars($tampil['username']); ?>!</h3>
                            <?php echo $icon; ?>
                        </div>
                    </div>
                </div>
                
                <div class="header-right">
                    <form action="dashboard.php" method="GET" class="search-box">
                        <input type="hidden" name="page" value="pencarian">
                        <input type="text" name="keyword" class="search-input" placeholder="Cari data..." autocomplete="off">
                    </form>
                    
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="notif-btn" id="darkModeBtn" title="Ganti Mode Gelap/Terang" style="cursor: pointer;"><i class='bx bx-moon'></i></div>
                        <a href="?page=booking" class="notif-btn" title="Lihat Request Booking"><i class='bx bx-bell'></i><?php if($jml_notif > 0): ?><span class="notif-dot"></span><?php endif; ?></a>
                    </div>

                    <div class="profile-dropdown">
                        <div class="mini-avatar"><?php echo $inisial; ?></div>
                        <div class="dropdown-content">
                            <span class="dropdown-name">Hi, <?php echo $tampil['username']; ?></span>
                            <a href="?page=profile"><i class='bx bx-user'></i> Profile Saya</a>
                            <a href="?page=ubah_password"><i class='bx bx-lock-alt'></i> Ubah Password</a>
                            <a href="#" class="logout-btn" onclick="konfirmasiLogout(event)"><i class='bx bx-log-out'></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                <?php
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                    switch ($page) {
                        case 'profile': include "profile.php"; break;
                        case 'buku': include "buku/view_buku.php"; break;
                        case 'tambah_buku': include "buku/form_tambah_buku.php"; break;
                        case 'edit_buku': include "buku/edit_buku.php"; break;
                        case 'kategori': include "kategori/view_kategori.php"; break;
                        case 'tambah_kategori': include "kategori/form_tambah_kategori.php"; break;
                        case 'edit_kategori': include "kategori/edit_kategori.php"; break;
                        case 'penerbit': include "penerbit/view_penerbit.php"; break;
                        case 'tambah_penerbit': include "penerbit/form_tambah_penerbit.php"; break;
                        case 'edit_penerbit': include "penerbit/edit_penerbit.php"; break;
                        case 'anggota': include "anggota/view_anggota.php"; break;
                        case 'tambah_anggota': include "anggota/form_tambah_anggota.php"; break;
                        case 'booking':include "data_booking.php"; break;
                        case 'edit_anggota': include "anggota/edit_anggota.php"; break;
                        case 'peminjaman': include "peminjaman/view_peminjaman.php"; break;
                        case 'tambah_peminjaman': include "peminjaman/form_tambah_peminjaman.php"; break;
                        case 'ulasan': include "ulasan/view_ulasan.php"; break;
                        case 'pencarian': include "pencarian.php"; break;
                        case 'ubah_password': include "form_ubah_password.php"; break;
                        case 'laporan': include "laporan/laporan_peminjaman.php"; break;
                        default: include "profile.php"; break;
                    }
                } else {
                ?>
                    <?php if ($jml_ready_antre > 0) { ?>
                        <div class="fade-in-up" style="background: #E8F5E9; border-left: 5px solid #2E7D32; padding: 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(46, 125, 50, 0.1);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h3 style="color: #2E7D32; font-size: 16px; font-family: 'Montserrat', sans-serif; display: flex; align-items: center; gap: 10px;"><i class='bx bxs-bell-ring bx-tada' style="font-size: 24px;"></i> Info Antrean: Ada Buku yang Sudah Tersedia!</h3>
                                <a href="?page=booking" style="font-size: 11px; font-weight: 700; color: #2E7D32; text-transform: uppercase; text-decoration: none;">Proses Sekarang &rarr;</a>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php mysqli_data_seek($q_cek_antre, 0); while($ra = mysqli_fetch_array($q_cek_antre)) { ?>
                                    <div style="background: #fff; padding: 10px 15px; border-radius: 10px; font-size: 12px; border: 1px solid #C8E6C9; display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 8px; height: 8px; background: #2E7D32; border-radius: 50%;"></div>
                                        Siswa <b><?= $ra['nama_anggota']; ?></b> sedang antre <b><?= $ra['judul_buku']; ?></b>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($jml_telat > 0) { ?>
                        <div class="fade-in-up" style="background: #FFF5F5; border-left: 5px solid #C62828; padding: 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(198, 40, 40, 0.05);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <h3 style="color: #C62828; font-size: 16px; font-family: 'Montserrat', sans-serif; display: flex; align-items: center; gap: 10px;"><i class='bx bxs-alarm-exclamation' style="font-size: 24px;"></i> Peringatan: Ada <?= $jml_telat; ?> Siswa Telat Mengembalikan Buku!</h3>
                                <a href="?page=peminjaman&status=pinjam" style="font-size: 11px; font-weight: 700; color: #C62828; text-transform: uppercase; text-decoration: none;">Proses Semua &rarr;</a>
                            </div>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                <?php mysqli_data_seek($q_telat, 0); while($t = mysqli_fetch_array($q_telat)) { 
                                    $diff = strtotime($tgl_sekarang) - strtotime($t['jatuh_tempo']);
                                    $hari = floor($diff / (60 * 60 * 24));
                                ?>
                                    <div style="background: #fff; padding: 10px 15px; border-radius: 10px; font-size: 12px; border: 1px solid #FFCDD2; display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 8px; height: 8px; background: #C62828; border-radius: 50%;"></div>
                                        <b style="color: #3E2723;"><?= $t['nama_anggota']; ?></b> <span style="color: #8D6E63; opacity: 0.7;">(<?= $t['judul_buku']; ?>)</span><span style="color: #C62828; font-weight: 800; margin-left: 5px;">Telat <?= $hari; ?> Hari</span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="header-profile fade-in-up">
                        <h1 class="dashboard-title">Dashboard Overview <i class='bx bxs-widget' style="color: #A1887F; font-size: 24px;"></i></h1> 
                        <p class="dashboard-subtitle">Ringkasan data perpustakaan real-time.</p>
                    </div>

                    <div class="tab-menu fade-in-up">
                        <button class="tab-btn active" onclick="openTab(event, 'ringkasan')">Ringkasan Utama</button>
                        <button class="tab-btn" onclick="openTab(event, 'analitik')">Analitik Tren Buku</button>
                    </div>

                    <div id="ringkasan" class="tab-content active">
                        <div class="stats-grid fade-in-up">
                            <a href="?page=anggota" style="text-decoration: none;">
                                <div class="stat-card">
                                    <div class="stat-info"><h3><?php echo $total_anggota; ?></h3><p>Total Siswa</p></div>
                                    <div class="stat-icon" style="background: #E3F2FD; color: #1565C0;"><i class='bx bx-group'></i></div>
                                </div>
                            </a>
                            <a href="?page=buku" style="text-decoration: none;">
                                <div class="stat-card">
                                    <div class="stat-info"><h3><?php echo $total_buku; ?></h3><p>Koleksi Buku</p></div>
                                    <div class="stat-icon" style="background: #FFF3E0; color: #EF6C00;"><i class='bx bx-book'></i></div>
                                </div>
                            </a>
                            <a href="?page=booking" style="text-decoration: none;">
                                <div class="stat-card">
                                    <div class="stat-info"><h3><?php echo $jml_notif; ?></h3><p>Request Booking</p></div>
                                    <div class="stat-icon" style="background: #FFEBEE; color: #C62828;"><i class='bx bx-timer'></i></div>
                                </div>
                            </a>
                        </div>

                        <div class="hof-container fade-in-up">
                            <div class="hof-card hof-gold">
                                <div class="hof-icon icon-gold"><i class='bx bx-trophy'></i></div>
                                <div>
                                    <span class="hof-label label-gold">Siswa Ter-Rajin</span>
                                    <h4 class="hof-title"><?php echo ($d_rajin) ? $d_rajin['nama_anggota'] : "- Belum Ada -"; ?></h4>
                                    <p class="hof-desc"><?php echo ($d_rajin) ? "Total Pinjam: <b>" . $d_rajin['total'] . " Buku</b>" : "Data masih kosong"; ?></p>
                                </div>
                            </div>

                            <div class="hof-card hof-red">
                                <div class="hof-icon icon-red"><i class='bx bxs-hot'></i></div>
                                <div>
                                    <span class="hof-label label-red">Buku Terlaris</span>
                                    <h4 class="hof-title"><?php echo ($d_laku) ? $d_laku['judul_buku'] : "- Belum Ada -"; ?></h4>
                                    <p class="hof-desc"><?php echo ($d_laku) ? "Total Dipinjam: <b>" . $d_laku['total'] . " Kali</b>" : "Data masih kosong"; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="card welcome-banner fade-in-up">
                            <div>
                                <h3 style="font-family: 'Playfair Display', serif; color: #3E2723; margin-bottom: 5px;">Selamat Bekerja, <?php echo htmlspecialchars($tampil['username']); ?>! ✨</h3>
                                <p style="color: #8D6E63; font-style: italic; font-size: 13px; margin: 0;">"Buku adalah jendela dunia, dan kamu adalah pemegang kuncinya."</p>
                            </div>
                            <div class="welcome-icon"><i class='bx bx-coffee'></i></div>
                        </div>
                    </div>

                    <div id="analitik" class="tab-content">
                        <div class="chart-full-wrapper fade-in-up">
                            <h3 style="font-family: 'Playfair Display', serif; margin-bottom: 25px; color: #3E2723;">Visualisasi 5 Buku Terpopuler</h3>
                            <div style="width: 100%; height: 400px;">
                                <canvas id="trendingChart"></canvas>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
    </div>

<script>
    // LOGIKA PINDAH TAB
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; tabcontent[i].classList.remove("active"); }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) { tablinks[i].classList.remove("active"); }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    // KONFIGURASI GRAFIK
    const ctx = document.getElementById('trendingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_chart); ?>,
            datasets: [{
                label: 'Jumlah Pinjam',
                data: <?php echo json_encode($data_chart); ?>,
                backgroundColor: 'rgba(141, 110, 99, 0.8)',
                borderColor: '#3E2723',
                borderWidth: 1,
                borderRadius: 12,
                barThickness: 'flex',
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F0F0F0' } },
                x: { grid: { display: false } }
            }
        }
    });

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
            reverseButtons: true,
            didOpen: () => {
                const popup = Swal.getPopup();
                popup.style.fontFamily = "'Montserrat', sans-serif";
                popup.style.borderRadius = "25px";
            }
        }).then((result) => {
            if (result.isConfirmed) { window.location.href = 'logout.php'; }
        })
    }

    const toggleBtn = document.getElementById('darkModeBtn');
    const body = document.body;
    toggleBtn.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    });
    if (localStorage.getItem('theme') === 'dark') body.classList.add('dark-mode');
</script>
</body>
</html>