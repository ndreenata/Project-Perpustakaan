<?php
include 'inc/koneksi.php';

if (isset($_POST['btn_register'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $no_tlp   = mysqli_real_escape_string($koneksi, $_POST['no_tlp']);
    $kelas    = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // Cek dulu apakah username sudah dipakai di tbl_anggota
    $cek_user = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE id_anggota='$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Ups! Username sudah dipakai orang lain.');</script>";
    } else {
        // SIMPAN HANYA KE SATU TABEL: tbl_anggota
        // Kita tidak masukkan email dulu supaya tidak error kolom
        $query_anggota = "INSERT INTO tbl_anggota (id_anggota, nama_anggota, kelas, no_tlp, status, password) 
                          VALUES ('$username', '$nama', '$kelas', '$no_tlp', 'Aktif', '$password')";
        
        if(mysqli_query($koneksi, $query_anggota)) {
            echo "<script>
                alert('Hore! Pendaftaran kamu berhasil. Silakan login sekarang.');
                window.location.href = 'login.php';
            </script>";
        } else {
            // Jika masih error, ini akan memunculkan pesan error aslinya dari database
            $error_db = mysqli_error($koneksi);
            echo "<script>alert('Gagal daftar: $error_db');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Membership | Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root { --primary: #3E2723; --accent: #8D6E63; --bg: #FDFBF9; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body { 
            background: var(--bg); 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 40px 20px;
            overflow-x: hidden;
            position: relative;
        }

        /* Background Decoration */
        .blob { position: fixed; border-radius: 50%; filter: blur(80px); opacity: 0.4; z-index: -1; }
        .blob-1 { width: 400px; height: 400px; background: #FFCCBC; top: -100px; right: -100px; }
        .blob-2 { width: 300px; height: 300px; background: #D1C4E9; bottom: -50px; left: -50px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reg-wrapper {
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 45px;
            box-shadow: 0 25px 50px rgba(62, 39, 35, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: fadeInUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .reg-header { text-align: center; margin-bottom: 35px; }
        .reg-header h2 { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--primary); margin-bottom: 8px; }
        .reg-header p { font-size: 13px; color: var(--accent); letter-spacing: 1px; }

        .form-group { position: relative; margin-bottom: 20px; }
        .form-group i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--accent); font-size: 20px; }
        
        .input-field {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border-radius: 15px;
            border: 1px solid #EEE;
            background: #FFF;
            font-size: 13px;
            transition: 0.3s;
            outline: none;
            color: var(--primary);
        }

        .input-field:focus {
            border-color: var(--accent);
            box-shadow: 0 5px 15px rgba(141, 110, 99, 0.1);
        }

        select.input-field { appearance: none; cursor: pointer; }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3E2723, #5D4037);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 15px;
            box-shadow: 0 10px 20px rgba(62, 39, 35, 0.2);
        }

        .btn-register:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(62, 39, 35, 0.3); }

        .footer-link { text-align: center; margin-top: 25px; font-size: 13px; color: #777; }
        .footer-link a { color: var(--primary); font-weight: 700; text-decoration: none; }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="reg-wrapper">
        <div class="reg-header">
            <div style="width: 60px; height: 60px; background: #EFEBE9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: var(--primary); font-size: 30px;">
                <i class='bx bxs-user-plus'></i>
            </div>
            <h2>Daftar Anggota</h2>
            <p>Mulai Petualangan Literasimu</p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <i class='bx bx-id-card'></i>
                <input type="text" name="nama" class="input-field" placeholder="Nama Lengkap" required autocomplete="off">
            </div>

            <div class="form-group">
                <i class='bx bx-door-open'></i>
                <select name="kelas" class="input-field" required>
                    <option value="" disabled selected>Pilih Kelas</option>
                    <option value="X RPL 1">X RPL 1</option>
                    <option value="X RPL 2">X RPL 2</option>
                    <option value="XI RPL 1">XI RPL 1</option>
                    <option value="XI RPL 2">XI RPL 2</option>
                    <option value="XII RPL 1">XII RPL 1</option>
                    <option value="XII RPL 2">XII RPL 2</option>
                </select>
            </div>

            <div class="form-group">
                <i class='bx bx-envelope'></i>
                <input type="email" name="email" class="input-field" placeholder="Email Aktif" required autocomplete="off">
            </div>

            <div class="form-group">
                <i class='bx bx-phone'></i>
                <input type="number" name="no_tlp" class="input-field" placeholder="Nomor WhatsApp" required autocomplete="off">
            </div>

            <div class="form-group">
                <i class='bx bx-user'></i>
                <input type="text" name="username" class="input-field" placeholder="Username Baru" required autocomplete="off">
            </div>

            <div class="form-group">
                <i class='bx bx-lock-alt'></i>
                <input type="password" name="password" class="input-field" placeholder="Password" required>
            </div>

            <button type="submit" name="btn_register" class="btn-register">DAFTAR SEKARANG</button>
        </form>

        <div class="footer-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>

</body>
</html>