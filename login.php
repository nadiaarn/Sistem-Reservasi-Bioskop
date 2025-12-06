<?php
session_start();
require 'koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: index.php");
        } else {
            header("Location: user_home.php");
        }
    } else {
        $error = "Email atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Inter', sans-serif; }
        .login-card { background: white; padding: 40px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #1e293b; border-color: #1e293b; }
        .btn-primary:hover { background-color: #0f172a; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <h1 class="fw-bold text-dark">🎬 CinemaPro</h1>
            <p class="text-muted">Sign in to your account</p>
        </div>
        <?php if(isset($error)) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">EMAIL ADDRESS</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">PASSWORD</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">Sign In</button>
        </form>
    </div>
</body>
</html>