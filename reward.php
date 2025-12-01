<?php
require 'koneksi.php';

// Handle Create Reward
if (isset($_POST['tambah_reward'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_reward']);
    $jenis = $_POST['jenis_reward'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $poin_dibutuhkan = $_POST['poin_dibutuhkan'];
    $nilai_diskon = $_POST['nilai_diskon'];
    $stok = $_POST['stok'];
    
    $query = "INSERT INTO reward (created_by_admin, nama_reward, jenis_reward, deskripsi, poin_dibutuhkan, nilai_diskon, stok) 
              VALUES (1, '$nama', '$jenis', '$deskripsi', $poin_dibutuhkan, $nilai_diskon, $stok)";
    
    if (mysqli_query($conn, $query)) {
        $success = "Reward berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get data
$rewards = mysqli_query($conn, "SELECT * FROM reward ORDER BY created_at DESC");
$penukaran = mysqli_query($conn, "
    SELECT pr.*, p.nama_lengkap, r.nama_reward 
    FROM penukaran_reward pr 
    JOIN pengguna p ON pr.id_pengguna = p.id_pengguna 
    JOIN reward r ON pr.id_reward = r.id_reward 
    ORDER BY pr.tanggal_penukaran DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Rewards</title>
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
                        <h1>Reward System</h1>
                        <p>Manage loyalty points and rewards</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRewardModal">
                        <i class="bi bi-plus-circle"></i> Add Reward
                    </button>
                </div>
            </div>

            <div class="content-area">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>

                <!-- Stats -->
                <div class="stats-grid">
                    <?php
                    $stats = mysqli_query($conn, "
                        SELECT 
                            COUNT(*) as total_reward,
                            SUM(stok) as total_stok,
                            SUM(poin_dibutuhkan) as total_poin,
                            (SELECT COUNT(*) FROM penukaran_reward WHERE is_used = 1) as reward_terpakai
                        FROM reward 
                        WHERE is_active = 1
                    ");
                    $stat = mysqli_fetch_assoc($stats);
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_reward'] ?></div>
                        <div class="stat-label">Total Rewards</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_stok'] ?></div>
                        <div class="stat-label">Total Stock</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_poin'] ?></div>
                        <div class="stat-label">Total Points</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['reward_terpakai'] ?></div>
                        <div class="stat-label">Rewards Used</div>
                    </div>
                </div>

                <!-- Rewards List -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-gift"></i>
                                Rewards List
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Reward Name</th>
                                                <th>Type</th>
                                                <th>Points</th>
                                                <th>Value</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($reward = mysqli_fetch_assoc($rewards)): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= $reward['nama_reward'] ?></strong>
                                                    <br><small class="text-muted"><?= $reward['deskripsi'] ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= 
                                                        $reward['jenis_reward'] == 'diskon' ? 'primary' : 
                                                        ($reward['jenis_reward'] == 'voucher' ? 'success' : 'info')
                                                    ?>">
                                                        <?= ucfirst($reward['jenis_reward']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-warning"><?= $reward['poin_dibutuhkan'] ?> points</span>
                                                </td>
                                                <td>
                                                    <?php if ($reward['nilai_diskon']): ?>
                                                        Rp <?= number_format($reward['nilai_diskon'], 0, ',', '.') ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $reward['stok'] > 10 ? 'success' : 'warning' ?>">
                                                        <?= $reward['stok'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $reward['is_active'] ? 'success' : 'secondary' ?>">
                                                        <?= $reward['is_active'] ? 'Active' : 'Inactive' ?>
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

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-clock-history"></i>
                                Redemption History
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Reward</th>
                                                <th>Points Used</th>
                                                <th>Voucher Code</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($tukar = mysqli_fetch_assoc($penukaran)): ?>
                                            <tr>
                                                <td><?= $tukar['nama_lengkap'] ?></td>
                                                <td><?= $tukar['nama_reward'] ?></td>
                                                <td><?= $tukar['poin_ditukar'] ?> points</td>
                                                <td>
                                                    <code><?= $tukar['kode_voucher'] ?></code>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= 
                                                        $tukar['status_penukaran'] == 'active' ? 'success' : 
                                                        ($tukar['status_penukaran'] == 'used' ? 'info' : 'secondary')
                                                    ?>">
                                                        <?= $tukar['status_penukaran'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d M Y', strtotime($tukar['tanggal_penukaran'])) ?></td>
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

    <!-- Modal Tambah Reward -->
    <div class="modal fade" id="tambahRewardModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Reward</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reward Name</label>
                            <input type="text" class="form-control" name="nama_reward" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reward Type</label>
                            <select class="form-control" name="jenis_reward" required>
                                <option value="diskon">Discount</option>
                                <option value="voucher">Voucher</option>
                                <option value="merchandise">Merchandise</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="deskripsi" rows="2"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Points Required</label>
                                    <input type="number" class="form-control" name="poin_dibutuhkan" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Value (Rp)</label>
                                    <input type="number" class="form-control" name="nilai_diskon">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stok" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="tambah_reward" class="btn btn-primary">Save Reward</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>