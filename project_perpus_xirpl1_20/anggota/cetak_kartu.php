<?php
include '../inc/koneksi.php';

// Tangkap ID
if (isset($_GET['id_anggota'])) {
    $id = $_GET['id_anggota'];
} else {
    die("Error: ID Anggota tidak ditemukan.");
}

$sql = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE id_anggota='$id'");
$data = mysqli_fetch_array($sql);

if (!$data) {
    die("Error: Data Anggota tidak ditemukan di database.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kartu Anggota - <?php echo $data['nama_anggota']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #eee; 
            display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0;
        }
        .id-card {
            width: 350px; height: 210px;
            background: linear-gradient(135deg, #FFF8E1 0%, #FFFFFF 100%);
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            position: relative; overflow: hidden;
            border: 1px solid #D7CCC8; display: flex;
        }
        .card-left {
            width: 50px; background: #3E2723;
            display: flex; align-items: center; justify-content: center;
            color: #fff; writing-mode: vertical-rl; text-orientation: mixed;
            font-family: 'Playfair Display', serif; font-weight: 700;
            font-size: 12px; letter-spacing: 2px;
            border-right: 3px solid #8D6E63;
        }
        .card-right { flex: 1; padding: 20px; position: relative; }
        
        .school-name { font-size: 10px; color: #8D6E63; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        .member-name { font-family: 'Playfair Display', serif; font-size: 22px; color: #3E2723; margin: 5px 0 10px 0; font-weight: 700; line-height: 1.2; }
        
        /* CSS KHUSUS TABEL BIODATA BIAR RAPI */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            color: #5D4037;
        }
        .info-table td {
            padding: 3px 0; /* Jarak antar baris */
            vertical-align: top;
        }
        .label-col { width: 80px; font-weight: 600; } /* Lebar kolom Label */
        .colon-col { width: 10px; text-align: center; } /* Lebar kolom Titik Dua */

        .barcode {
            margin-top: 15px; height: 25px; width: 100%;
            background: repeating-linear-gradient(90deg, #333 0px, #333 2px, transparent 2px, transparent 4px, #333 4px, #333 7px);
            opacity: 0.7;
        }
        .card-footer { position: absolute; bottom: 10px; right: 20px; font-size: 9px; color: #aaa; }
        @media print { body { background: none; -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body onload="window.print()">

    <div class="id-card">
        <div class="card-left">PERPUSTAKAAN</div>
        <div class="card-right">
            <div class="school-name">Kartu Anggota</div>
            
            <h2 class="member-name"><?php echo $data['nama_anggota']; ?></h2>
            
            <table class="info-table">
                <tr>
                    <td class="label-col">ID Anggota</td>
                    <td class="colon-col">:</td>
                    <td><?php echo $data['id_anggota']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">Kelas</td>
                    <td class="colon-col">:</td>
                    <td><?php echo $data['kelas']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">No. Telp</td>
                    <td class="colon-col">:</td>
                    <td><?php echo $data['no_tlp']; ?></td>
                </tr>
            </table>

            <div class="barcode"></div>
            <div class="card-footer">Valid Member</div>
        </div>
    </div>

</body>
</html>
