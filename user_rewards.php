<?php
require 'koneksi.php';
include 'user_header.php';

if (isset($_POST['redeem'])) {
    $id_reward = $_POST['id_reward'];
    $cost = $_POST['cost'];
    $uid = $_SESSION['id_pengguna'];
    
    $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total_poin FROM pengguna WHERE id_pengguna=$uid"));
    
    if ($u['total_poin'] >= $cost) {
        mysqli_query($conn, "UPDATE pengguna SET total_poin = total_poin - $cost WHERE id_pengguna=$uid");
        mysqli_query($conn, "UPDATE reward SET stok = stok - 1 WHERE id_reward=$id_reward");
        $kode = "V-" . rand(1000,9999);
        $tgl = date('Y-m-d');
        mysqli_query($conn, "INSERT INTO penukaran_reward (id_pengguna, id_reward, tanggal_penukaran, poin_ditukar, kode_voucher, status_penukaran) VALUES ($uid, $id_reward, '$tgl', $cost, '$kode', 'active')");
        echo "<script>alert('Redeem Success! Voucher Code: $kode'); window.location='user_rewards.php';</script>";
    } else {
        echo "<script>alert('Insufficient Points!');</script>";
    }
}

$rewards = mysqli_query($conn, "SELECT * FROM reward WHERE is_active=1 AND stok > 0");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Rewards • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container py-4">
        <div class="page-header">
            <div class="header-title">
                <h1>Redeem Rewards</h1>
                <p>Exchange your points for exclusive items</p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Your Balance</small>
                <h2 class="fw-bold text-primary mb-0"><?= $u['total_poin'] ?> Pts</h2>
            </div>
        </div>

        <div class="row g-4">
            <?php while($r = mysqli_fetch_assoc($rewards)): ?>
            <div class="col-md-4">
                <div class="card h-100 text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <div class="mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:80px; height:80px;">
                            <i class="bi bi-gift display-4 text-primary"></i>
                        </div>
                        <h5 class="fw-bold"><?= $r['nama_reward'] ?></h5>
                        <p class="text-muted small mb-4"><?= $r['deskripsi'] ?></p>
                        
                        <div class="mt-auto w-100">
                            <h3 class="text-dark fw-bold mb-3"><?= $r['poin_dibutuhkan'] ?> <small class="fs-6 text-muted">pts</small></h3>
                            <form method="POST">
                                <input type="hidden" name="id_reward" value="<?= $r['id_reward'] ?>">
                                <input type="hidden" name="cost" value="<?= $r['poin_dibutuhkan'] ?>">
                                <button type="submit" name="redeem" class="btn btn-primary w-100" 
                                    <?= ($u['total_poin'] < $r['poin_dibutuhkan']) ? 'disabled' : '' ?>>
                                    Redeem Now
                                </button>
                            </form>
                            <small class="text-muted mt-2 d-block"><?= $r['stok'] ?> remaining</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>