<?php
require 'koneksi.php';

// Handle Create
if (isset($_POST['tambah_film'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul_film']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $durasi = $_POST['durasi_menit'];
    $sinopsis = mysqli_real_escape_string($conn, $_POST['sinopsis']);
    $sutradara = mysqli_real_escape_string($conn, $_POST['sutradara']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating_usia']);
    
    $query = "INSERT INTO film (created_by_admin, judul_film, genre, durasi_menit, sinopsis, sutradara, rating_usia) 
              VALUES (1, '$judul', '$genre', $durasi, '$sinopsis', '$sutradara', '$rating')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Film berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Update
if (isset($_POST['update_film'])) {
    $id = $_POST['id_film'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul_film']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);
    $durasi = $_POST['durasi_menit'];
    $sinopsis = mysqli_real_escape_string($conn, $_POST['sinopsis']);
    $sutradara = mysqli_real_escape_string($conn, $_POST['sutradara']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating_usia']);
    
    $query = "UPDATE film SET 
              judul_film = '$judul', 
              genre = '$genre', 
              durasi_menit = $durasi, 
              sinopsis = '$sinopsis', 
              sutradara = '$sutradara', 
              rating_usia = '$rating' 
              WHERE id_film = $id";
    
    if (mysqli_query($conn, $query)) {
        $success = "Film berhasil diupdate!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $query = "DELETE FROM film WHERE id_film = $id";
    
    if (mysqli_query($conn, $query)) {
        $success = "Film berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get all films
$films = mysqli_query($conn, "SELECT * FROM film ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Movies</title>
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
                        <h1>Movie Management</h1>
                        <p>Manage all movies in the system</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahFilmModal">
                        <i class="bi bi-plus-circle"></i>
                        Add Movie
                    </button>
                </div>
            </div>

            <div class="content-area">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-film"></i>
                        Movie List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Movie Title</th>
                                        <th>Genre</th>
                                        <th>Duration</th>
                                        <th>Director</th>
                                        <th>Rating</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($film = mysqli_fetch_assoc($films)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= $film['judul_film'] ?></strong>
                                            <?php if ($film['sinopsis']): ?>
                                                <br><small class="text-muted"><?= substr($film['sinopsis'], 0, 50) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $film['genre'] ?></td>
                                        <td><?= $film['durasi_menit'] ?>m</td>
                                        <td><?= $film['sutradara'] ?></td>
                                        <td>
                                            <span class="badge badge-warning"><?= $film['rating_usia'] ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editFilmModal"
                                                    data-id="<?= $film['id_film'] ?>"
                                                    data-judul="<?= $film['judul_film'] ?>"
                                                    data-genre="<?= $film['genre'] ?>"
                                                    data-durasi="<?= $film['durasi_menit'] ?>"
                                                    data-sinopsis="<?= $film['sinopsis'] ?>"
                                                    data-sutradara="<?= $film['sutradara'] ?>"
                                                    data-rating="<?= $film['rating_usia'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="film.php?hapus=<?= $film['id_film'] ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus film ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
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

    <!-- Modals -->
    <?php include 'modals_film.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk mengisi data di modal edit
        var editFilmModal = document.getElementById('editFilmModal');
        editFilmModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            
            document.getElementById('edit_id_film').value = button.getAttribute('data-id');
            document.getElementById('edit_judul').value = button.getAttribute('data-judul');
            document.getElementById('edit_genre').value = button.getAttribute('data-genre');
            document.getElementById('edit_durasi').value = button.getAttribute('data-durasi');
            document.getElementById('edit_sinopsis').value = button.getAttribute('data-sinopsis');
            document.getElementById('edit_sutradara').value = button.getAttribute('data-sutradara');
            document.getElementById('edit_rating').value = button.getAttribute('data-rating');
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>