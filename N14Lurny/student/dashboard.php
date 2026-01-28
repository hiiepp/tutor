<?php
session_start();
include '../includes/header.php'; 
require_once '../config/db.php';

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login_register.php"); exit();
}

$student_id = $_SESSION['user_id'];
$today = date('Y-m-d');

// Truy vấn các lớp đã đăng ký (THÊM c.start_date VÀO SQL)
$sql = "SELECT r.*, c.title, c.subject, c.price, c.method, c.location, c.start_date, c.end_date, u.full_name as tutor_name
        FROM class_registrations r
        JOIN classes c ON r.class_id = c.id
        JOIN users u ON c.tutor_id = u.id
        WHERE r.student_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/student.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<section class="tutor-dashboard">
  <div class="container py-4">

    <h4 class="dashboard-title mb-4">Lớp học của tôi</h4>

    <?php if ($result->num_rows > 0): ?>
        <div class="row">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="class-card h-100 shadow-sm">
                    
                    <div class="card-section">
                        <h6 class="class-title text-truncate" title="<?= htmlspecialchars($row['title']) ?>">
                            <a href="../class-detail.php?id=<?= $row['class_id'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h6>
                        <div class="text-muted small">
                            <i class="bi bi-person-badge"></i> Gia sư: <?= htmlspecialchars($row['tutor_name']) ?>
                        </div>
                    </div>

                    <div class="card-section">
                        <div class="class-price">
                            <?= htmlspecialchars($row['price']) ?>
                        </div>
                    </div>

                    <div class="card-section">
                        <div class="class-tags">
                            <span><?= htmlspecialchars($row['subject']) ?></span>
                            <span><?= htmlspecialchars($row['method']) ?></span>
                        </div>
                        
                        <div class="small text-muted mb-1">
                            <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($row['location']) ?>
                        </div>
                        
                        <div class="small text-muted">
                            <i class="bi bi-calendar-range me-1"></i>
                            <?php 
                                if (!empty($row['start_date']) && !empty($row['end_date'])) {
                                    echo date('d/m/Y', strtotime($row['start_date'])) . ' - ' . date('d/m/Y', strtotime($row['end_date']));
                                } else {
                                    echo "Chưa cập nhật thời gian";
                                }
                            ?>
                        </div>
                    </div>

                    <div class="card-section card-section-last pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <?php if($row['status'] == 'pending'): ?>
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            <?php elseif($row['status'] == 'accepted'): ?>
                                <span class="badge bg-success">Đã được nhận</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Bị từ chối</span>
                            <?php endif; ?>

                            <a href="../class-detail.php?id=<?= $row['class_id'] ?>" class="btn btn-outline-secondary btn-sm">
                               Xem chi tiết
                            </a>
                        </div>

                        <?php 
                            if($row['status'] == 'accepted' && !empty($row['start_date']) && $today >= $row['start_date']):
                        ?>
                            <div class="mt-2 text-end">
                                <a href="../class-detail.php?id=<?= $row['class_id'] ?>" class="text-danger small text-decoration-none fw-bold">
                                    <i class="bi bi-flag-fill"></i> Báo cáo vấn đề
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="80" class="opacity-25 mb-3">
            <p class="text-muted">Bạn chưa đăng ký lớp học nào.</p>
            <a href="../index.php" class="btn btn-success fw-bold">Tìm gia sư ngay</a>
        </div>
    <?php endif; ?>

  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>