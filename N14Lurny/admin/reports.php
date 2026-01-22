<?php
session_start();
require __DIR__ . '/../config/db.php';

// Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

$message = '';

// =================================================================================
// XỬ LÝ FORM SUBMIT
// =================================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- XỬ LÝ BÁO CÁO (TAB 1) ---
    if (isset($_POST['action_report'])) {
        $report_id = intval($_POST['report_id']);
        $decision = $_POST['decision']; 
        $admin_reply = trim($_POST['admin_reply']);
        
        $r_sql = "SELECT r.*, s.id as s_id, t.id as t_id, t.warnings_count, c.title as class_title, c.id as class_id
                  FROM reports r 
                  JOIN users s ON r.student_id = s.id 
                  JOIN users t ON r.tutor_id = t.id 
                  JOIN classes c ON r.class_id = c.id 
                  WHERE r.id = $report_id";
        $r_data = $conn->query($r_sql)->fetch_assoc();

        // Link thông báo
        $link_class = "class-detail.php?id=" . $r_data['class_id'];
        $link_tutor = "tutor/violations.php"; 

        if ($decision == 'approve') {
            $new_warning = $r_data['warnings_count'] + 1;
            $is_banned = ($new_warning >= 3) ? 1 : 0;

            $conn->query("UPDATE reports SET status='approved' WHERE id=$report_id");
            $conn->query("UPDATE users SET warnings_count=$new_warning, is_banned=$is_banned WHERE id=".$r_data['tutor_id']);

            // Thông báo GIA SƯ
            $warn_msg = "Bạn bị báo cáo tại lớp: <strong>".$r_data['class_title']."</strong>.<br>";
            $warn_msg .= "Lý do: ".$r_data['reason'].".<br>Số lần cảnh cáo: <strong class='text-danger'>$new_warning/3</strong>.";
            if($is_banned) $warn_msg .= "<br><strong>TÀI KHOẢN ĐÃ BỊ KHÓA VĨNH VIỄN.</strong>";
            
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$r_data['tutor_id'].", 'CẢNH BÁO VI PHẠM ⚠️', '$warn_msg', '$link_tutor', NOW())");

            // Thông báo HỌC VIÊN
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$r_data['student_id'].", 'Báo cáo thành công ✅', 'Báo cáo của bạn đã được xử lý. Nhấn để xem chi tiết.', '$link_class', NOW())");

            $message = "Đã chấp thuận báo cáo. Gia sư đã bị cảnh cáo.";

        } else {
            if(empty($admin_reply)) $admin_reply = "Không đủ bằng chứng.";
            
            $stmt = $conn->prepare("UPDATE reports SET status='rejected', admin_reply=? WHERE id=?");
            $stmt->bind_param("si", $admin_reply, $report_id);
            $stmt->execute();

            $msg_student = "Báo cáo bị từ chối.<br>Nhấn vào đây để xem lý do chi tiết từ Admin.";
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$r_data['student_id'].", 'Báo cáo bị từ chối ❌', '$msg_student', '$link_class', NOW())");

            $message = "Đã từ chối báo cáo.";
        }
    }

    // --- XỬ LÝ KHIẾU NẠI (TAB 2) ---
    if (isset($_POST['action_appeal'])) {
        $appeal_id = intval($_POST['appeal_id']);
        $decision = $_POST['decision'];
        $reply = trim($_POST['reply']);

        $a_sql = "SELECT a.*, r.student_id, t.warnings_count, r.class_id 
                  FROM appeals a 
                  JOIN reports r ON a.report_id = r.id 
                  JOIN users t ON a.tutor_id = t.id 
                  WHERE a.id = $appeal_id";
        $a_data = $conn->query($a_sql)->fetch_assoc();
        
        $link_class = "class-detail.php?id=" . $a_data['class_id'];
        $link_tutor = "tutor/violations.php";

        if ($decision == 'approve') {
            $conn->query("UPDATE appeals SET status='approved' WHERE id=$appeal_id");
            if ($a_data['warnings_count'] > 0) {
                $new_count = $a_data['warnings_count'] - 1;
                $unban = ($new_count < 3) ? ", is_banned=0" : "";
                $conn->query("UPDATE users SET warnings_count=$new_count $unban WHERE id=".$a_data['tutor_id']);
            }
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$a_data['tutor_id'].", 'Khiếu nại thành công ✅', 'Cảnh cáo đã được gỡ bỏ.', '$link_tutor', NOW())");
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$a_data['student_id'].", 'Cập nhật báo cáo ℹ️', 'Báo cáo của bạn bị hủy do gia sư khiếu nại thành công.', '$link_class', NOW())");
            $message = "Đã chấp thuận khiếu nại.";
        } else {
            if(empty($reply)) $reply = "Bằng chứng không đủ.";
            $stmt = $conn->prepare("UPDATE appeals SET status='rejected', admin_reply=? WHERE id=?");
            $stmt->bind_param("si", $reply, $appeal_id);
            $stmt->execute();
            
            $msg_tutor = "Khiếu nại thất bại.<br>Lý do: " . htmlspecialchars($reply);
            $conn->query("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (".$a_data['tutor_id'].", 'Khiếu nại thất bại ❌', '$msg_tutor', '$link_tutor', NOW())");
            $message = "Đã từ chối khiếu nại.";
        }
    }
}

// --- LOGIC ĐẾM SỐ LƯỢNG CHỜ XỬ LÝ (MỚI) ---
$count_p_reports = $conn->query("SELECT COUNT(*) as total FROM reports WHERE status='pending'")->fetch_assoc()['total'];
$count_p_appeals = $conn->query("SELECT COUNT(*) as total FROM appeals WHERE status='pending'")->fetch_assoc()['total'];

// LẤY DỮ LIỆU HIỂN THỊ
$sql_reports = "SELECT r.*, s.full_name as s_name, t.full_name as t_name, c.title as c_title 
                FROM reports r 
                JOIN users s ON r.student_id = s.id 
                JOIN users t ON r.tutor_id = t.id 
                JOIN classes c ON r.class_id = c.id 
                ORDER BY r.created_at DESC";
$res_reports = $conn->query($sql_reports);

$sql_appeals = "SELECT a.*, t.full_name as t_name, r.reason as report_reason 
                FROM appeals a 
                JOIN users t ON a.tutor_id = t.id 
                JOIN reports r ON a.report_id = r.id 
                ORDER BY a.created_at DESC";
$res_appeals = $conn->query($sql_appeals);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Báo cáo & Khiếu nại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <h2 class="mb-4 text-dark border-bottom pb-2">Trung tâm Xử lý Vi phạm</h2>

        <?php if($message): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm">
                <i class="fas fa-check-circle me-2"></i> <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" id="adminTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button">
                            <i class="fas fa-flag text-danger me-2"></i>Báo cáo vi phạm
                            <?php if($count_p_reports > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-2"><?= $count_p_reports ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold" id="appeals-tab" data-bs-toggle="tab" data-bs-target="#appeals" type="button">
                            <i class="fas fa-gavel text-warning me-2"></i>Đơn khiếu nại
                            <?php if($count_p_appeals > 0): ?>
                                <span class="badge bg-danger rounded-pill ms-2"><?= $count_p_appeals ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-0">
                <div class="tab-content" id="adminTabContent">
                    
                    <div class="tab-pane fade show active" id="reports" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Học viên báo</th>
                                        <th>Gia sư bị báo</th>
                                        <th>Lý do</th>
                                        <th>Chi tiết</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($r = $res_reports->fetch_assoc()): ?>
                                    <tr class="<?= ($r['status'] == 'pending') ? 'table-warning' : '' ?>">
                                        <td>#<?= $r['id'] ?></td>
                                        <td>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($r['s_name']) ?></div>
                                            <small class="text-muted">Lớp: <?= htmlspecialchars($r['c_title']) ?></small>
                                        </td>
                                        <td class="text-danger fw-bold"><?= htmlspecialchars($r['t_name']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($r['reason']) ?></span></td>
                                        <td class="description-cell">
                                            <?= mb_substr(htmlspecialchars($r['description']), 0, 50) ?>...
                                            <?php if(!empty($r['image_proof'])): ?>
                                                <div class="text-success small mt-1"><i class="fas fa-image"></i> Có ảnh</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($r['status']=='pending'): ?><span class="badge bg-warning text-dark">Chờ xử lý</span>
                                            <?php elseif($r['status']=='approved'): ?><span class="badge bg-success">Đã chấp thuận</span>
                                            <?php else: ?><span class="badge bg-secondary">Đã từ chối</span><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($r['status']=='pending'): ?>
                                                <button class="btn btn-primary btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#modalReport<?= $r['id'] ?>" title="Xử lý">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-light btn-sm btn-action border" disabled><i class="fas fa-check"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalReport<?= $r['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Xử lý Báo cáo #<?= $r['id'] ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 border-end">
                                                                <div class="mb-3">
                                                                    <label class="small text-muted fw-bold">Người báo cáo:</label>
                                                                    <div><?= htmlspecialchars($r['s_name']) ?></div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="small text-muted fw-bold">Gia sư bị báo:</label>
                                                                    <div class="text-danger fw-bold"><?= htmlspecialchars($r['t_name']) ?></div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="small text-muted fw-bold">Chi tiết sự việc:</label>
                                                                    <div class="p-2 bg-light rounded fst-italic">
                                                                        <?= nl2br(htmlspecialchars($r['description'])) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="small text-muted fw-bold mb-2">Hình ảnh minh chứng:</label>
                                                                <?php if (!empty($r['image_proof'])): ?>
                                                                    <div class="text-center border rounded p-2 bg-light">
                                                                        <img src="../assets/uploads/reports/<?= $r['image_proof'] ?>" class="img-fluid rounded mb-2" style="max-height: 250px; cursor: pointer" onclick="window.open(this.src)">
                                                                        <a href="../assets/uploads/reports/<?= $r['image_proof'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                            <i class="fas fa-expand me-1"></i> Xem ảnh lớn
                                                                        </a>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="text-center py-5 bg-light rounded border text-muted">
                                                                        <i class="fas fa-image fa-2x mb-2 opacity-25"></i>
                                                                        <p class="mb-0 small">Học viên không gửi kèm ảnh.</p>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Quyết định của Admin:</label>
                                                            <select name="decision" class="form-select" onchange="toggleReason(this, <?= $r['id'] ?>)">
                                                                <option value="approve">✅ Chấp thuận (Cảnh cáo Gia sư)</option>
                                                                <option value="reject">❌ Từ chối (Báo lại Học viên)</option>
                                                            </select>
                                                        </div>
                                                        <div id="rejectReason<?= $r['id'] ?>" class="d-none animate-fade">
                                                            <label class="form-label text-danger">Lý do từ chối (Gửi cho HS):</label>
                                                            <textarea name="admin_reply" class="form-control" rows="3" placeholder="Nhập lý do bác bỏ báo cáo..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" name="action_report" class="btn btn-danger">Xác nhận xử lý</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="appeals" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Gia sư</th>
                                        <th>Về báo cáo</th>
                                        <th>Lần thứ</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($a = $res_appeals->fetch_assoc()): ?>
                                    <tr class="<?= ($a['status'] == 'pending') ? 'table-warning' : '' ?>">
                                        <td>#A<?= $a['id'] ?></td>
                                        <td class="fw-bold text-primary"><?= htmlspecialchars($a['t_name']) ?></td>
                                        <td><?= htmlspecialchars($a['report_reason']) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= $a['attempt_number'] ?>/2</span></td>
                                        <td>
                                            <?php if($a['status']=='pending'): ?><span class="badge bg-warning text-dark">Chờ duyệt</span>
                                            <?php elseif($a['status']=='approved'): ?><span class="badge bg-success">Thành công</span>
                                            <?php else: ?><span class="badge bg-danger">Thất bại</span><?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($a['status']=='pending'): ?>
                                                <button class="btn btn-primary btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#modalAppeal<?= $a['id'] ?>" title="Xem & Xử lý">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-light btn-sm btn-action border" disabled><i class="fas fa-check"></i></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="modalAppeal<?= $a['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <input type="hidden" name="appeal_id" value="<?= $a['id'] ?>">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title"><i class="fas fa-balance-scale me-2"></i>Xử lý Khiếu nại #A<?= $a['id'] ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 border-end">
                                                                <h6 class="text-secondary border-bottom pb-2">Nội dung giải trình:</h6>
                                                                <div class="p-3 bg-light rounded text-dark">
                                                                    <?= nl2br(htmlspecialchars($a['content'])) ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6 class="text-secondary border-bottom pb-2">Hình ảnh minh chứng:</h6>
                                                                <div class="evidence-box">
                                                                    <?php if(!empty($a['evidence_image'])): ?>
                                                                        <img src="../assets/uploads/appeals/<?= $a['evidence_image'] ?>" class="evidence-img shadow-sm" onclick="window.open(this.src)">
                                                                        <div class="text-center mt-2">
                                                                            <a href="../assets/uploads/appeals/<?= $a['evidence_image'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-expand me-1"></i> Xem ảnh gốc</a>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="py-5 text-muted fst-italic">Không có ảnh minh chứng</div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Quyết định của Admin:</label>
                                                            <select name="decision" class="form-select" onchange="toggleReply(this, <?= $a['id'] ?>)">
                                                                <option value="approve">✅ Chấp thuận (Gỡ cảnh cáo)</option>
                                                                <option value="reject">❌ Từ chối (Giữ nguyên phạt)</option>
                                                            </select>
                                                        </div>
                                                        <div id="rejectReply<?= $a['id'] ?>" class="d-none animate-fade">
                                                            <label class="form-label text-danger">Lý do từ chối (Gửi cho Gia sư):</label>
                                                            <textarea name="reply" class="form-control" rows="3" placeholder="Giải thích tại sao bằng chứng không đủ..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" name="action_appeal" class="btn btn-info text-white">Lưu quyết định</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleReason(select, id) {
    const div = document.getElementById('rejectReason' + id);
    if (select.value === 'reject') {
        div.classList.remove('d-none');
        div.querySelector('textarea').required = true;
    } else {
        div.classList.add('d-none');
        div.querySelector('textarea').required = false;
    }
}

function toggleReply(select, id) {
    const div = document.getElementById('rejectReply' + id);
    if (select.value === 'reject') {
        div.classList.remove('d-none');
        div.querySelector('textarea').required = true;
    } else {
        div.classList.add('d-none');
        div.querySelector('textarea').required = false;
    }
}
</script>
</body>
</html>