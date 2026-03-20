<?php
session_start();
include 'inc/koneksi.php';

if (isset($_POST['btn_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. CEK KE TABEL USERS (Bisa Admin, Bisa Anggota dari Register)
    $cek_users = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if ($cek_users && mysqli_num_rows($cek_users) > 0) {
        $data = mysqli_fetch_assoc($cek_users);
        
        $_SESSION['ses_id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['status'] = "login";
        $_SESSION['akses'] = $data['akses']; 

        // --- LOGIKA PEMISAH (BIAR GAK NYASAR) ---
        if($data['akses'] == 'admin'){
            header("location:dashboard.php");
        } else {
            header("location:katalog.php"); // Anggota masuk sini
        }
        exit;
    } 
    
    // 2. CEK KE TABEL ANGGOTA LAMA (Backup data lama)
    $cek_member = mysqli_query($koneksi, "SELECT * FROM tbl_anggota WHERE (id_anggota='$username' OR nama_anggota='$username') AND password='$password'");
    
    if ($cek_member && mysqli_num_rows($cek_member) > 0) {
        $data = mysqli_fetch_assoc($cek_member);
        $_SESSION['ses_id'] = $data['id_anggota'];
        $_SESSION['nama'] = $data['nama_anggota'];
        $_SESSION['username'] = $data['nama_anggota'];
        $_SESSION['status'] = "login";
        $_SESSION['akses'] = "anggota";
        
        header("location:katalog.php"); 
        exit;
    }

    $error_msg = "Username atau Password Salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        /* BACKGROUND AURA */
        body {
            display: flex; justify-content: center; align-items: center; min-height: 100vh;
            background: linear-gradient(135deg, #EFEBE9 0%, #D7CCC8 100%);
            overflow: hidden;
        }

        /* ANIMASI MASUK */
        @keyframes softPopUp { 
            0% { opacity: 0; transform: scale(0.95) translateY(20px); } 
            100% { opacity: 1; transform: scale(1) translateY(0); } 
        }

        /* KARTU LOGIN */
        .login-wrapper {
            width: 100%; max-width: 420px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(62, 39, 35, 0.15);
            overflow: hidden;
            animation: softPopUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
            position: relative;
            margin: 20px;
            padding-bottom: 30px; /* Ruang untuk watermark */
        }

        /* HEADER (TEKS KEMBALI SEPERTI SEMULA) */
        .login-header {
            background: linear-gradient(135deg, #5D4037, #8D6E63);
            padding: 40px 20px 50px;
            text-align: center;
            color: white;
            border-radius: 0 0 50% 50% / 0 0 40px 40px;
        }
        .login-header h2 { font-family: 'Playfair Display', serif; font-size: 26px; margin-bottom: 5px; }
        .login-header p { font-size: 12px; opacity: 0.9; letter-spacing: 1px; text-transform: uppercase; }

        /* ICON USER */
        .login-avatar-area {
            margin-top: -45px; display: flex; justify-content: center; position: relative; z-index: 2;
        }
        .login-avatar-icon {
            width: 90px; height: 90px;
            background: #fff; color: #5D4037;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 4px solid #fff;
        }

        /* FORM */
        .login-form { padding: 30px 40px 10px; }
        .login-input-group { position: relative; margin-bottom: 20px; text-align: left; }
        
        .login-input-field {
            width: 100%; padding: 14px 20px 14px 50px; 
            border-radius: 30px; border: 1px solid #E0E0E0; background: #FAFAFA;
            color: #3E2723; font-size: 13px; outline: none; transition: 0.3s;
        }
        .login-input-field:focus { border-color: #8D6E63; background: #fff; box-shadow: 0 5px 15px rgba(141, 110, 99, 0.1); }
        
        .login-input-icon {
            position: absolute; top: 50%; left: 18px; transform: translateY(-50%);
            color: #A1887F; font-size: 20px; transition: 0.3s;
        }
        .login-input-field:focus + .login-input-icon { color: #5D4037; }
        
        .btn-login {
            width: 100%; padding: 14px; background: #3E2723; color: white;
            border: none; border-radius: 30px;
            font-size: 13px; font-weight: 700; letter-spacing: 1px; cursor: pointer;
            transition: 0.3s; margin-top: 10px;
            box-shadow: 0 10px 20px rgba(62, 39, 35, 0.15);
        }
        .btn-login:hover { background: #5D4037; transform: translateY(-2px); }
        
        .footer-text { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .footer-text a { color: #3E2723; font-weight: 700; text-decoration: none; }
        
        .katalog-link { display: block; text-align: center; margin-top: 10px; font-size: 12px; color: #888; text-decoration: none; }
        .katalog-link:hover { text-decoration: underline; color: #3E2723; }
        
        /* COPYRIGHT WATERMARK */
        .watermark { 
            text-align: center; margin-top: 25px; font-size: 10px; color: #aaa; letter-spacing: 1px; text-transform: uppercase; 
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-header">
            <h2>Perpustakaan</h2>
            <p>SYSTEM LOGIN AREA</p>
        </div>

        <div class="login-avatar-area">
            <div class="login-avatar-icon"><i class='bx bxs-user'></i></div>
        </div>

        <div class="login-form">
            <h3 style="text-align: center; color: #3E2723; margin-bottom: 25px; font-family: 'Playfair Display', serif; font-size: 22px;">Welcome Back</h3>
            
            <form action="" method="POST">
                <div class="login-input-group">
                    <input type="text" name="username" class="login-input-field" placeholder="Username / ID Anggota" required autocomplete="off">
                    <i class='bx bx-user login-input-icon'></i>
    </div>

                <div class="login-input-group">
                    <input type="password" name="password" class="login-input-field" placeholder="Password" required>
                    <i class='bx bx-lock-alt login-input-icon'></i>
                </div>

                <button type="submit" name="btn_login" class="btn-login">MASUK SEKARANG</button>
            </form>

            <div class="footer-text">
                Belum punya akun? <a href="register.php">Daftar disini</a>
            </div>
            <a href="katalog.php" class="katalog-link">&larr; Lihat Katalog Tanpa Login</a>
            
            <div class="watermark">&copy; 2026 E-Perpus System</div>
        </div>
    </div>

    <?php if (isset($error_msg)) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Masuk',
            text: '<?php echo $error_msg; ?>',
            confirmButtonColor: '#3E2723',
            background: '#fff',
            color: '#3E2723'
        });
    </script>
    <?php endif; ?>

</body>
</html>