<div class="card profile-card fade-in-up">
    
    <div class="profile-header-bg">
        <h2>Profil Pengguna</h2>
        <p>ADMINISTRATOR SYSTEM</p>
    </div>

    <div class="profile-avatar-area">
        <div class="avatar-ring">
            <div class="avatar-img"><?php echo $inisial; ?></div>
        </div>
        
        <h1 class="profile-name"><?php echo $tampil['username']; ?></h1> <span class="profile-role">Admin</span>
    </div>

    <div class="profile-grid">
        
        <div class="info-box">
            <span class="info-label">Username</span>
            <span class="info-value">@<?php echo $tampil['username']; ?></span>
        </div>

        <div class="info-box">
            <span class="info-label">No. Telepon</span>
            <span class="info-value"><?php echo $tampil['no_tlp']; ?></span>
        </div>

        <div class="info-box full-width">
            <span class="info-label">Alamat Email</span>
            <span class="info-value"><?php echo $tampil['email']; ?></span>
        </div>

        <div class="info-box full-width">
            <span class="info-label">Login Terakhir</span>
            <span class="info-value">
                📅 <?php echo date('d F Y'); ?> &nbsp; • &nbsp; ⏰ <?php echo date('H:i'); ?> WIB
            </span>
        </div>

    </div>

    <div class="profile-actions">
        <a href="?page=ubah_password" class="btn-profile">🔒 Ganti Password</a>
    </div>

</div>
