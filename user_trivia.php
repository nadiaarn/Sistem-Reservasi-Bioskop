<?php
require 'koneksi.php';
include 'user_header.php';

$id_film_selected = isset($_GET['id_film']) ? $_GET['id_film'] : 0;
$show_result = false; // Flag untuk menampilkan layar hasil
$total_poin = 0;
$correct_count = 0;
$total_questions = 0;

// Handle Submit
if (isset($_POST['submit_quiz'])) {
    $answers = $_POST['jawaban'];
    $uid = $_SESSION['id_pengguna'];
    $total_questions = count($answers);
    
    foreach ($answers as $id_trivia => $jawaban_user) {
        $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jawaban_benar, poin_reward FROM trivia WHERE id_trivia=$id_trivia"));
        if ($jawaban_user == $cek['jawaban_benar']) {
            $total_poin += $cek['poin_reward'];
            $correct_count++;
        }
    }
    
    // Update Poin User
    if ($total_poin > 0) {
        mysqli_query($conn, "UPDATE pengguna SET total_poin = total_poin + $total_poin WHERE id_pengguna=$uid");
    }
    
    // Tampilkan Layar Hasil
    $show_result = true;
}

// Ambil Soal
$query_trivia = "SELECT t.*, f.judul_film FROM trivia t JOIN film f ON t.id_film = f.id_film WHERE t.is_active=1";
if ($id_film_selected > 0) $query_trivia .= " AND t.id_film = $id_film_selected";
$trivias = mysqli_query($conn, $query_trivia);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Trivia • CinemaPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Custom Radio Button Style */
        .option-input:checked + .option-label {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            box-shadow: 0 4px 6px rgba(13, 110, 253, 0.2);
        }
        .option-label {
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #dee2e6;
        }
        .option-label:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">

    <div class="container py-5">
        
        <?php if ($show_result): ?>
        <div class="row justify-content-center animate__animated animate__fadeIn">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg text-center overflow-hidden">
                    <div class="card-header bg-white border-0 pt-5 pb-0">
                        <?php if ($total_poin > 0): ?>
                            <div class="mb-3">
                                <span class="d-inline-block p-4 rounded-circle bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-trophy-fill display-1"></i>
                                </span>
                            </div>
                            <h2 class="fw-bold text-dark">Great Job!</h2>
                            <p class="text-muted">You've completed the quiz.</p>
                        <?php else: ?>
                            <div class="mb-3">
                                <span class="d-inline-block p-4 rounded-circle bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-emoji-frown-fill display-1"></i>
                                </span>
                            </div>
                            <h2 class="fw-bold text-dark">Nice Try!</h2>
                            <p class="text-muted">Keep watching movies to learn more.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body px-5 pb-5">
                        <div class="row g-3 mb-4 mt-2">
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Correct Answers</small>
                                    <div class="fs-2 fw-bold text-dark"><?= $correct_count ?> <span class="fs-6 text-muted">/ <?= $total_questions ?></span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Points Earned</small>
                                    <div class="fs-2 fw-bold text-warning">+<?= $total_poin ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="user_rewards.php" class="btn btn-primary py-2 fw-bold">Use Points for Rewards</a>
                            <a href="user_home.php" class="btn btn-outline-secondary py-2">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
            
            <div class="page-header mb-4">
                <div class="header-title">
                    <h1>Trivia Challenge</h1>
                    <p>Test your knowledge and earn rewards!</p>
                </div>
            </div>

            <?php if(mysqli_num_rows($trivias) > 0): ?>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <form method="POST">
                            <?php $no = 1; while($t = mysqli_fetch_assoc($trivias)): ?>
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge bg-light text-primary border border-primary px-3 py-2 rounded-pill">Question #<?= $no++ ?></span>
                                        <div class="text-end">
                                            <small class="d-block text-muted fw-bold" style="font-size: 0.7rem;">REWARD</small>
                                            <span class="text-warning fw-bold">+<?= $t['poin_reward'] ?> Pts</span>
                                        </div>
                                    </div>
                                    
                                    <h5 class="fw-bold mb-4" style="line-height: 1.5;"><?= $t['pertanyaan'] ?></h5>
                                    
                                    <div class="d-flex flex-column gap-2">
                                        <?php 
                                        $options = ['A', 'B', 'C', 'D'];
                                        foreach($options as $opt): 
                                            $val = $t['pilihan_'.strtolower($opt)];
                                        ?>
                                        <div class="position-relative">
                                            <input type="radio" class="btn-check option-input" 
                                                   name="jawaban[<?= $t['id_trivia'] ?>]" 
                                                   value="<?= $opt ?>" 
                                                   id="q<?= $t['id_trivia'] . $opt ?>" required>
                                            
                                            <label class="option-label w-100 p-3 rounded-3 d-flex align-items-center" for="q<?= $t['id_trivia'] . $opt ?>">
                                                <span class="fw-bold me-3 border rounded-circle d-flex align-items-center justify-content-center" 
                                                      style="width: 30px; height: 30px; font-size: 0.8rem;"><?= $opt ?></span>
                                                <span><?= $val ?></span>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            
                            <div class="d-grid mb-5">
                                <button type="submit" name="submit_quiz" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm">
                                    Submit Answers <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3 text-muted opacity-50">
                        <i class="bi bi-controller" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold">No Trivia Available</h4>
                    <p class="text-muted">There are no quizzes for this movie yet.</p>
                    <a href="user_home.php" class="btn btn-secondary mt-2 px-4">Back to Movies</a>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>