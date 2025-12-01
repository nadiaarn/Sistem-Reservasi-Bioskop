<?php
require 'koneksi.php';

// Handle Create Bioskop
if (isset($_POST['tambah_bioskop'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_bioskop']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat_lengkap']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $jam_buka = $_POST['jam_buka'];
    $jam_tutup = $_POST['jam_tutup'];
    
    $query = "INSERT INTO bioskop (created_by_admin, nama_bioskop, alamat_lengkap, kota, no_telepon, jam_buka, jam_tutup) 
              VALUES (1, '$nama', '$alamat', '$kota', '$telepon', '$jam_buka', '$jam_tutup')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Bioskop berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Create Studio
if (isset($_POST['tambah_studio'])) {
    $id_bioskop = $_POST['id_bioskop'];
    $nama_studio = mysqli_real_escape_string($conn, $_POST['nama_studio']);
    $kapasitas = $_POST['kapasitas_total'];
    $tipe = $_POST['tipe_studio'];
    $baris = $_POST['jumlah_baris'];
    $kursi_per_baris = $_POST['kursi_per_baris'];
    
    $query = "INSERT INTO studio (id_bioskop, nama_studio, kapasitas_total, tipe_studio, jumlah_baris, kursi_per_baris) 
              VALUES ($id_bioskop, '$nama_studio', $kapasitas, '$tipe', $baris, $kursi_per_baris)";
    
    if (mysqli_query($conn, $query)) {
        // Generate kursi otomatis
        $id_studio = mysqli_insert_id($conn);
        generateKursi($conn, $id_studio, $baris, $kursi_per_baris, $tipe);
        
        $success = "Studio berhasil ditambahkan dengan " . ($baris * $kursi_per_baris) . " kursi!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Function generate kursi otomatis
function generateKursi($conn, $id_studio, $jumlah_baris, $kursi_per_baris, $tipe_studio) {
    $baris_huruf = range('A', 'Z');
    
    for ($i = 0; $i < $jumlah_baris; $i++) {
        for ($j = 1; $j <= $kursi_per_baris; $j++) {
            $kode_kursi = $baris_huruf[$i] . $j;
            $query = "INSERT INTO kursi (id_studio, kode_kursi, baris, nomor, tipe_kursi) 
                      VALUES ($id_studio, '$kode_kursi', '{$baris_huruf[$i]}', $j, '$tipe_studio')";
            mysqli_query($conn, $query);
        }
    }
}

// Get data
$bioskops = mysqli_query($conn, "SELECT * FROM bioskop ORDER BY nama_bioskop");
$studios = mysqli_query($conn, "
    SELECT s.*, b.nama_bioskop 
    FROM studio s 
    JOIN bioskop b ON s.id_bioskop = b.id_bioskop 
    ORDER BY b.nama_bioskop, s.nama_studio
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Cinemas</title>
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
                        <h1>Cinema Management</h1>
                        <p>Manage cinema locations and studios</p>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBioskopModal">
                            <i class="bi bi-plus-circle"></i> Add Cinema
                        </button>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahStudioModal">
                            <i class="bi bi-plus-square"></i> Add Studio
                        </button>
                    </div>
                </div>
            </div>

            <div class="content-area">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-building"></i>
                                Cinema Locations
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Cinema Name</th>
                                                <th>Location</th>
                                                <th>Phone</th>
                                                <th>Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($bioskop = mysqli_fetch_assoc($bioskops)): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= $bioskop['nama_bioskop'] ?></strong>
                                                    <br><small class="text-muted"><?= $bioskop['alamat_lengkap'] ?></small>
                                                </td>
                                                <td><?= $bioskop['kota'] ?></td>
                                                <td><?= $bioskop['no_telepon'] ?></td>
                                                <td>
                                                    <small><?= substr($bioskop['jam_buka'], 0, 5) ?> - <?= substr($bioskop['jam_tutup'], 0, 5) ?></small>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-door-closed"></i>
                                Studios
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Studio Name</th>
                                                <th>Cinema</th>
                                                <th>Type</th>
                                                <th>Capacity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($studio = mysqli_fetch_assoc($studios)): ?>
                                            <tr>
                                                <td><strong><?= $studio['nama_studio'] ?></strong></td>
                                                <td><?= $studio['nama_bioskop'] ?></td>
                                                <td>
                                                    <span class="badge badge-<?= 
                                                        $studio['tipe_studio'] == 'vip' ? 'warning' : 
                                                        ($studio['tipe_studio'] == 'premium' ? 'info' : 'primary')
                                                    ?>">
                                                        <?= strtoupper($studio['tipe_studio']) ?>
                                                    </span>
                                                </td>
                                                <td><?= $studio['kapasitas_total'] ?> seats</td>
                                                <td>
                                                    <span class="badge badge-<?= $studio['status_studio'] == 'active' ? 'success' : 'danger' ?>">
                                                        <?= $studio['status_studio'] ?>
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
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <?php include 'modals_bioskop.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>