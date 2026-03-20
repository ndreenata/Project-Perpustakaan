<?php
// 1. Pelacak Error (Agar tetap aman)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'inc/koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:katalog.php");
    exit;
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);

// 2. Ambil data buku detail
$sql = "SELECT b.*, p.nama_penerbit 
        FROM tbl_buku b 
        LEFT JOIN tbl_penerbit p ON b.id_penerbit = p.id_penerbit 
        WHERE b.id_buku = '$id_buku'";

$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_array($query);

if (!$data) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='katalog.php';</script>";
    exit;
}

// 3. Hitung Rating
$q_avg = mysqli_query($koneksi, "SELECT AVG(rating) as rata_rata, COUNT(id_ulasan) as total FROM tbl_ulasan WHERE id_buku = '$id_buku'");
$d_avg = mysqli_fetch_array($q_avg);
$rating = round($d_avg['rata_rata'] ?? 0, 1);

// --- KODE ASLI FITUR BARU: KOMUNITAS FAVORIT ---
$q_fav_users = mysqli_query($koneksi, "SELECT a.nama_anggota FROM tbl_favorit f JOIN tbl_anggota a ON f.id_anggota = a.id_anggota WHERE f.id_buku = '$id_buku' ORDER BY f.id_favorit DESC LIMIT 5");
$total_fav = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_favorit FROM tbl_favorit WHERE id_buku = '$id_buku'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eksplorasi | <?php echo $data['judul_buku']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { 
            --primary: #3E2723; 
            --accent: #8D6E63; 
            --bg: #FDFBF9;
            --glass: rgba(255, 255, 255, 0.8);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: var(--bg); 
            background-image: radial-gradient(#D7CCC8 1px, transparent 1px);
            background-size: 30px 30px;
            color: var(--primary); 
            min-height: 100vh; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px; 
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container { 
            max-width: 1100px; 
            width: 100%;
            animation: slideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .detail-card { 
            background: var(--glass);
            backdrop-filter: blur(10px);
            border-radius: 40px; 
            overflow: hidden; 
            display: grid; 
            grid-template-columns: 420px 1fr;
            box-shadow: 0 40px 100px rgba(62, 39, 35, 0.08);
            border: 1px solid rgba(255,255,255,0.6);
        }

        .cover-side { 
            background: linear-gradient(135deg, #F5F0EB 0%, #EFEBE9 100%); 
            padding: 60px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            justify-content: center;
            border-right: 1px solid rgba(0,0,0,0.03);
        }

        .book-frame {
            width: 100%;
            aspect-ratio: 3/4.5;
            background: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 20px 20px 60px rgba(62, 39, 35, 0.2);
            position: relative;
            overflow: hidden;
        }

        .book-frame i { font-size: 100px; color: rgba(255,255,255,0.15); }

        .info-side { padding: 70px; background: #fff; position: relative; }
        
        .title { 
            font-family: 'Playfair Display', serif; 
            font-size: 48px; 
            font-weight: 900;
            line-height: 1.1; 
            margin-bottom: 10px; 
            color: var(--primary);
        }

        /* STYLE INFO STATS (SEPERTI ULASAN) */
        .social-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .social-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .social-item i { font-size: 18px; }
        .social-item b { font-size: 14px; color: var(--primary); }
        .social-item span { font-size: 12px; color: #BBB; font-weight: 500; }

        .meta-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 20px; 
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #F0F0F0;
            border-bottom: 1px solid #F0F0F0;
        }

        .meta-item span { display: block; font-size: 10px; font-weight: 700; color: #BBB; text-transform: uppercase; margin-bottom: 6px; }
        .meta-item b { font-size: 15px; color: var(--primary); }

        .synopsis-text { font-size: 15px; line-height: 1.9; color: #5D4037; text-align: justify; margin-bottom: 40px; }

        .action-btns { display: flex; gap: 20px; }
        .btn-booking { 
            flex: 1; padding: 20px; background: var(--primary); color: #fff; 
            text-decoration: none; border-radius: 18px; text-align: center; 
            font-weight: 700; font-size: 15px; transition: 0.4s;
        }
        .btn-booking:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(62, 39, 35, 0.2); }

        .btn-back { 
            width: 65px; height: 65px; border-radius: 18px; border: 2px solid #F0F0F0; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 28px; color: var(--primary); text-decoration: none; 
        }

        @media (max-width: 950px) { .detail-card { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="detail-card">
        <div class="cover-side">
            <div class="book-frame"><i class='bx bxs-book-alt'></i></div>
            <div style="margin-top: 35px; text-align: center;">
                <span style="font-size: 10px; font-weight: 800; color: #A1887F; letter-spacing: 2px;">PENERBIT</span>
                <p style="font-weight: 700; color: var(--primary); font-size: 16px;"><?php echo $data['nama_penerbit']; ?></p>
            </div>
        </div>

        <div class="info-side">
            <h1 class="title"><?php echo $data['judul_buku']; ?></h1>

            <div class="social-row">
                <div class="social-item">
                    <i class='bx bxs-star' style="color: #FFA000;"></i>
                    <b><?php echo ($rating > 0) ? $rating : '0'; ?></b>
                    <span>(<?php echo $d_avg['total']; ?> ulasan)</span>
                </div>
                <div class="social-item">
                    <i class='bx bxs-heart' style="color: #e74c3c;"></i>
                    <b><?php echo $total_fav; ?></b>
                    <span>(difavoritkan)</span>
                </div>
            </div>
            
            <div class="meta-grid">
                <div class="meta-item">
                    <span>Halaman</span>
                    <b><?php echo $data['jumlah_halaman']; ?> Hal</b>
                </div>
                <div class="meta-item">
                    <span>Tahun Terbit</span>
                    <b><?php echo $data['tahun_terbit']; ?></b>
                </div>
                <div class="meta-item">
                    <span>Stok</span>
                    <b><?php echo $data['jumlah_buku']; ?> Buku</b>
                </div>
            </div>

            <div class="synopsis-text">
                <p><?php echo nl2br($data['sinopsis_buku']); ?></p>
            </div>

            <div class="action-btns">
                <a href="katalog.php" class="btn-back"><i class='bx bx-left-arrow-alt'></i></a>
                <?php if($data['jumlah_buku'] > 0): ?>
                    <a href="katalog.php?aksi=booking&id=<?php echo $id_buku; ?>" class="btn-booking">Pesan Sekarang</a>
                <?php else: ?>
                    <a href="katalog.php?aksi=booking&id=<?php echo $id_buku; ?>&mode=antre" class="btn-booking" style="background: var(--accent);">Ikut Antrean</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>