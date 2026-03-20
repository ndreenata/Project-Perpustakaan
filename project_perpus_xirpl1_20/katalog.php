<?php
session_start();
include 'inc/koneksi.php';
if (!$koneksi) { die("Koneksi Gagal: " . mysqli_connect_error()); }

// 1. LOGIKA BOOKING + SISTEM ANTREAN
if (isset($_GET['aksi']) && $_GET['aksi'] == 'booking') {
    if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
        echo "<script>alert('Silakan login dulu!'); window.location='login.php';</script>";
        exit;
    }
    
    $id_buku = $_GET['id'];
    $ses_nama = $_SESSION['nama']; 
    $ses_username = $_SESSION['username'];

    // AMBIL ID ANGGOTA YANG VALID DARI DATABASE (SAMA SEPERTI DI MEMBER.PHP)
    $q_cari_id = mysqli_query($koneksi, "SELECT id_anggota FROM tbl_anggota WHERE nama_anggota='$ses_nama' OR id_anggota='$ses_username'");
    $d_cari_id = mysqli_fetch_array($q_cari_id);
    
    // Variabel ID yang akan dikirim ke database
    $id_user = ($d_cari_id) ? $d_cari_id['id_anggota'] : $_SESSION['username'];
    
    $tgl = date('Y-m-d');
    $status_booking = (isset($_GET['mode']) && $_GET['mode'] == 'antre') ? 'Antre' : 'Menunggu';

    mysqli_query($koneksi, "ALTER TABLE tbl_booking MODIFY COLUMN id_buku VARCHAR(50)");
    $cek_stok = mysqli_fetch_array(mysqli_query($koneksi, "SELECT jumlah_buku FROM tbl_buku WHERE id_buku='$id_buku'"));
    
    // Cek double booking menggunakan ID yang sudah disinkronkan
    $cek_double = mysqli_query($koneksi, "SELECT * FROM tbl_booking WHERE id_buku='$id_buku' AND id_anggota='$id_user' AND (status='Menunggu' OR status='Antre')");

    if($id_user == '') {
        echo "<script>alert('Error: ID Anggota tidak ditemukan!'); window.location='login.php';</script>";
    } elseif($status_booking == 'Menunggu' && $cek_stok['jumlah_buku'] < 1) {
        echo "<script>alert('Stok habis! Silakan gunakan fitur Antre.'); window.location='katalog.php';</script>";
    } elseif(mysqli_num_rows($cek_double) > 0){
        echo "<script>alert('Kamu sudah dalam daftar booking/antrean buku ini!'); window.location='member.php';</script>";
    } else {
        $query_simpan = "INSERT INTO tbl_booking (id_buku, id_anggota, tgl_booking, status) VALUES ('$id_buku', '$id_user', '$tgl', '$status_booking')";
        $simpan = mysqli_query($koneksi, $query_simpan);
        
        if($simpan){ 
            $pesan = ($status_booking == 'Antre') ? 'BERHASIL MASUK ANTREAN!' : 'BERHASIL BOOKING!';
            echo "<script>alert('$pesan'); window.location='member.php';</script>"; 
        } else {
            echo "<script>alert('Gagal Simpan: " . mysqli_error($koneksi) . "'); window.location='katalog.php';</script>";
        }
    }
    exit;
}


// 2. QUERY DATA
$where = "";
$keyword_value = "";
if (isset($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);
    $keyword_value = htmlspecialchars($_GET['keyword']);
    $where = "WHERE tbl_buku.judul_buku LIKE '%$keyword%' OR tbl_penerbit.nama_penerbit LIKE '%$keyword%'";
}

$sql = "SELECT tbl_buku.*, tbl_penerbit.nama_penerbit FROM tbl_buku LEFT JOIN tbl_penerbit ON tbl_buku.id_penerbit = tbl_penerbit.id_penerbit $where ORDER BY tbl_buku.id_buku DESC";
$query = mysqli_query($koneksi, $sql);

$q_new = mysqli_query($koneksi, "SELECT id_buku FROM tbl_buku ORDER BY id_buku DESC LIMIT 5");
$new_ids = [];
while($rn = mysqli_fetch_assoc($q_new)) { $new_ids[] = $rn['id_buku']; }

$q_populer = mysqli_query($koneksi, "
    SELECT b.*, pnr.nama_penerbit, COUNT(p.id_buku) as total_pinjam 
    FROM tbl_buku b
    LEFT JOIN tbl_penerbit pnr ON b.id_penerbit = pnr.id_penerbit
    JOIN tbl_peminjaman p ON b.id_buku = p.id_buku
    GROUP BY b.id_buku 
    ORDER BY total_pinjam DESC 
    LIMIT 3
");

function cekFavorit($koneksi, $id_buku) {
    if(!isset($_SESSION['ses_id'])) return false;
    $id_user = $_SESSION['ses_id'];
    $cek = mysqli_query($koneksi, "SELECT id_favorit FROM tbl_favorit WHERE id_anggota='$id_user' AND id_buku='$id_buku'");
    return mysqli_num_rows($cek) > 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku | Koleksi Eksklusif</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Playfair+Display:ital,wght@0,700;0,800;1,700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* CSS Tambahan untuk Scroll Halus */
        html { scroll-behavior: smooth; }
        
        @keyframes revealPage {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        body { background: #FDFBF9; animation: revealPage 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }

        .scroll-reveal {
            opacity: 0; transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.2, 0.8, 0.2, 1), transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .scroll-reveal.active { opacity: 1; transform: translateY(0); }

        .ktlg-nav { 
            display: flex; justify-content: space-between; align-items: center; 
            padding: 25px 50px; width: 100%; position: absolute; top: 0; z-index: 100;
        }

        .user-profile-group {
            display: flex; align-items: center; background: #fff; 
            padding: 5px 10px 5px 18px; border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); gap: 15px;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .btn-fav-nav {
            color: #e74c3c; font-size: 20px; display: flex; align-items: center;
            text-decoration: none; transition: 0.3s;
        }
        .btn-fav-nav:hover { transform: scale(1.2); }
        
        .nav-profile-link {
            display: flex; align-items: center; gap: 10px; text-decoration: none;
            border-left: 1px solid #EEE; padding-left: 15px;
        }
        .ktlg-btn-login { text-decoration: none; color: #3E2723; font-size: 13px; font-weight: 700; }
        .nav-avatar {
            width: 32px; height: 32px; background: linear-gradient(135deg, #3E2723, #5D4037);
            color: #fff; border-radius: 50%; display: flex; align-items: center; 
            justify-content: center; font-size: 12px; font-weight: 800;
            font-family: 'Playfair Display', serif;
        }

        .premium-banner-wrapper {
            width: 100%; height: 320px; margin-top: -60px; position: relative; z-index: 10;
        }
        .banner-card {
            width: 100%; height: 100%; border-radius: 35px; position: relative;
            overflow: hidden; display: flex; align-items: center;
            box-shadow: 0 30px 60px rgba(62, 39, 35, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .banner-image {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-size: cover; background-position: center;
            transition: transform 1.5s ease, opacity 1s ease; z-index: 1;
        }
        .glass-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(105deg, rgba(62,39,35,0.95) 0%, rgba(62,39,35,0.4) 60%, transparent 100%);
            z-index: 2;
        }
        .banner-content { position: relative; z-index: 3; padding: 0 80px; color: #fff; transition: 0.8s ease; }
        .badge-premium {
            background: rgba(255, 213, 79, 0.2); color: #FFD54F; padding: 6px 15px;
            border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase;
            letter-spacing: 2px; border: 1px solid rgba(255, 213, 79, 0.3); backdrop-filter: blur(5px);
        }

        .btn-fav {
            position: absolute; background: rgba(255,255,255,0.9);
            width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; color: #BBB; cursor: pointer; transition: 0.3s; z-index: 10;
            border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .btn-fav:hover { color: #e74c3c; transform: scale(1.1); }
        .btn-fav.is-favorited { color: #e74c3c; }

        .populer-card {
            background: #fff; border-radius: 25px; padding: 25px; display: flex; gap: 20px;
            border: 1px solid rgba(0,0,0,0.03); box-shadow: 0 10px 30px rgba(62, 39, 35, 0.05);
            position: relative; transition: 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); overflow: hidden;
        }
        .populer-card:hover { transform: translateY(-12px) scale(1.02); box-shadow: 0 25px 50px rgba(62, 39, 35, 0.12); }
        
        .populer-rank { position: absolute; top: 0; right: 0; background: #3E2723; color: #fff; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-weight: 800; border-radius: 0 0 0 20px; font-size: 14px; }
        .populer-img-wrapper { width: 100px; height: 145px; background: linear-gradient(135deg, #F5F0EB, #EFEBE9); border-radius: 15px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; cursor: pointer; transition: 0.3s; }

        .populer-info h4 a, .ktlg-title a { text-decoration: none; color: inherit; transition: 0.3s; }
        .populer-info h4 a:hover, .ktlg-title a:hover { color: #8D6E63; }

        .populer-stats { display: inline-flex; align-items: center; gap: 6px; background: #FFF8E1; color: #FFA000; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; margin-bottom: 12px; border: 1px solid #FFECB3; }
        .btn-booking-premium { text-decoration: none; font-size: 11px; font-weight: 800; color: #3E2723; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; padding: 8px 0; border-bottom: 2px solid #3E2723; cursor: pointer; text-transform: uppercase; }

        .ktlg-img-box { cursor: pointer; overflow: hidden; position: relative; }
        .ktlg-img-box a { display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; text-decoration: none; color: inherit; }

        .social-container { display: flex; align-items: center; gap: 15px; margin-bottom: 8px; flex-wrap: wrap; }
        .social-item { display: flex; align-items: center; gap: 4px; }

        .swal2-popup-custom { border-radius: 30px !important; padding: 2em !important; background: #fff url('https://www.transparenttextures.com/patterns/cream-paper.png') !important; }
    </style>
    <link rel="stylesheet" href="style.css?v=<?= time(); ?>">
</head>
<body class="katalog-premium-body">

    <div class="ktlg-hero">
        <nav class="ktlg-nav">
            <div class="ktlg-brand"><i class='bx bxs-book-heart'></i> Perpustakaan</div>
            <div class="nav-right">
                <?php if (isset($_SESSION['status']) && $_SESSION['status'] == "login") { 
                    $inisial_nav = strtoupper(substr($_SESSION['nama'], 0, 1));
                ?>
                    <div class="user-profile-group">
                        <a href="favorit.php" class="btn-fav-nav" title="Buku Favorit Saya">
                            <i class='bx bxs-heart'></i>
                        </a>
                        <a href="member.php" class="nav-profile-link">
                            <span class="ktlg-btn-login">Halo, <?= $_SESSION['nama']; ?></span>
                            <div class="nav-avatar"><?= $inisial_nav; ?></div>
                        </a>
                    </div>
                <?php } else { ?>
                    <a href="login.php" class="ktlg-btn-login"><i class='bx bx-log-in-circle'></i> Login Area</a>
                <?php } ?>
            </div>
        </nav>
        <div class="ktlg-content">
            <h1 class="ktlg-judul">Temukan Jendela Duniamu</h1>
            <p class="ktlg-sub">Cari koleksi buku terbaru dan booking sekarang.</p>
            <form action="" method="GET" class="ktlg-search-wrap">
                <input type="text" name="keyword" class="ktlg-input" placeholder="Cari judul buku..." value="<?= $keyword_value; ?>">
                <button type="submit" class="ktlg-btn-cari">Cari</button>
            </form>
        </div>
    </div>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="premium-banner-wrapper scroll-reveal">
            <div class="banner-card">
                <div class="banner-image" id="bgBanner" style="background-image: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1200&q=80');"></div>
                <div class="glass-overlay"></div>
                <div class="banner-content" id="bannerContent">
                    <span class="badge-premium" id="badgeBanner">Daily Inspiration</span>
                    <h2 id="titleBanner" style="font-family: 'Playfair Display', serif; font-size: 42px; margin: 15px 0; line-height: 1.1;">Perluas Cakrawala <br>Dengan Membaca</h2>
                    <p id="descBanner" style="opacity: 0.8; font-size: 15px; max-width: 450px; line-height: 1.8;">Membaca satu bab sehari dapat meningkatkan daya ingat dan konsentrasi kamu hingga 40%.</p>
                    <div style="margin-top: 30px;">
                        <a href="#daftar-koleksi" class="btn-booking-premium" style="color: #FFD54F; border-color: #FFD54F; text-decoration: none;">MULAI EKSPLORASI &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(mysqli_num_rows($q_populer) > 0) { ?>
    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <h2 style="font-family: 'Playfair Display', serif; color: #3E2723; font-size: 36px; font-weight: 800; margin-bottom: 30px;">Paling Banyak Dibaca</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 30px;">
            <?php 
            $rank_pop = 1;
            while($pop = mysqli_fetch_array($q_populer)) { 
                $id_pop = $pop['id_buku'];
                $q_rate_pop = mysqli_query($koneksi, "SELECT AVG(rating) as rata_rata, COUNT(id_ulasan) as total FROM tbl_ulasan WHERE id_buku = '$id_pop'");
                $d_rate_pop = mysqli_fetch_array($q_rate_pop);
                $rating_pop = round($d_rate_pop['rata_rata'], 1);
                
                $q_fav_count = mysqli_query($koneksi, "SELECT COUNT(id_favorit) as total_fav FROM tbl_favorit WHERE id_buku = '$id_pop'");
                $d_fav_count = mysqli_fetch_array($q_fav_count);
                $is_faved = cekFavorit($koneksi, $id_pop);
            ?>
                <div class="populer-card">
                    <div class="populer-rank">#<?= $rank_pop++; ?></div>
                    <button class="btn-fav <?= ($is_faved) ? 'is-favorited' : ''; ?>" style="top:auto; bottom:25px; left:25px;" onclick="toggleFavorit('<?= $pop['id_buku']; ?>')">
                        <i class='bx <?= ($is_faved) ? 'bxs-heart' : 'bx-heart'; ?>'></i>
                    </button>
                    <a href="detail.php?id=<?= $pop['id_buku']; ?>" class="populer-img-wrapper"><i class='bx bxs-book-heart'></i></a>
                    <div class="populer-info">
                        <div class="populer-stats"><i class='bx bxs-flame bx-tada'></i> <?= $pop['total_pinjam']; ?> PINJAM</div>
                        <div class="social-container">
                            <div class="social-item">
                                <i class='bx bxs-star' style="color: #FFA000; font-size: 14px;"></i>
                                <b style="font-size: 12px; color: #3E2723;"><?= ($rating_pop > 0 ? $rating_pop : '0'); ?></b>
                                <span style="color: #BBB; font-size: 11px;">(<?= $d_rate_pop['total']; ?> ulasan)</span>
                            </div>
                            <div class="social-item">
                                <i class='bx bxs-heart' style="color: #e74c3c; font-size: 14px;"></i>
                                <b style="font-size: 12px; color: #3E2723;"><?= $d_fav_count['total_fav']; ?></b>
                                <span style="color: #BBB; font-size: 11px;">(difavoritkan)</span>
                            </div>
                        </div>
                        <h4><a href="detail.php?id=<?= $pop['id_buku']; ?>"><?= $pop['judul_buku']; ?></a></h4>
                        <div onclick="konfirmasiBooking('<?= $pop['id_buku']; ?>', '<?= addslashes($pop['judul_buku']); ?>', 'normal')" class="btn-booking-premium">BOOKING SEKARANG &rarr;</div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;"><hr style="border: 0; border-top: 1px solid #EEE; margin: 20px 0 50px;"></div>
    <?php } ?>

    <div class="ktlg-grid">
        <div style="grid-column: 1/-1; margin-bottom: 30px;">
             <h2 id="daftar-koleksi" style="font-family: 'Playfair Display', serif; color: #3E2723; font-size: 30px; font-weight: 800;">Eksplorasi Koleksi</h2>
        </div>
        <?php 
        if(mysqli_num_rows($query) > 0){
            while($data = mysqli_fetch_array($query)){
                $stok = $data['jumlah_buku'];
                $id_buku_skrg = $data['id_buku'];
                $q_avg = mysqli_query($koneksi, "SELECT AVG(rating) as rata_rata, COUNT(id_ulasan) as total FROM tbl_ulasan WHERE id_buku = '$id_buku_skrg'");
                $d_avg = mysqli_fetch_array($q_avg);
                $rating = round($d_avg['rata_rata'], 1);
                $q_f = mysqli_query($koneksi, "SELECT COUNT(id_favorit) as total_fav FROM tbl_favorit WHERE id_buku = '$id_buku_skrg'");
                $d_f = mysqli_fetch_array($q_f);
                
                $is_new = in_array($data['id_buku'], $new_ids);
                $is_faved_koleksi = cekFavorit($koneksi, $id_buku_skrg);
        ?>
        <div class="ktlg-card scroll-reveal">
            <button class="btn-fav <?= ($is_faved_koleksi) ? 'is-favorited' : ''; ?>" style="<?= $is_new ? 'top: 50px;' : 'top: 15px;'; ?>" onclick="toggleFavorit('<?= $data['id_buku']; ?>')">
                <i class='bx <?= ($is_faved_koleksi) ? 'bxs-heart' : 'bx-heart'; ?>'></i>
            </button>
            <?php if ($is_new) { echo '<span class="ktlg-badge-new">NEW ARRIVAL</span>'; } ?>
            <div class="ktlg-img-box">
                <a href="detail.php?id=<?= $data['id_buku']; ?>">
                    <span class="ktlg-badge-stok" style="background:<?= ($stok>0)?'#E8F5E9':'#FFEBEE'; ?>; color:<?= ($stok>0)?'#2E7D32':'#C62828'; ?>; font-weight:800; font-size:9px;"><?= ($stok>0)?'Stok: '.$stok:'Habis'; ?></span>
                    <i class='bx bxs-book-open ktlg-icon-book'></i>
                </a>
            </div>
            <div class="ktlg-body">
                <div class="ktlg-meta"><span><?= $data['tahun_terbit']; ?></span><span><?= $data['jumlah_halaman']; ?> HALAMAN</span></div>
                <div class="social-container">
                    <div class="social-item">
                        <i class='bx bxs-star' style="color: #FFA000; font-size: 14px;"></i>
                        <b><?= ($rating > 0 ? $rating : '0'); ?></b>
                        <span style="color: #BBB; font-size: 11px;">(<?= $d_avg['total']; ?>)</span>
                    </div>
                    <div class="social-item">
                        <i class='bx bxs-heart' style="color: #e74c3c; font-size: 14px;"></i>
                        <b><?= $d_f['total_fav']; ?></b>
                        <span style="color: #BBB; font-size: 11px;">(save)</span>
                    </div>
                </div>

                <h3 class="ktlg-title"><a href="detail.php?id=<?= $data['id_buku']; ?>"><?= $data['judul_buku']; ?></a></h3>
                <div class="ktlg-text" style="font-size: 12px; color: #777; line-height: 1.6;"><?= substr($data['sinopsis_buku'], 0, 90) . '...'; ?></div>
                <button onclick="konfirmasiBooking('<?= $data['id_buku']; ?>', '<?= addslashes($data['judul_buku']); ?>', '<?= ($stok>0)?'normal':'antre'; ?>')" class="ktlg-btn-book" style="width:100%; border:none; cursor:pointer; font-weight: 700; background:<?= ($stok>0)?'':'#8D6E63'; ?>;"><?= ($stok>0)?'Booking Sekarang':'Masuk Antrean'; ?></button>
            </div>
        </div>
        <?php } } ?>
    </div>

    <script>
        // JS BANNER AUTO-UPDATE (SMOOTH FADE)
        const banners = [
            { bg: 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1200&q=80', badge: 'Daily Inspiration', title: 'Perluas Cakrawala <br>Dengan Membaca', desc: 'Membaca satu bab sehari dapat meningkatkan daya ingat dan konsentrasi kamu hingga 40%.' },
            { bg: 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80', badge: 'New Collection', title: 'Mahakarya Sastra <br>Edisi Terbatas', desc: 'Koleksi buku klasik kini tersedia dalam versi hard-cover eksklusif di rak VIP.' },
            { bg: 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=1200&q=80', badge: 'Library Info', title: 'Klub Buku <br>Diskusi Senja', desc: 'Bergabunglah dengan komunitas pembaca kami setiap hari Jumat sore di taman perpus.' }
        ];

        let current = 0;
        function updateBanner() {
            current = (current + 1) % banners.length;
            const bg = document.getElementById('bgBanner');
            const content = document.getElementById('bannerContent');
            
            content.style.opacity = '0';
            content.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                bg.style.backgroundImage = `url('${banners[current].bg}')`;
                document.getElementById('badgeBanner').innerHTML = banners[current].badge;
                document.getElementById('titleBanner').innerHTML = banners[current].title;
                document.getElementById('descBanner').innerHTML = banners[current].desc;
                content.style.opacity = '1';
                content.style.transform = 'translateX(0)';
            }, 600);
        }
        setInterval(updateBanner, 5000);

        function toggleFavorit(idBuku) {
            fetch('proses_favorit.php?id=' + idBuku)
                .then(response => response.text())
                .then(data => {
                    if(data === 'login') { Swal.fire('Opps!', 'Silakan login dulu!', 'warning'); } 
                    else if(data === 'tambah') { Swal.fire({ icon: 'success', title: 'Tersimpan!', timer: 1000, showConfirmButton: false }).then(() => location.reload()); } 
                    else { Swal.fire({ icon: 'info', title: 'Dihapus!', timer: 1000, showConfirmButton: false }).then(() => location.reload()); }
                });
        }
        function konfirmasiBooking(id, judul, mode) {
            const isAntre = mode === 'antre';
            Swal.fire({
                title: isAntre ? 'Ikut Antrean?' : 'Konfirmasi Pesanan',
                html: `Apakah Anda ingin memesan mahakarya <br><b>"${judul}"</b>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3E2723',
                cancelButtonColor: '#D7CCC8',
                confirmButtonText: 'Ya, Lanjut',
                customClass: { popup: 'swal2-popup-custom' }
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = `katalog.php?aksi=booking&id=${id}${isAntre ? '&mode=antre' : ''}`; }
            });
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add('active'); } });
        }, { threshold: 0.1 });
        document.querySelectorAll('.scroll-reveal').forEach((el) => observer.observe(el));
    </script>
</body>
</html>
