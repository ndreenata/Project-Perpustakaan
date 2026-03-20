<?php
session_start();
include 'inc/koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    echo "<script>alert('Silakan login dulu!'); window.location='login.php';</script>";
    exit;
}

$id_user = $_SESSION['ses_id'];

$sql = "SELECT f.id_favorit, b.*, p.nama_penerbit 
        FROM tbl_favorit f
        JOIN tbl_buku b ON f.id_buku = b.id_buku
        LEFT JOIN tbl_penerbit p ON b.id_penerbit = p.id_penerbit
        WHERE f.id_anggota = '$id_user'
        ORDER BY f.id_favorit DESC";
$query = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorit Saya | Koleksi Pribadi</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Playfair+Display:wght@700;800&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root { 
            --primary: #3E2723; 
            --accent: #8D6E63; 
            --bg: #FDFBF9; 
            --white: #ffffff;
            --danger: #e74c3c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: var(--bg); 
            color: var(--primary); 
            min-height: 100vh; 
            padding: 40px 20px;
            background-image: radial-gradient(#D7CCC8 0.5px, transparent 0.5px);
            background-size: 20px 20px;
            overflow-x: hidden;
        }

        .container { max-width: 1100px; margin: 0 auto; }

        /* HEADER SECTION - UKURAN TEKS DIPERKECIL */
        .header { 
            margin-bottom: 40px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            padding-bottom: 15px;
        }

        .title { 
            font-family: 'Playfair Display', serif; 
            font-size: 28px; /* Teks dikecilkan sesuai permintaan */
            font-weight: 800; 
            color: var(--primary);
            line-height: 1.2;
        }

        .btn-back { 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            text-decoration: none; 
            color: var(--accent); 
            font-weight: 600; 
            font-size: 13px; 
            margin-bottom: 5px;
            transition: 0.3s;
        }
        .btn-back:hover { color: var(--primary); transform: translateX(-5px); }

        /* GRID SYSTEM */
        .grid-fav { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            gap: 25px; 
        }

        /* ANIMASI MASUK (FADE IN UP) */
        @keyframes revealCard {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .card-fav { 
            background: var(--white); 
            border-radius: 25px; 
            overflow: hidden; 
            box-shadow: 0 15px 35px rgba(62, 39, 35, 0.05); 
            position: relative;
            transition: 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.8);
            opacity: 0; /* Awalnya tidak terlihat */
            animation: revealCard 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .card-fav:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 25px 50px rgba(62, 39, 35, 0.1); 
        }

        .img-box { 
            height: 180px; 
            background: linear-gradient(135deg, #F5F0EB 0%, #EFEBE9 100%); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }

        .img-box i { font-size: 60px; color: #D7CCC8; transition: 0.5s; }
        .card-fav:hover .img-box i { transform: scale(1.1) rotate(-5deg); color: var(--accent); }

        .btn-remove { 
            position: absolute; 
            top: 15px; 
            right: 15px; 
            background: var(--white); 
            width: 35px; 
            height: 35px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: var(--danger); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
            border: none; 
            cursor: pointer;
            z-index: 10;
            transition: 0.3s;
        }
        .btn-remove:hover { 
            background: var(--danger); 
            color: var(--white); 
            transform: scale(1.1) rotate(90deg); 
        }

        .body-fav { padding: 20px; }

        .penerbit-tag {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent);
            margin-bottom: 5px;
            display: block;
        }

        .book-title { 
            font-family: 'Playfair Display', serif;
            font-size: 16px; 
            font-weight: 700; 
            margin-bottom: 15px; 
            height: 45px; 
            overflow: hidden; 
            line-height: 1.4;
            color: var(--primary);
        }

        .btn-read { 
            display: flex; 
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%; 
            padding: 12px; 
            background: var(--primary); 
            color: var(--white); 
            text-decoration: none; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: 600; 
            transition: 0.3s;
        }
        
        .btn-read:hover { 
            background: var(--accent); 
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 30px;
            border: 2px dashed #D7CCC8;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <a href="katalog.php" class="btn-back"><i class='bx bx-left-arrow-alt'></i> Katalog</a>
            <h1 class="title">Buku Favorit Kamu</h1>
        </div>
        <i class='bx bxs-heart' style="font-size: 35px; color: var(--danger); opacity: 0.8;"></i>
    </div>

    <div class="grid-fav">
        <?php if(mysqli_num_rows($query) > 0) { 
            $delay = 0.1;
            while($data = mysqli_fetch_array($query)) { 
        ?>
            <div class="card-fav" id="item-<?= $data['id_buku']; ?>" style="animation-delay: <?= $delay; ?>s">
                <button class="btn-remove" onclick="hapusFavorit('<?= $data['id_buku']; ?>', '<?= addslashes($data['judul_buku']); ?>')" title="Hapus">
                    <i class='bx bxs-trash-alt'></i>
                </button>
                <div class="img-box">
                    <i class='bx bxs-book-heart'></i>
                </div>
                <div class="body-fav">
                    <span class="penerbit-tag"><?= $data['nama_penerbit']; ?></span>
                    <h3 class="book-title"><?= $data['judul_buku']; ?></h3>
                    <a href="detail.php?id=<?= $data['id_buku']; ?>" class="btn-read">
                        DETAIL BUKU <i class='bx bx-right-arrow-alt'></i>
                    </a>
                </div>
            </div>
        <?php 
            $delay += 0.1; // Menambah delay untuk setiap kartu berikutnya
            } 
        } else { ?>
            <div class="empty-state">
                <i class='bx bx-heart-circle' style="font-size: 60px; color: #EEE; margin-bottom: 15px; display: block;"></i>
                <p style="color: #A1887F; margin-bottom: 20px;">Daftar favoritmu masih kosong.</p>
                <a href="katalog.php" class="btn-read" style="display: inline-flex; width: auto; padding: 12px 30px;">Cari Buku</a>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    function hapusFavorit(idBuku, judul) {
        Swal.fire({
            title: 'Hapus Favorit?',
            html: `Lepaskan <b>"${judul}"</b>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#D7CCC8',
            confirmButtonText: 'Ya, Hapus',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('proses_favorit.php?id=' + idBuku)
                .then(response => response.text())
                .then(data => {
                    if(data === 'hapus') {
                        const element = document.getElementById('item-' + idBuku);
                        element.style.transform = "scale(0.8)";
                        element.style.opacity = "0";
                        setTimeout(() => {
                            element.style.display = 'none';
                        }, 400);
                        Swal.fire({ icon: 'success', title: 'Dihapus', timer: 1000, showConfirmButton: false });
                    }
                });
            }
        });
    }
</script>

</body>
</html>