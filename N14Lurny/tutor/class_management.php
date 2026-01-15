<?php 
include '../includes/header_tutor.php'; 
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

$tutor_id = $_SESSION['user_id'];

// Lấy danh sách lớp + Đếm số học viên đã nhận
$sql = "SELECT c.*, 
       (SELECT COUNT(*) FROM class_registrations r WHERE r.class_id = c.id AND r.status = 'accepted') as accepted_count
       FROM classes c 
       WHERE c.tutor_id = ? 
       ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">

<div class="container py-5">
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'class_full'): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Không thể mở lại lớp! Lớp học đã đủ số lượng học viên.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Quản lý lớp học</h3>
            <p class="text-muted mb-0">Danh sách các lớp bạn đã đăng</p>
        </div>
        <a href="new_class.php" class="btn btn-brand px-4 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Đăng lớp mới
        </a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): 
            $max_students = $row['max_students'] ?? 1; 
            $is_full = ($row['accepted_count'] >= $max_students);
            
            // Xử lý hiển thị Giá (Format lại cho đẹp)
            $price_display = is_numeric($row['price']) ? number_format($row['price'], 0, ',', '.') . ' đ' : $row['price'];
        ?>
            <div class="card p-0 mb-4 border-0 shadow-sm <?php echo ($row['status'] == 'hidden' || $row['status'] == 'rejected') ? 'opacity-75 bg-light' : ''; ?>">
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <span class="badge blue"><?= htmlspecialchars($row['subject']) ?></span>
                                <span class="badge gray"><i class="bi bi-laptop"></i> <?= htmlspecialchars($row['method']) ?></span>
                                
                                <?php if($row['status'] == 'active'): ?>
                                    <span class="badge green">● Đang tìm</span>
                                <?php elseif($row['status'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark">⏳ Chờ duyệt</span>
                                <?php elseif($row['status'] == 'rejected'): ?>
                                    <span class="badge bg-danger">🚫 Bị từ chối</span>
                                <?php else: ?>
                                    <span class="badge gray">🔒 Đã đóng</span>
                                <?php endif; ?>

                                <?php if($is_full && $row['status'] != 'rejected'): ?>
                                    <span class="badge bg-secondary text-white ms-1">Đã đủ học viên (<?= $row['accepted_count'] ?>/<?= $max_students ?>)</span>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="fw-bold mb-2">
                                <a href="see_details.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none hover-brand">
                                    <?= htmlspecialchars($row['title']) ?>
                                </a>
                            </h5>

                            <div class="d-flex flex-wrap gap-3 text-muted small mt-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt me-2 text-primary"></i> <?= htmlspecialchars($row['location']) ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash-coin me-2 text-success"></i> <?= $price_display ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar3 me-2"></i> <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 border-start-md ps-md-4">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="see_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm px-3" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <a href="edit_class.php?id=<?= $row['id'] ?>" class="btn btn-light btn-sm px-3 border" title="Sửa thông tin">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <?php if($row['status'] == 'pending'): ?>
                                    <button class="btn btn-secondary btn-sm px-3 border disabled" disabled title="Bài đăng đang chờ Admin duyệt">
                                        <i class="bi bi-hourglass-split"></i> Chờ duyệt
                                    </button>

                                <?php elseif($row['status'] == 'active'): ?>
                                    <a href="update_status.php?id=<?= $row['id'] ?>&action=close" 
                                       class="btn btn-warning btn-sm px-3 text-dark border" 
                                       onclick="return confirm('Bạn muốn tạm khóa lớp này? Học sinh sẽ không tìm thấy lớp nữa.')" title="Khóa lớp">
                                        <i class="bi bi-lock-fill"></i>
                                    </a>
                                
                                <?php elseif($row['status'] == 'rejected'): ?>
                                    <button class="btn btn-danger btn-sm px-3 border disabled" disabled title="Bài đăng này đã bị Admin từ chối">
                                        <i class="bi bi-x-circle"></i> Từ chối
                                    </button>

                                <?php else: ?>
                                    <?php if ($is_full): ?>
                                        <button class="btn btn-secondary btn-sm px-3 border disabled-cursor" title="Lớp đã đủ học viên, không thể mở lại">
                                            <i class="bi bi-dash-circle"></i> Đầy
                                        </button>
                                    <?php else: ?>
                                        <a href="update_status.php?id=<?= $row['id'] ?>&action=open" 
                                           class="btn btn-success btn-sm px-3 border" 
                                           title="Mở lại lớp">
                                            <i class="bi bi-unlock-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <a href="delete_class.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-light btn-sm px-3 text-danger border hover-danger" 
                                   onclick="return confirm('CẢNH BÁO: Bạn có chắc muốn XÓA VĨNH VIỄN lớp này không? Hành động này không thể hoàn tác.')" title="Xóa vĩnh viễn">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="100" class="opacity-25 mb-3" alt="Empty">
            <p class="text-muted">Bạn chưa đăng lớp học nào.</p>
            <a href="new_class.php" class="btn btn-brand mt-2">Đăng lớp ngay</a>
        </div>
    <?php endif; ?>

</div>
</body>
</html>