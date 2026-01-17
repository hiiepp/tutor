<?php 
include '../includes/header_tutor.php'; 
require_once '../config/db.php';

// 1. THIẾT LẬP MÚI GIỜ VIỆT NAM
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

$tutor_id = $_SESSION['user_id'];
$today = date('Y-m-d'); 

// Lấy danh sách lớp
$sql = "SELECT c.*, 
       (SELECT COUNT(*) FROM class_registrations r WHERE r.class_id = c.id AND r.status = 'accepted') as accepted_count
       FROM classes c 
       WHERE c.tutor_id = ? 
       ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tutor_id);
$stmt->execute();
$result = $stmt->get_result();

// --- 2. PHÂN LOẠI LỚP ---
$list_pending = [];   
$list_upcoming = [];  
$list_ongoing = [];   
$list_hidden = [];    
$list_rejected = [];  

while ($row = $result->fetch_assoc()) {
    $has_students = ($row['accepted_count'] > 0);
    $is_started = (!empty($row['start_date']) && $row['start_date'] <= $today);

    // --- TỰ ĐỘNG KHÓA LỚP QUÁ HẠN KHÔNG CÓ HỌC VIÊN ---
    // Nếu: Đang mở (active) VÀ Đã đến ngày (is_started) VÀ Không có học viên (!has_students)
    if ($row['status'] == 'active' && $is_started && !$has_students) {
        // 1. Cập nhật Database thành 'closed'
        $update_id = $row['id'];
        $conn->query("UPDATE classes SET status = 'closed' WHERE id = $update_id");
        
        // 2. Cập nhật biến $row để xếp loại đúng vào tab Đang ẩn ngay bây giờ
        $row['status'] = 'closed';
    }
    // ----------------------------------------------------

    if ($row['status'] == 'pending') {
        $list_pending[] = $row;
    } 
    elseif ($row['status'] == 'rejected') {
        $list_rejected[] = $row;
    }
    elseif ($row['status'] == 'hidden' || $row['status'] == 'closed') {
        if ($has_students) {
            // Nếu đã có học viên thì vẫn hiện ở tab Dạy
            if ($is_started) $list_ongoing[] = $row;
            else $list_upcoming[] = $row;
        } else {
            // Nếu không có học viên -> Vào tab Đang ẩn
            $list_hidden[] = $row;
        }
    } 
    elseif ($row['status'] == 'active') {
        if ($has_students && $is_started) $list_ongoing[] = $row;
        else $list_upcoming[] = $row;
    }
}

// --- HÀM RENDER GIAO DIỆN ---
function renderClassCard($row, $type) {
    global $today;
    
    $price_display = is_numeric($row['price']) ? number_format($row['price'], 0, ',', '.') . ' đ' : $row['price'];
    $date_range = (!empty($row['start_date']) && !empty($row['end_date'])) 
                  ? date('d/m/Y', strtotime($row['start_date'])) . " - " . date('d/m/Y', strtotime($row['end_date'])) 
                  : "Chưa cập nhật";

    $max = $row['max_students'] ?? 1;
    $current = $row['accepted_count'];
    $is_full = ($current >= $max);
    $is_started = (!empty($row['start_date']) && $row['start_date'] <= $today);
    $db_status = $row['status'];
    $has_students = ($current > 0);

    // Logic Xóa: Khóa nút xóa nếu có học viên
    $can_delete = !$has_students;

    $status_label = '';
    $border_class = 'border-0';
    $opacity = '';

    if ($type == 'pending') {
        $status_label = '<span class="badge bg-warning text-dark">⏳ Chờ Admin duyệt</span>';
    } elseif ($type == 'rejected') {
        $status_label = '<span class="badge bg-danger">🚫 Bị từ chối</span>';
        $opacity = 'opacity-75';
    } elseif ($type == 'hidden') {
        // Thêm label giải thích lý do ẩn
        $reason = ($db_status == 'closed' && !$has_students) ? "(Quá hạn tuyển)" : "(Chưa có HV)";
        $status_label = '<span class="badge bg-secondary"><i class="bi bi-lock-fill"></i> Đã khóa ' . $reason . '</span>';
        $opacity = 'opacity-75 bg-light';
    } else {
        if ($db_status == 'hidden' || $db_status == 'closed') {
            $status_label = '<span class="badge bg-secondary"><i class="bi bi-lock-fill"></i> Đã khóa sổ</span>';
        } else {
            $status_label = '<span class="badge bg-primary"><i class="bi bi-megaphone"></i> Đang tuyển sinh</span>';
        }
        
        if($type == 'ongoing') $border_class = 'border-success border-start border-4';
        else $border_class = 'border-primary border-start border-4';
    }

    if ($is_full && $type != 'rejected' && $db_status != 'hidden') {
        $status_label .= ' <span class="badge bg-danger ms-1">Đã đủ HV</span>';
    }
    ?>
    
    <div class="col-12 mb-4">
        <div class="card shadow-sm <?php echo $border_class . ' ' . $opacity; ?>">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    
                    <div class="col-md-8">
                        <div class="mb-2">
                            <span class="badge blue"><?= htmlspecialchars($row['subject']) ?></span>
                            <span class="badge gray"><?= htmlspecialchars($row['method']) ?></span>
                            <?= $status_label ?>
                        </div>
                        
                        <h5 class="fw-bold mb-2">
                            <a href="see_details.php?id=<?= $row['id'] ?>" class="text-dark text-decoration-none hover-brand">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h5>

                        <div class="d-flex flex-wrap gap-4 text-muted small mt-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-people-fill me-2 text-info"></i> 
                                <span>Học viên: <strong><?= $current ?>/<?= $max ?></strong></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-primary"></i> 
                                <span class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($row['location']) ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-range me-2 text-danger"></i> <?= $date_range ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 border-start-md ps-md-4 mt-3 mt-md-0">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            
                            <a href="see_details.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm px-3" title="Xem chi tiết">
                                <i class="bi bi-eye"></i>
                            </a>
                            
                            <?php if ($is_started || $is_full || $type == 'rejected'): ?>
                                <button class="btn btn-light btn-sm px-3 border disabled" title="Không thể sửa">
                                    <i class="bi bi-pencil text-muted"></i>
                                </button>
                            <?php else: ?>
                                <a href="edit_class.php?id=<?= $row['id'] ?>" class="btn btn-light btn-sm px-3 border" title="Sửa thông tin">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($db_status == 'active'): ?>
                                <a href="update_status.php?id=<?= $row['id'] ?>&action=close" 
                                   class="btn btn-warning btn-sm px-3 text-dark border" 
                                   onclick="return confirm('Khóa lớp này?')" 
                                   title="Khóa lớp">
                                    <i class="bi bi-lock-fill"></i>
                                </a>
                            <?php elseif ($db_status == 'hidden' || $db_status == 'closed'): ?>
                                <?php if ($is_full): ?>
                                    <button class="btn btn-secondary btn-sm px-3 border" disabled title="Lớp đã đủ học viên">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                <?php else: ?>
                                    <a href="update_status.php?id=<?= $row['id'] ?>&action=open" 
                                       class="btn btn-success btn-sm px-3 border" 
                                       title="Mở lại lớp">
                                        <i class="bi bi-unlock-fill"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($can_delete): ?>
                                <a href="delete_class.php?id=<?= $row['id'] ?>" class="btn btn-light btn-sm px-3 text-danger border hover-danger" onclick="return confirm('CẢNH BÁO: Xóa vĩnh viễn?')" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-light btn-sm px-3 border disabled text-muted" title="Lớp đã có học viên, không thể xóa!">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
    <style>
        .nav-tabs .nav-link { color: #6c757d; font-weight: 600; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; background: none; }
        .nav-tabs .nav-link:hover { color: #198754; }
        .empty-state { padding: 40px 0; text-align: center; color: #aaa; }
        .btn-warning { background-color: #ffc107; border-color: #ffc107; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark">Quản lý lớp học</h3>
            <p class="text-muted mb-0">Theo dõi trạng thái các lớp học của bạn</p>
        </div>
        <a href="new_class.php" class="btn btn-brand px-4 py-2 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Đăng lớp mới
        </a>
    </div>

    <ul class="nav nav-tabs mb-4" id="classTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                Chờ dạy (<?= count($list_upcoming) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button">
                Đang dạy (<?= count($list_ongoing) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="hidden-tab" data-bs-toggle="tab" data-bs-target="#hidden" type="button">
                Đang ẩn (<?= count($list_hidden) ?>)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                Chờ duyệt (<?= count($list_pending) + count($list_rejected) ?>)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="classTabsContent">
        
        <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
            <?php if(count($list_upcoming) > 0): ?>
                <div class="row">
                    <?php foreach($list_upcoming as $class) renderClassCard($class, 'upcoming'); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-megaphone fs-1 mb-2"></i>
                    <p>Không có lớp nào đang tuyển sinh.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="ongoing" role="tabpanel">
            <?php if(count($list_ongoing) > 0): ?>
                <div class="row">
                    <?php foreach($list_ongoing as $class) renderClassCard($class, 'ongoing'); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-mortarboard fs-1 mb-2"></i>
                    <p>Chưa có lớp nào đang diễn ra.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="hidden" role="tabpanel">
            <?php if(count($list_hidden) > 0): ?>
                <div class="row">
                    <?php foreach($list_hidden as $class) renderClassCard($class, 'hidden'); ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-eye-slash fs-1 mb-2"></i>
                    <p>Không có lớp nháp/ẩn nào.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="pending" role="tabpanel">
            <?php if(count($list_pending) > 0 || count($list_rejected) > 0): ?>
                <div class="row">
                    <?php 
                        foreach($list_pending as $class) renderClassCard($class, 'pending'); 
                        foreach($list_rejected as $class) renderClassCard($class, 'rejected'); 
                    ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-hourglass-split fs-1 mb-2"></i>
                    <p>Không có lớp nào đang chờ duyệt.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>