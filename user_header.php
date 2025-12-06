<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
require_once 'koneksi.php';
$uid = $_SESSION['id_pengguna'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total_poin FROM pengguna WHERE id_pengguna=$uid"));
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #1e293b; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="user_home.php" style="font-size: 1.5rem;">
            🎬 CinemaPro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'user_home.php' ? 'active' : '' ?>" href="user_home.php">Movies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'user_rewards.php' ? 'active' : '' ?>" href="user_rewards.php">Rewards</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'user_history.php' ? 'active' : '' ?>" href="user_history.php">History</a>
                </li>
                <li class="nav-item">
                    <div class="px-3 py-1 rounded-3 d-flex align-items-center" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        <span class="text-white fw-bold"><?= $u['total_poin'] ?> Pts</span>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="login.php" class="btn btn-sm btn-danger px-3" style="border-radius: 6px;">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div style="height: 80px;"></div>