<?php
require 'koneksi.php';

// Get statistics
$stats = mysqli_query($conn, "
    SELECT 
        (SELECT COUNT(*) FROM film) as total_film,
        (SELECT COUNT(*) FROM bioskop) as total_bioskop,
        (SELECT COUNT(*) FROM pemesanan WHERE DATE(tanggal_pemesanan) = CURDATE()) as pemesanan_hari_ini,
        (SELECT COUNT(*) FROM pengguna) as total_pengguna,
        (SELECT SUM(total_bayar) FROM pemesanan WHERE status_pemesanan = 'confirmed') as total_pendapatan,
        (SELECT SUM(total_poin) FROM pengguna) as total_poin,
        (SELECT COUNT(*) FROM jadwal_tayang WHERE tanggal_tayang >= CURDATE()) as jadwal_aktif
");
$stat = mysqli_fetch_assoc($stats);

// Get films with upcoming shows
$films = mysqli_query($conn, "
    SELECT f.*, 
           (SELECT COUNT(*) FROM jadwal_tayang j WHERE j.id_film = f.id_film AND j.tanggal_tayang >= CURDATE()) as jadwal_aktif 
    FROM film f 
    ORDER BY f.created_at DESC 
    LIMIT 6
");

// Get recent activities
$activities = mysqli_query($conn, "
    SELECT 'booking' as type, kode_booking as info, tanggal_pemesanan as waktu 
    FROM pemesanan 
    ORDER BY tanggal_pemesanan DESC 
    LIMIT 6
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="brand">
                <h1>🎬 CinemaPro</h1>
                <p>Management System</p>
            </div>
            
            <div class="nav-container">
                <div class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        Dashboard
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="film.php">
                        <i class="bi bi-film nav-icon"></i>
                        Movies
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="bioskop.php">
                        <i class="bi bi-building nav-icon"></i>
                        Cinemas
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="jadwal.php">
                        <i class="bi bi-calendar-event nav-icon"></i>
                        Schedule
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="pemesanan.php">
                        <i class="bi bi-ticket-perforated nav-icon"></i>
                        Bookings
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="trivia.php">
                        <i class="bi bi-question-circle nav-icon"></i>
                        Trivia
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="reward.php">
                        <i class="bi bi-gift nav-icon"></i>
                        Rewards
                    </a>
                </div>
                <div class="nav-item">
                    <a class="nav-link" href="laporan.php">
                        <i class="bi bi-bar-chart nav-icon"></i>
                        Analytics
                    </a>
                </div>
            </div>

            <div style="padding: 25px; text-align: center;">
                <small style="color: #94a3b8;">Kelompok 11</small>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1>Dashboard Overview</h1>
                        <p>Welcome to CinemaPro Management System</p>
                    </div>
                    <a href="pemesanan.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                        New Booking
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_film'] ?></div>
                        <div class="stat-label">Total Movies</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_bioskop'] ?></div>
                        <div class="stat-label">Cinema Locations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['pemesanan_hari_ini'] ?></div>
                        <div class="stat-label">Today's Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_pengguna'] ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>

                <!-- Additional Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">Rp <?= number_format($stat['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_poin'] ?? 0 ?></div>
                        <div class="stat-label">Loyalty Points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['jadwal_aktif'] ?? 0 ?></div>
                        <div class="stat-label">Active Shows</div>
                    </div>
                </div>

                <!-- Content Sections -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-film"></i>
                                Movies & Upcoming Shows
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($films) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Movie Title</th>
                                                <th>Genre</th>
                                                <th>Duration</th>
                                                <th>Upcoming Shows</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($film = mysqli_fetch_assoc($films)): ?>
                                            <tr>
                                                <td><strong><?= $film['judul_film'] ?></strong></td>
                                                <td><?= $film['genre'] ?></td>
                                                <td><?= $film['durasi_menit'] ?>m</td>
                                                <td>
                                                    <?php if ($film['jadwal_aktif'] > 0): ?>
                                                        <span class="badge badge-success"><?= $film['jadwal_aktif'] ?> shows</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">No schedule</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-film" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No movies found in database</p>
                                    <a href="film.php" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Add Movies First
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-activity"></i>
                                Recent Activity
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($activities) > 0): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php while ($activity = mysqli_fetch_assoc($activities)): ?>
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></div>
                                            <div>
                                                <div class="fw-medium">New Booking</div>
                                                <div class="text-muted small"><?= $activity['info'] ?></div>
                                            </div>
                                        </div>
                                        <div class="text-muted small"><?= date('H:i', strtotime($activity['waktu'])) ?></div>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-clock-history" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No recent activity</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>