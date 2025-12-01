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
        (SELECT COUNT(*) FROM trivia) as total_trivia,
        (SELECT COUNT(*) FROM reward) as total_reward
");
$stat = mysqli_fetch_assoc($stats);

// Get recent pemesanan
$recent_pemesanan = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, f.judul_film 
    FROM pemesanan p 
    JOIN pengguna u ON p.id_pengguna = u.id_pengguna 
    JOIN jadwal_tayang j ON p.id_jadwal = j.id_jadwal 
    JOIN film f ON j.id_film = f.id_film 
    ORDER BY p.tanggal_pemesanan DESC 
    LIMIT 5
");

// Get film popularity
$film_popular = mysqli_query($conn, "
    SELECT f.judul_film, COUNT(p.id_pemesanan) as jumlah_pemesanan
    FROM film f 
    JOIN jadwal_tayang j ON f.id_film = j.id_film 
    JOIN pemesanan p ON j.id_jadwal = p.id_jadwal 
    WHERE p.status_pemesanan = 'confirmed'
    GROUP BY f.id_film 
    ORDER BY jumlah_pemesanan DESC 
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1>Analytics & Reports</h1>
                        <p>Comprehensive system analytics and reports</p>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary">
                            <i class="bi bi-download"></i> Export PDF
                        </button>
                        <button class="btn btn-outline-success">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_film'] ?></div>
                        <div class="stat-label">Total Movies</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_bioskop'] ?></div>
                        <div class="stat-label">Cinemas</div>
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

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">Rp <?= number_format($stat['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_poin'] ?? 0 ?></div>
                        <div class="stat-label">Total Points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_trivia'] ?? 0 ?></div>
                        <div class="stat-label">Total Trivia</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_reward'] ?? 0 ?></div>
                        <div class="stat-label">Total Rewards</div>
                    </div>
                </div>

                <!-- Content Sections -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-ticket-perforated"></i>
                                Recent Bookings
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Booking Code</th>
                                                <th>Customer</th>
                                                <th>Movie</th>
                                                <th>Total Payment</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($pemesanan = mysqli_fetch_assoc($recent_pemesanan)): ?>
                                            <tr>
                                                <td><strong><?= $pemesanan['kode_booking'] ?></strong></td>
                                                <td><?= $pemesanan['nama_lengkap'] ?></td>
                                                <td><?= $pemesanan['judul_film'] ?></td>
                                                <td>Rp <?= number_format($pemesanan['total_bayar'], 0, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $pemesanan['status_pemesanan'] == 'confirmed' ? 'success' : 'warning' ?>">
                                                        <?= $pemesanan['status_pemesanan'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d M Y H:i', strtotime($pemesanan['tanggal_pemesanan'])) ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-trophy"></i>
                                Popular Movies
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column gap-3">
                                    <?php 
                                    $rank = 1;
                                    while ($film = mysqli_fetch_assoc($film_popular)): 
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary me-2">#<?= $rank++ ?></span>
                                            <div>
                                                <div class="fw-medium"><?= $film['judul_film'] ?></div>
                                                <small class="text-muted"><?= $film['jumlah_pemesanan'] ?> bookings</small>
                                            </div>
                                        </div>
                                        <span class="badge badge-success"><?= $film['jumlah_pemesanan'] ?></span>
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="card mt-4">
                    <div class="card-header">
                        <i class="bi bi-graph-up"></i>
                        Monthly Summary
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Bookings</th>
                                        <th>Revenue</th>
                                        <th>Movies Showing</th>
                                        <th>Trivia Answered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $monthly = mysqli_query($conn, "
                                        SELECT 
                                            DATE_FORMAT(p.tanggal_pemesanan, '%M %Y') as bulan,
                                            COUNT(p.id_pemesanan) as total_pemesanan,
                                            SUM(p.total_bayar) as pendapatan,
                                            COUNT(DISTINCT j.id_film) as film_tayang,
                                            (SELECT COUNT(*) FROM riwayat_trivia rt 
                                             WHERE DATE_FORMAT(rt.waktu_dijawab, '%Y-%m') = DATE_FORMAT(p.tanggal_pemesanan, '%Y-%m')) as trivia_dijawab
                                        FROM pemesanan p 
                                        JOIN jadwal_tayang j ON p.id_jadwal = j.id_jadwal 
                                        WHERE p.status_pemesanan = 'confirmed'
                                        GROUP BY DATE_FORMAT(p.tanggal_pemesanan, '%Y-%m')
                                        ORDER BY p.tanggal_pemesanan DESC 
                                        LIMIT 6
                                    ");
                                    
                                    while ($month = mysqli_fetch_assoc($monthly)):
                                    ?>
                                    <tr>
                                        <td><strong><?= $month['bulan'] ?></strong></td>
                                        <td><?= $month['total_pemesanan'] ?> bookings</td>
                                        <td>Rp <?= number_format($month['pendapatan'], 0, ',', '.') ?></td>
                                        <td><?= $month['film_tayang'] ?> movies</td>
                                        <td><?= $month['trivia_dijawab'] ?> trivia</td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
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