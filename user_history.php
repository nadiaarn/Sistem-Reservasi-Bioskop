<?php
require 'koneksi.php';
include 'user_header.php';

$uid = $_SESSION['id_pengguna'];
$history = mysqli_query($conn, "
    SELECT p.*, f.judul_film, b.nama_bioskop, j.tanggal_tayang, j.jam_mulai 
    FROM pemesanan p
    JOIN jadwal_tayang j ON p.id_jadwal = j.id_jadwal
    JOIN film f ON j.id_film = f.id_film
    JOIN studio s ON j.id_studio = s.id_studio
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop
    WHERE p.id_pengguna = $uid
    ORDER BY p.tanggal_pemesanan DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>My Booking History • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container py-4">
        <div class="page-header">
            <div class="header-title">
                <h1>My Bookings</h1>
                <p>History of your movie tickets</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if(mysqli_num_rows($history) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Booking Code</th>
                                <th>Movie</th>
                                <th>Cinema</th>
                                <th>Showtime</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($history)): ?>
                            <tr>
                                <td class="font-monospace fw-bold text-primary">#<?= $row['kode_booking'] ?></td>
                                <td class="fw-bold"><?= $row['judul_film'] ?></td>
                                <td><?= $row['nama_bioskop'] ?></td>
                                <td>
                                    <?= date('d M Y', strtotime($row['tanggal_tayang'])) ?><br>
                                    <small class="text-muted"><?= substr($row['jam_mulai'],0,5) ?></small>
                                </td>
                                <td>Rp <?= number_format($row['total_bayar']) ?></td>
                                <td><span class="badge bg-success">Confirmed</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-ticket-perforated display-1"></i>
                        <p class="mt-3">You haven't booked any tickets yet.</p>
                        <a href="user_home.php" class="btn btn-primary">Book Now</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>