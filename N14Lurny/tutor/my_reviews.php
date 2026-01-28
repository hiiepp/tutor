<?php
session_start();
require '../config/db.php';
include '../includes/header_tutor.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login_register.php"); exit();
}

$tutor_id = $_SESSION['user_id'];

// Lấy thông tin điểm trung bình
$u_info = $conn->query("SELECT avg_rating, review_count FROM users WHERE id = $tutor_id")->fetch_assoc();

// Lấy danh sách đánh giá
$sql = "SELECT r.*, s.full_name as student_name, s.avatar as s_avatar, c.title as class_title 
        FROM reviews r 
        JOIN users s ON r.student_id = s.id 
        JOIN classes c ON r.class_id = c.id 
        WHERE r.tutor_id = $tutor_id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đánh giá của tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">
<div class="container py-5">
    
    <div class="row justify-content-center mb-4">
        <div class="col-md-8 text-center">
            <h3 class="fw-bold mb-2">Đánh giá từ Học viên</h3>
            <div class="bg-white p-3 rounded shadow-sm d-inline-block border">
                <div class="display-4 fw-bold text-warning">
                    <?= $u_info['avg_rating'] > 0 ? $u_info['avg_rating'] : '0.0' ?> <i class="bi bi-star-fill fs-3"></i>
                </div>
                <div class="text-muted small">Tổng cộng <?= $u_info['review_count'] ?> đánh giá</div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold me-2" style="width: 40px; height: 40px;">
                                        <?= mb_substr($row['student_name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($row['student_name']) ?></h6>
                                        <small class="text-muted" style="font-size: 0.8rem;">Lớp: <?= htmlspecialchars($row['class_title']) ?></small>
                                    </div>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y', strtotime($row['created_at'])) ?></small>
                            </div>
                            
                            <div class="mt-2 mb-2 text-warning">
                                <?php 
                                    for($i=1; $i<=5; $i++) {
                                        echo ($i <= $row['rating']) ? '<i class="bi bi-star-fill"></i> ' : '<i class="bi bi-star text-secondary opacity-25"></i> ';
                                    }
                                ?>
                            </div>
                            
                            <div class="bg-light p-2 rounded text-dark fst-italic border-start border-4 border-warning">
                                "<?= nl2br(htmlspecialchars($row['comment'])) ?>"
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-chat-square-text fs-1 opacity-50"></i>
                    <p class="mt-2">Chưa có đánh giá nào từ học viên.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>