<?php
session_start();
include '../inc/koneksi.php'; // Jalur diperbaiki

// 1. CEK LOGIN & PARAMETER
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../login.php"); // Jalur diperbaiki
    exit;
}

if (!isset($_GET['id'])) {
    header("location:../member.php"); // Jalur diperbaiki
    exit;
}

$id_buku = $_GET['id'];
$id_user = $_SESSION['ses_id'];

// 2. AMBIL INFO BUKU
$q_buku = mysqli_query($koneksi, "SELECT judul_buku FROM tbl_buku WHERE id_buku = '$id_buku'");
$d_buku = mysqli_fetch_array($q_buku);

// 3. LOGIKA SIMPAN ULASAN
if (isset($_POST['kirim_ulasan'])) {
    $rating = $_POST['rating'];
    $ulasan = mysqli_real_escape_string($koneksi, $_POST['ulasan']);
    $tgl = date('Y-m-d');

    $sql = "INSERT INTO tbl_ulasan (id_buku, id_anggota, rating, ulasan, tgl_ulasan) 
            VALUES ('$id_buku', '$id_user', '$rating', '$ulasan', '$tgl')";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>
            alert('Terima kasih! Ulasan kamu sangat berarti.');
            window.location='../member.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beri Rating | <?= $d_buku['judul_buku']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body { background: #F9F7F5; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .rating-card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        .stars { display: flex; flex-direction: row-reverse; justify-content: center; gap: 10px; margin: 20px 0; }
        .stars input { display: none; }
        .stars label { font-size: 40px; color: #DDD; cursor: pointer; transition: 0.3s; }
        .stars input:checked ~ label, .stars label:hover, .stars label:hover ~ label { color: #FFA000; }
        textarea { width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #EEE; outline: none; resize: none; font-size: 13px; box-sizing: border-box; }
        .btn-kirim { width: 100%; padding: 12px; background: #3E2723; color: #fff; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-kirim:hover { background: #5D4037; transform: translateY(-2px); }
    </style>
</head>
<body>

    <div class="rating-card">
        <i class='bx bxs-star-half' style="font-size: 50px; color: #FFA000;"></i>
        <h2 style="margin: 10px 0 5px; color: #3E2723;">Beri Rating</h2>
        <p style="font-size: 13px; color: #888; margin-bottom: 20px;">Bagaimana pendapatmu tentang buku <br><b>"<?= $d_buku['judul_buku']; ?>"</b>?</p>

        <form action="" method="POST">
            <div class="stars">
                <input type="radio" name="rating" value="5" id="5"><label for="5">★</label>
                <input type="radio" name="rating" value="4" id="4"><label for="4">★</label>
                <input type="radio" name="rating" value="3" id="3"><label for="3">★</label>
                <input type="radio" name="rating" value="2" id="2"><label for="2">★</label>
                <input type="radio" name="rating" value="1" id="1" required><label for="1">★</label>
            </div>

            <textarea name="ulasan" rows="4" placeholder="Tulis komentar singkat kamu di sini..."></textarea>

            <button type="submit" name="kirim_ulasan" class="btn-kirim">Kirim Ulasan</button>
            <a href="../member.php" style="display: block; margin-top: 15px; font-size: 12px; color: #AAA; text-decoration: none;">Nanti Saja</a>
        </form>
    </div>

</body>
</html>