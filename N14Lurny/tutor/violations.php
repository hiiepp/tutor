<?php
session_start();
require '../config/db.php';
include '../includes/header_tutor.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login_register.php"); exit();
}

$tutor_id = $_SESSION['user_id'];

// 1. Lấy thông tin cảnh cáo hiện tại
$u_sql = "SELECT warnings_count, is_banned FROM users WHERE id = $tutor_id";
$u_res = $conn->query($u_sql);
$user_info = $u_res->fetch_assoc();
$warnings = $user_info['warnings_count'];
$is_banned = $user_info['is_banned'];

// 2. Lấy danh sách các báo cáo ĐÃ BỊ DUYỆT (Gia sư bị phạt)
// Kèm theo trạng thái khiếu nại (nếu có)
$sql = "SELECT r.*, c.title as class_title, 
               a.id as appeal_id, a.status as appeal_status, a.admin_reply as appeal_reply
        FROM reports r
        JOIN classes c ON r.class_id = c.id
        LEFT JOIN appeals a ON r.id = a.report_id
        WHERE r.tutor_id = $tutor_id 
        AND r.status = 'approved' 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Vi phạm & Khiếu nại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/tutor.css">
    <style>
        .violation-card { border-left: 5px solid #dc3545; transition: 0.2s; }
        .violation-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .status-badge { font-size: 0.85rem; padding: 5px 10px; border-radius: 20px; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 text-center">
            <h3 class="fw-bold text-dark mb-2">Trung tâm Giải quyết Vi phạm</h3>
            <p class="text-muted mb-4">Theo dõi các báo cáo vi phạm và gửi khiếu nại nếu bạn cho rằng quyết định chưa thỏa đáng.</p>
            
            <div class="d-inline-flex align-items-center justify-content-center gap-4 px-5 py-3 bg-light rounded-3 border">
                <div class="text-center">
                    <small class="d-block text-secondary fw-bold text-uppercase">Trạng thái tài khoản</small>
                    <?php if($is_banned): ?>
                        <span class="badge bg-danger fs-6 mt-1">ĐÃ BỊ KHÓA <i class="bi bi-lock-fill"></i></span>
                    <?php else: ?>
                        <span class="badge bg-success fs-6 mt-1">HOẠT ĐỘNG <i class="bi bi-check-circle-fill"></i></span>
                    <?php endif; ?>
                </div>
                <div style="width: 1px; height: 40px; background: #ddd;"></div>
                <div class="text-center">
                    <small class="d-block text-secondary fw-bold text-uppercase">Số gậy cảnh cáo</small>
                    <span class="fs-4 fw-bold <?php echo ($warnings >= 2) ? 'text-danger' : 'text-warning'; ?>">
                        <?= $warnings ?>/3
                    </span>
                </div>
            </div>

            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success mt-3 mb-0"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mt-3 mb-0"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <h5 class="fw-bold mb-3 ms-1 text-secondary"><i class="bi bi-exclamation-triangle-fill me-2"></i>Lịch sử vi phạm</h5>
    
    <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm border-0 violation-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold text-danger mb-1">VI PHẠM TẠI LỚP:</h6>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['class_title']) ?></div>
                                    <small class="text-muted fst-italic"><?= date('H:i d/m/Y', strtotime($row['created_at'])) ?></small>
                                </div>
                                <span class="badge bg-danger">Bị báo cáo</span>
                            </div>

                            <div class="p-3 bg-light rounded mb-3 border">
                                <strong class="small text-secondary text-uppercase">Lý do bị phạt:</strong>
                                <p class="mb-1 fw-bold text-danger"><?= htmlspecialchars($row['reason']) ?></p>
                                <hr class="my-2">
                                <small class="text-dark"><?= nl2br(htmlspecialchars($row['description'])) ?></small>
                            </div>

                            <div class="border-top pt-3 mt-auto">
                                <?php if (!$row['appeal_id']): ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Bạn có bằng chứng minh oan?</small>
                                        <button class="btn btn-outline-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#appealModal<?= $row['id'] ?>">
                                            <i class="bi bi-send-fill me-1"></i> Gửi khiếu nại
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="fw-bold text-secondary">Trạng thái khiếu nại:</small>
                                        <?php if($row['appeal_status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Đang chờ duyệt</span>
                                        <?php elseif($row['appeal_status'] == 'approved'): ?>
                                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Thành công (Đã gỡ gậy)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Bị từ chối</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if($row['appeal_status'] == 'rejected'): ?>
                                        <div class="alert alert-secondary small mb-0 p-2">
                                            <strong>Admin trả lời:</strong> <?= htmlspecialchars($row['appeal_reply']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$row['appeal_id']): ?>
                <div class="modal fade" id="appealModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="handle_appeal.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title"><i class="bi bi-envelope-paper me-2"></i>Gửi đơn khiếu nại</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info small">
                                        <i class="bi bi-info-circle-fill"></i> Hãy cung cấp bằng chứng (ảnh chụp màn hình tin nhắn, log cuộc gọi...) để chứng minh bạn không vi phạm.
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nội dung giải trình:</label>
                                        <textarea name="content" class="form-control" rows="5" required placeholder="Trình bày rõ ràng sự việc..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Ảnh minh chứng:</label>
                                        <input type="file" name="evidence" class="form-control" accept="image/*" required>
                                        <div class="form-text">Bắt buộc phải có hình ảnh chứng minh.</div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" class="btn btn-primary fw-bold">Gửi đơn</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="bi bi-shield-check text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3 fw-bold text-success">Tuyệt vời!</h4>
            <p class="text-muted">Hồ sơ của bạn rất trong sạch. Chưa có vi phạm nào được ghi nhận.</p>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>