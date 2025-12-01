<?php
require 'koneksi.php';

// Get all pemesanan dengan detail
$pemesanans = mysqli_query($conn, "
    SELECT p.*, u.nama_lengkap, f.judul_film, j.tanggal_tayang, j.jam_mulai, 
           b.nama_bioskop, s.nama_studio, r.nama_reward
    FROM pemesanan p
    LEFT JOIN pengguna u ON p.id_pengguna = u.id_pengguna
    LEFT JOIN jadwal_tayang j ON p.id_jadwal = j.id_jadwal
    LEFT JOIN film f ON j.id_film = f.id_film
    LEFT JOIN studio s ON j.id_studio = s.id_studio
    LEFT JOIN bioskop b ON s.id_bioskop = b.id_bioskop
    LEFT JOIN reward r ON p.id_reward = r.id_reward
    ORDER BY p.tanggal_pemesanan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Bookings</title>
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
                        <h1>Booking Management</h1>
                        <p>View and manage all ticket bookings</p>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <!-- Stats -->
                <div class="stats-grid">
                    <?php
                    $stats = mysqli_query($conn, "
                        SELECT 
                            COUNT(*) as total_pemesanan,
                            SUM(CASE WHEN status_pemesanan = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                            SUM(CASE WHEN status_pemesanan = 'pending' THEN 1 ELSE 0 END) as pending,
                            SUM(total_bayar) as total_pendapatan
                        FROM pemesanan 
                        WHERE status_pemesanan != 'cancelled'
                    ");
                    $stat = mysqli_fetch_assoc($stats);
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_pemesanan'] ?></div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['confirmed'] ?></div>
                        <div class="stat-label">Confirmed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['pending'] ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">Rp <?= number_format($stat['total_pendapatan'], 0, ',', '.') ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-ticket-perforated"></i>
                        Booking List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Booking Code</th>
                                        <th>Customer</th>
                                        <th>Movie & Schedule</th>
                                        <th>Cinema</th>
                                        <th>Total Payment</th>
                                        <th>Status</th>
                                        <th>Reward Used</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($pemesanan = mysqli_fetch_assoc($pemesanans)): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $pemesanan['kode_booking'] ?></strong>
                                            <br><small class="text-muted"><?= $pemesanan['jumlah_tiket'] ?> tickets</small>
                                        </td>
                                        <td><?= $pemesanan['nama_lengkap'] ?></td>
                                        <td>
                                            <strong><?= $pemesanan['judul_film'] ?></strong>
                                            <br><small class="text-muted">
                                                <?= date('d M Y', strtotime($pemesanan['tanggal_tayang'])) ?> • 
                                                <?= substr($pemesanan['jam_mulai'], 0, 5) ?>
                                            </small>
                                        </td>
                                        <td><?= $pemesanan['nama_bioskop'] ?></td>
                                        <td>Rp <?= number_format($pemesanan['total_bayar'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $pemesanan['status_pemesanan'] == 'confirmed' ? 'success' : 
                                                ($pemesanan['status_pemesanan'] == 'pending' ? 'warning' : 'danger')
                                            ?>">
                                                <?= $pemesanan['status_pemesanan'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pemesanan['nama_reward']): ?>
                                                <span class="badge badge-info"><?= $pemesanan['nama_reward'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
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
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>