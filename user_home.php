<?php
require 'koneksi.php';
// Filter film yang sedang tayang
$films = mysqli_query($conn, "
    SELECT DISTINCT f.* FROM film f
    JOIN jadwal_tayang j ON f.id_film = j.id_film
    WHERE j.tanggal_tayang >= CURDATE() AND j.status_jadwal = 'active'
    ORDER BY j.tanggal_tayang ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Movies • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'user_header.php'; ?>
    
    <div class="container py-4">
        <div class="page-header">
            <div class="header-title">
                <h1>Now Showing</h1>
                <p>Watch the latest movies at our cinemas</p>
            </div>
        </div>

        <div class="row">
            <?php while($f = mysqli_fetch_assoc($films)): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; border-bottom: 1px solid var(--border);">
                        <i class="bi bi-film text-secondary" style="font-size: 3rem;"></i>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-bold mb-2"><?= $f['judul_film'] ?></h5>
                        <div class="mb-3">
                            <span class="badge bg-secondary fw-normal"><?= $f['genre'] ?></span>
                            <span class="badge bg-secondary fw-normal"><?= $f['durasi_menit'] ?>m</span>
                            <span class="badge bg-warning text-dark fw-normal"><?= $f['rating_usia'] ?></span>
                        </div>
                        <p class="text-muted small flex-grow-1">
                            <?= substr($f['sinopsis'], 0, 80) ?>...
                        </p>
                        <div class="d-grid gap-2 mt-3">
                            <a href="user_booking.php?id_film=<?= $f['id_film'] ?>" class="btn btn-primary">
                                Book Ticket
                            </a>
                            <a href="user_trivia.php?id_film=<?= $f['id_film'] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-joystick"></i> Play Trivia
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>