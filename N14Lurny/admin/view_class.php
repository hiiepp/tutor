<?php
session_start();
require __DIR__ . '/../config/db.php';

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: classes.php"); exit();
}

$id = intval($_GET['id']);
$message = "";

// --- 2. XỬ LÝ HÀNH ĐỘNG (DUYỆT / TỪ CHỐI / XÓA) ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        $conn->query("UPDATE classes SET status = 'active' WHERE id=$id");
        $message = "Đã duyệt bài đăng thành công!";
    } 
    elseif ($action == 'reject') {
        $conn->query("UPDATE classes SET status = 'rejected' WHERE id=$id");
        $message = "Đã từ chối bài đăng.";
    }
    elseif ($action == 'delete') {
        $conn->query("DELETE FROM classes WHERE id=$id");
        header("Location: classes.php?msg=deleted"); // Xóa xong thì về danh sách
        exit();
    }
}

// 3. LẤY THÔNG TIN CHI TIẾT
$sql = "SELECT classes.*, users.full_name, users.email, users.phone 
        FROM classes 
        LEFT JOIN users ON classes.tutor_id = users.id 
        WHERE classes.id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Không tìm thấy bài đăng.");
}
$row = $result->fetch_assoc();

// Format hiển thị
$status_badge = '';
if ($row['status'] == 'pending') $status_badge = '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
elseif ($row['status'] == 'active') $status_badge = '<span class="badge bg-success">Đang hiển thị</span>';
elseif ($row['status'] == 'rejected') $status_badge = '<span class="badge bg-danger">Đã từ chối</span>';
else $status_badge = '<span class="badge bg-secondary">Đã đóng</span>';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết bài đăng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="mb-4">
            <a href="classes.php" class="text-decoration-none text-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $message; ?>
                <a href="view_class.php?id=<?= $id ?>" class="btn-close text-decoration-none"></a>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-primary">Nội dung lớp học</h5>
                            <?php echo $status_badge; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="fw-bold mb-3"><?php echo htmlspecialchars($row['title']); ?></h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <ul class="list-unstyled text-secondary">
                                    <li class="mb-2"><i class="fas fa-book w-25px"></i> <strong>Môn học:</strong> <?php echo htmlspecialchars($row['subject']); ?></li>
                                    <li class="mb-2"><i class="fas fa-graduation-cap w-25px"></i> <strong>Lớp:</strong> <?php echo htmlspecialchars($row['grade']); ?></li>
                                    <li class="mb-2"><i class="fas fa-laptop w-25px"></i> <strong>Hình thức:</strong> <?php echo htmlspecialchars($row['method']); ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled text-secondary">
                                    <li class="mb-2"><i class="fas fa-money-bill-wave w-25px"></i> <strong>Học phí:</strong> <span class="text-success fw-bold"><?php echo htmlspecialchars($row['price']); ?></span></li>
                                    <li class="mb-2"><i class="fas fa-map-marker-alt w-25px"></i> <strong>Khu vực:</strong> <?php echo htmlspecialchars($row['location']); ?></li>
                                    <li class="mb-2"><i class="fas fa-calendar-alt w-25px"></i> <strong>Ngày đăng:</strong> <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></li>
                                </div>
                        </div>

                        <div class="border-top pt-3">
                            <h6 class="fw-bold text-dark">Mô tả chi tiết & Yêu cầu:</h6>
                            <div class="bg-light p-3 rounded text-dark" style="white-space: pre-line;">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-tasks me-2"></i> Xét duyệt</h6>
                    </div>
                    <div class="card-body">
                        <?php if($row['status'] == 'pending'): ?>
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-1"></i> Bài đăng này đang chờ duyệt. Vui lòng kiểm tra kỹ nội dung trước khi chấp nhận.
                            </div>
                            <div class="d-grid gap-2">
                                <a href="view_class.php?id=<?= $id ?>&action=approve" class="btn btn-success fw-bold" onclick="return confirm('Xác nhận DUYỆT bài đăng này?')">
                                    <i class="fas fa-check me-2"></i> Duyệt bài ngay
                                </a>
                                <a href="view_class.php?id=<?= $id ?>&action=reject" class="btn btn-outline-danger" onclick="return confirm('Xác nhận TỪ CHỐI bài đăng này?')">
                                    <i class="fas fa-ban me-2"></i> Từ chối
                                </a>
                            </div>
                        <?php elseif($row['status'] == 'active'): ?>
                            <div class="alert alert-success small mb-3">
                                <i class="fas fa-check-circle me-1"></i> Bài đăng đang hiển thị công khai.
                            </div>
                            <div class="d-grid">
                                <a href="view_class.php?id=<?= $id ?>&action=reject" class="btn btn-outline-warning text-dark btn-sm" onclick="return confirm('Bạn muốn gỡ bài đăng này xuống?')">
                                    <i class="fas fa-eye-slash me-2"></i> Gỡ bài (Chuyển sang Từ chối)
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary small mb-2">
                                Trạng thái hiện tại: <strong><?php echo ucfirst($row['status']); ?></strong>
                            </div>
                            <?php if($row['status'] == 'rejected'): ?>
                                <a href="view_class.php?id=<?= $id ?>&action=approve" class="btn btn-outline-success btn-sm w-100">
                                    <i class="fas fa-redo me-2"></i> Duyệt lại
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="avatar-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                            <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                        </div>
                        <h6 class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></h6>
                        <p class="text-muted small mb-3">Gia sư đăng bài</p>
                        
                        <div class="text-start small border-top pt-3">
                            <div class="mb-2"><i class="fas fa-envelope me-2 text-secondary"></i> <?php echo $row['email']; ?></div>
                            <div class="mb-2"><i class="fas fa-phone me-2 text-secondary"></i> <?php echo $row['phone'] ?? 'Chưa cập nhật'; ?></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>