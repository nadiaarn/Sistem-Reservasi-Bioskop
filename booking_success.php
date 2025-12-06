<?php
require 'koneksi.php';
include 'user_header.php';

if (!isset($_GET['id'])) { header("Location: user_home.php"); exit(); }
$id_pemesanan = $_GET['id'];

$trx = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, f.judul_film, f.durasi_menit, b.nama_bioskop, b.kota, s.nama_studio, j.tanggal_tayang, j.jam_mulai
    FROM pemesanan p
    JOIN jadwal_tayang j ON p.id_jadwal = j.id_jadwal
    JOIN film f ON j.id_film = f.id_film
    JOIN studio s ON j.id_studio = s.id_studio
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop
    WHERE p.id_pemesanan = $id_pemesanan
"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Booking Success • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container py-5 text-center">
        <div class="card shadow-lg mx-auto border-0" style="max-width: 500px;">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h2 class="fw-bold mb-1">Booking Confirmed!</h2>
                <p class="text-muted">Thank you for your order.</p>
                <hr class="my-4">
                
                <div class="text-start">
                    <h5 class="fw-bold text-primary"><?= $trx['judul_film'] ?></h5>
                    <p class="mb-1 fw-bold"><?= $trx['nama_bioskop'] ?> - <?= $trx['nama_studio'] ?></p>
                    <p class="text-muted small mb-3"><?= $trx['kota'] ?></p>
                    
                    <div class="row mb-2">
                        <div class="col-6 text-muted small">Date</div>
                        <div class="col-6 text-end fw-medium"><?= date('d F Y', strtotime($trx['tanggal_tayang'])) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted small">Time</div>
                        <div class="col-6 text-end fw-medium"><?= substr($trx['jam_mulai'], 0, 5) ?> WIB</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 text-muted small">Tickets</div>
                        <div class="col-6 text-end fw-medium"><?= $trx['jumlah_tiket'] ?> Seats</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 text-muted small">Booking Code</div>
                        <div class="col-6 text-end fw-bold font-monospace"><?= $trx['kode_booking'] ?></div>
                    </div>
                    <div class="bg-light p-3 rounded d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Paid</span>
                        <span class="fw-bold text-primary fs-5">Rp <?= number_format($trx['total_bayar']) ?></span>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <a href="user_history.php" class="btn btn-dark">View My History</a>
                    <a href="user_home.php" class="btn btn-outline-secondary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>