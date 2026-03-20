<div class="card fade-in-up" style="max-width: 500px; margin: 0 auto;">
    <div class="header-profile">
        <h1>Ubah Password 🔒</h1>
        <p>Jaga keamanan akunmu secara berkala.</p>
    </div>

    <form action="" method="post"> 
        
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="pass_lama" id="pass_lama" placeholder="Masukkan password lama..." required>
        </div>
        
        <hr style="border: 0; border-top: 1px dashed #ccc; margin: 20px 0;">
        
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="pass_baru" id="pass_baru" placeholder="Password baru..." required>
        </div>
        
        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi_pass" id="konfirmasi_pass" placeholder="Ulangi password baru..." required>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <input type="checkbox" onclick="lihatPassword()" style="width: 18px; height: 18px; cursor: pointer; accent-color: #3E2723;">
            <label style="margin: 0; font-size: 13px; font-weight: 600; color: #8D6E63; cursor: pointer;" onclick="lihatPassword()">Tampilkan Password</label>
        </div>

        <button type="submit" name="simpan_password" class="btn-simpan">Simpan Password Baru</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="?page=profile" style="color: #8D6E63; text-decoration: none; font-size: 13px;">Batal & Kembali</a>
        </div>
    </form>
</div>

<script>
function lihatPassword() {
    var lama = document.getElementById("pass_lama");
    var baru = document.getElementById("pass_baru");
    var konfirmasi = document.getElementById("konfirmasi_pass");
    
    if (lama.type === "password") {
        lama.type = "text"; baru.type = "text"; konfirmasi.type = "text";
    } else {
        lama.type = "password"; baru.type = "password"; konfirmasi.type = "password";
    }
}
</script>

<?php
// PROSES SIMPAN PASSWORD (FIXED)
if (isset($_POST['simpan_password'])) {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi_pass'];
    
    // AMBIL USERNAME LANGSUNG DARI SESSION (LEBIH AMAN & PASTI ADA)
    $username_sekarang = $_SESSION['username'];
    
    // Cek data user berdasarkan username
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username_sekarang'");
    $data = mysqli_fetch_array($cek);
    
    if ($data) {
        // 1. Cek apakah Password Lama benar?
        // (Pastikan di database passwordnya tidak terenkripsi md5, kalau terenkripsi pakai: md5($pass_lama))
        if ($pass_lama == $data['password']) {
            
            // 2. Cek apakah Password Baru & Konfirmasi sama?
            if ($pass_baru == $konfirmasi) {
                
                // 3. Update Password
                $update = mysqli_query($koneksi, "UPDATE users SET password='$pass_baru' WHERE username='$username_sekarang'");
                
                if ($update) {
                    echo "<script>
                        alert('SUKSES! Password berhasil diubah. Silakan login ulang.');
                        window.location='logout.php';
                    </script>";
                } else {
                    echo "<script>alert('ERROR: Gagal update ke database!');</script>";
                }
                
            } else {
                echo "<script>alert('GAGAL: Password Baru dan Konfirmasi Tidak Cocok!');</script>";
            }
            
        } else {
            // INI YANG KEMARIN GAK MUNCUL
            echo "<script>alert('GAGAL: Password Lama Anda SALAH!');</script>";
        }
        
    } else {
        echo "<script>alert('ERROR: Data user tidak ditemukan!');</script>";
    }
}
?>
