<?php
require 'koneksi.php';

// Handle Create Jadwal
if (isset($_POST['tambah_jadwal'])) {
    $id_film = $_POST['id_film'];
    $id_studio = $_POST['id_studio'];
    $tanggal = $_POST['tanggal_tayang'];
    $jam_mulai = $_POST['jam_mulai'];
    $harga_reguler = $_POST['harga_reguler'];
    $harga_vip = $_POST['harga_vip'];
    $harga_premium = $_POST['harga_premium'];
    
    // Get kapasitas studio
    $studio_query = mysqli_query($conn, "SELECT kapasitas_total FROM studio WHERE id_studio = $id_studio");
    $studio = mysqli_fetch_assoc($studio_query);
    $kapasitas = $studio['kapasitas_total'];
    
    $query = "INSERT INTO jadwal_tayang (id_film, id_studio, created_by_admin, tanggal_tayang, jam_mulai, harga_reguler, harga_vip, harga_premium, kursi_tersedia) 
              VALUES ($id_film, $id_studio, 1, '$tanggal', '$jam_mulai', $harga_reguler, $harga_vip, $harga_premium, $kapasitas)";
    
    if (mysqli_query($conn, $query)) {
        $success = "Jadwal tayang berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get data
$jadwals = mysqli_query($conn, "
    SELECT j.*, f.judul_film, f.durasi_menit, s.nama_studio, b.nama_bioskop 
    FROM jadwal_tayang j 
    JOIN film f ON j.id_film = f.id_film 
    JOIN studio s ON j.id_studio = s.id_studio 
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop 
    ORDER BY j.tanggal_tayang DESC, j.jam_mulai
");

$films = mysqli_query($conn, "SELECT id_film, judul_film FROM film ORDER BY judul_film");
$studios = mysqli_query($conn, "
    SELECT s.id_studio, s.nama_studio, b.nama_bioskop 
    FROM studio s 
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop 
    WHERE s.status_studio = 'active'
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Schedule</title>
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
                        <h1>Movie Schedule</h1>
                        <p>Manage movie showtimes and schedules</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
                        <i class="bi bi-plus-circle"></i> Add Schedule
                    </button>
                </div>
            </div>

            <div class="content-area">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-calendar-event"></i>
                        Show Schedule
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Movie</th>
                                        <th>Studio & Cinema</th>
                                        <th>Date & Time</th>
                                        <th>Price</th>
                                        <th>Seats Available</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($jadwal = mysqli_fetch_assoc($jadwals)): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $jadwal['judul_film'] ?></strong>
                                            <br><small class="text-muted"><?= $jadwal['durasi_menit'] ?> minutes</small>
                                        </td>
                                        <td>
                                            <?= $jadwal['nama_studio'] ?>
                                            <br><small class="text-muted"><?= $jadwal['nama_bioskop'] ?></small>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($jadwal['tanggal_tayang'])) ?>
                                            <br><small class="text-muted"><?= substr($jadwal['jam_mulai'], 0, 5) ?></small>
                                        </td>
                                        <td>
                                            <small>
                                                Regular: Rp <?= number_format($jadwal['harga_reguler'], 0, ',', '.') ?><br>
                                                VIP: Rp <?= number_format($jadwal['harga_vip'], 0, ',', '.') ?><br>
                                                Premium: Rp <?= number_format($jadwal['harga_premium'], 0, ',', '.') ?>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $jadwal['kursi_tersedia'] > 20 ? 'success' : 'warning' ?>">
                                                <?= $jadwal['kursi_tersedia'] ?> seats
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $jadwal['status_jadwal'] == 'active' ? 'success' : 'danger' ?>">
                                                <?= $jadwal['status_jadwal'] ?>
                                            </span>
                                        </td>
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

    <!-- Modal Tambah Jadwal -->
    <div class="modal fade" id="tambahJadwalModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Show Schedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Movie</label>
                                    <select class="form-control" name="id_film" required>
                                        <option value="">-- Select Movie --</option>
                                        <?php while ($film = mysqli_fetch_assoc($films)): ?>
                                            <option value="<?= $film['id_film'] ?>"><?= $film['judul_film'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Studio</label>
                                    <select class="form-control" name="id_studio" required>
                                        <option value="">-- Select Studio --</option>
                                        <?php 
                                        $studios_data = mysqli_query($conn, "
                                            SELECT s.id_studio, s.nama_studio, b.nama_bioskop 
                                            FROM studio s 
                                            JOIN bioskop b ON s.id_bioskop = b.id_bioskop 
                                            WHERE s.status_studio = 'active'
                                        ");
                                        while ($studio = mysqli_fetch_assoc($studios_data)): 
                                        ?>
                                            <option value="<?= $studio['id_studio'] ?>">
                                                <?= $studio['nama_studio'] ?> - <?= $studio['nama_bioskop'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Show Date</label>
                                    <input type="date" class="form-control" name="tanggal_tayang" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Start Time</label>
                                    <input type="time" class="form-control" name="jam_mulai" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Regular Price</label>
                                    <input type="number" class="form-control" name="harga_reguler" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">VIP Price</label>
                                    <input type="number" class="form-control" name="harga_vip" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Premium Price</label>
                                    <input type="number" class="form-control" name="harga_premium" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="tambah_jadwal" class="btn btn-primary">Save Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>