<nav class="sidebar">
    <div class="brand">
        <h1>🎬 CinemaPro</h1>
        <p>Management System</p>
    </div>
    
    <div class="nav-container">
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                <i class="bi bi-speedometer2 nav-icon"></i>
                Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'film.php' ? 'active' : '' ?>" href="film.php">
                <i class="bi bi-film nav-icon"></i>
                Movies
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bioskop.php' ? 'active' : '' ?>" href="bioskop.php">
                <i class="bi bi-building nav-icon"></i>
                Cinemas
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'jadwal.php' ? 'active' : '' ?>" href="jadwal.php">
                <i class="bi bi-calendar-event nav-icon"></i>
                Schedule
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pemesanan.php' ? 'active' : '' ?>" href="pemesanan.php">
                <i class="bi bi-ticket-perforated nav-icon"></i>
                Bookings
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'trivia.php' ? 'active' : '' ?>" href="trivia.php">
                <i class="bi bi-question-circle nav-icon"></i>
                Trivia
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'reward.php' ? 'active' : '' ?>" href="reward.php">
                <i class="bi bi-gift nav-icon"></i>
                Rewards
            </a>
        </div>
        <div class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : '' ?>" href="laporan.php">
                <i class="bi bi-bar-chart nav-icon"></i>
                Analytics
            </a>
        </div>
    </div>

    <div style="padding: 25px; text-align: center;">
        <small style="color: #94a3b8;">Kelompok 11</small>
    </div>
</nav>