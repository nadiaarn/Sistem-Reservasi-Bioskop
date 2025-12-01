<?php
require 'koneksi.php';

// Handle Create Trivia
if (isset($_POST['tambah_trivia'])) {
    $id_film = $_POST['id_film'];
    $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $pilihan_a = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
    $pilihan_b = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
    $pilihan_c = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
    $pilihan_d = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
    $jawaban_benar = $_POST['jawaban_benar'];
    $poin_reward = $_POST['poin_reward'];
    $tingkat_kesulitan = $_POST['tingkat_kesulitan'];
    
    $query = "INSERT INTO trivia (id_film, created_by_admin, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, poin_reward, tingkat_kesulitan) 
              VALUES ($id_film, 1, '$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar', $poin_reward, '$tingkat_kesulitan')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Trivia berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get data
$trivias = mysqli_query($conn, "
    SELECT t.*, f.judul_film 
    FROM trivia t 
    JOIN film f ON t.id_film = f.id_film 
    ORDER BY t.created_at DESC
");

$films = mysqli_query($conn, "SELECT id_film, judul_film FROM film ORDER BY judul_film");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaPro • Trivia</title>
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
                        <h1>Movie Trivia</h1>
                        <p>Manage interactive movie quizzes</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTriviaModal">
                        <i class="bi bi-plus-circle"></i> Add Trivia
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
                            COUNT(*) as total_trivia,
                            SUM(CASE WHEN tingkat_kesulitan = 'easy' THEN 1 ELSE 0 END) as easy,
                            SUM(CASE WHEN tingkat_kesulitan = 'medium' THEN 1 ELSE 0 END) as medium,
                            SUM(CASE WHEN tingkat_kesulitan = 'hard' THEN 1 ELSE 0 END) as hard
                        FROM trivia 
                        WHERE is_active = 1
                    ");
                    $stat = mysqli_fetch_assoc($stats);
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['total_trivia'] ?></div>
                        <div class="stat-label">Total Trivia</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['easy'] ?></div>
                        <div class="stat-label">Easy Level</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['medium'] ?></div>
                        <div class="stat-label">Medium Level</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stat['hard'] ?></div>
                        <div class="stat-label">Hard Level</div>
                    </div>
                </div>

                <!-- Trivia List -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-question-circle"></i>
                        Trivia List
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($trivias) > 0): ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Movie</th>
                                        <th>Answer Choices</th>
                                        <th>Correct Answer</th>
                                        <th>Points</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($trivia = mysqli_fetch_assoc($trivias)): ?>
                                    <tr>
                                        <td>
                                            <strong><?= $trivia['pertanyaan'] ?></strong>
                                        </td>
                                        <td><?= $trivia['judul_film'] ?></td>
                                        <td>
                                            <small>
                                                <div class="mb-1"><strong>A.</strong> <?= $trivia['pilihan_a'] ?></div>
                                                <div class="mb-1"><strong>B.</strong> <?= $trivia['pilihan_b'] ?></div>
                                                <div class="mb-1"><strong>C.</strong> <?= $trivia['pilihan_c'] ?></div>
                                                <div class="mb-1"><strong>D.</strong> <?= $trivia['pilihan_d'] ?></div>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?= $trivia['jawaban_benar'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning"><?= $trivia['poin_reward'] ?> points</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= 
                                                $trivia['tingkat_kesulitan'] == 'easy' ? 'success' : 
                                                ($trivia['tingkat_kesulitan'] == 'medium' ? 'warning' : 'danger')
                                            ?>">
                                                <?= $trivia['tingkat_kesulitan'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $trivia['is_active'] ? 'primary' : 'secondary' ?>">
                                                <?= $trivia['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-question-circle" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">No Trivia Available</h4>
                            <p>Start adding trivia to increase user engagement</p>
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#tambahTriviaModal">
                                <i class="bi bi-plus-circle"></i> Add First Trivia
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Trivia -->
    <div class="modal fade" id="tambahTriviaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Trivia</h5>
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
                                    <label class="form-label">Difficulty Level</label>
                                    <select class="form-control" name="tingkat_kesulitan" required>
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question</label>
                            <textarea class="form-control" name="pertanyaan" rows="3" placeholder="Enter trivia question..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choice A</label>
                                    <input type="text" class="form-control" name="pilihan_a" placeholder="Answer A" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Choice B</label>
                                    <input type="text" class="form-control" name="pilihan_b" placeholder="Answer B" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Choice C</label>
                                    <input type="text" class="form-control" name="pilihan_c" placeholder="Answer C" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Choice D</label>
                                    <input type="text" class="form-control" name="pilihan_d" placeholder="Answer D" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Correct Answer</label>
                                    <select class="form-control" name="jawaban_benar" required>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Reward Points</label>
                                    <input type="number" class="form-control" name="poin_reward" value="10" min="5" max="50" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="tambah_trivia" class="btn btn-primary">Save Trivia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>