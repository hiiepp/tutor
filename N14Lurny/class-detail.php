<?php
session_start();
require 'config/db.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: find-class.php");
    exit();
}
$class_id = intval($_GET['id']);

// 1. CẬP NHẬT QUERY ĐỂ LẤY THÊM THÔNG TIN GIA SƯ
$sql = "SELECT c.*, 
               u.full_name as tutor_name, 
               u.email as tutor_email, 
               u.phone as tutor_phone,
               u.avatar as tutor_avatar,
               u.degree, u.major, u.experience, u.address as tutor_address
        FROM classes c 
        LEFT JOIN users u ON c.tutor_id = u.id 
        WHERE c.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('<div class="container py-5 text-center"><h3>Lớp học không tồn tại!</h3><a href="find-class.php" class="btn btn-primary">Quay lại</a></div>');
}
$row = $result->fetch_assoc();

// Kiểm tra trạng thái đăng ký của học viên hiện tại
$registration_status = null;
if (isset($_SESSION['user_id'])) {
    $stu_id = $_SESSION['user_id'];
    $check_reg = $conn->prepare("SELECT status FROM class_registrations WHERE class_id = ? AND student_id = ?");
    $check_reg->bind_param("ii", $class_id, $stu_id);
    $check_reg->execute();
    $res_reg = $check_reg->get_result();
    if ($res_reg->num_rows > 0) {
        $registration_status = $res_reg->fetch_assoc()['status']; 
    }
}

$badge_class = ($row['status'] == 'active') ? 'badge green' : 'badge red';
$status_text = ($row['status'] == 'active') ? 'Đang tuyển sinh' : 'Đã đóng';

// Xử lý giá tiền
$price_raw = $row['price'];
$price_display = '';
$unit_display = '';

if (is_numeric($price_raw)) {
    $price_display = number_format($price_raw, 0, ',', '.') . ' đ';
    $unit_display = '/ buổi';
} elseif (preg_match('/^(\d+)\s+(.*)$/', $price_raw, $matches)) {
    $price_display = number_format($matches[1], 0, ',', '.') . ' đ';
    $unit_clean = trim(str_replace('VND', '', $matches[2]));
    $unit_display = (strpos($unit_clean, '/') === 0) ? $unit_clean : '/ ' . $unit_clean;
} else {
    $price_display = $price_raw;
    $unit_display = '';
}

// 2. XỬ LÝ AVATAR GIA SƯ
$tutorName = $row['tutor_name'] ?? 'Gia sư';
$initials = mb_strtoupper(mb_substr($tutorName, 0, 1, "UTF-8"));
$avatar_url = "";
if (!empty($row['tutor_avatar']) && file_exists("assets/uploads/avatars/" . $row['tutor_avatar'])) {
    $avatar_url = "assets/uploads/avatars/" . $row['tutor_avatar'];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($row['title']); ?> - Chi tiết lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/N14LURNY/assets/css/tutor.css">
    <style>
        .register-box { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .alert-floating { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
        /* CSS cho Avatar */
        .tutor-avatar-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e9ecef; margin: 0 auto; display: block; }
    </style>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-floating shadow border-0" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-floating shadow border-0" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="javascript:history.back()" class="back-link text-decoration-none">
            <i class="bi bi-arrow-left"></i> Quay lại tìm lớp
        </a>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card card-detail">
                <div class="mb-3">
                    <span class="badge green"><?= htmlspecialchars($row['subject']) ?></span>
                    <span class="badge gray"><i class="bi bi-laptop"></i> <?= htmlspecialchars($row['method']) ?></span>
                    <span class="<?= $badge_class ?>"><?= $status_text ?></span>
                </div>

                <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($row['title']) ?></h2>

                <div class="d-flex flex-wrap gap-4 text-secondary mb-4">
                   <div class="d-flex align-items-center">
                       <i class="bi bi-mortarboard me-2 text-brand"></i> 
                       <?= htmlspecialchars($row['grade']) ?>
                   </div>
                   <div class="d-flex align-items-center">
                       <?php if($row['method'] == 'Online'): ?>
                           <i class="bi bi-camera-video me-2 text-brand"></i> Online
                       <?php else: ?>
                           <i class="bi bi-geo-alt me-2 text-brand"></i> 
                           <?= htmlspecialchars($row['location']) ?>
                       <?php endif; ?>
                   </div>
                   <div class="d-flex align-items-center">
                       <i class="bi bi-clock-history me-2 text-brand"></i> 
                       <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                   </div>
                </div>

                <hr class="text-muted opacity-25">

                <div class="d-flex align-items-end">
                    <h3 class="text-primary fw-bold mb-0 me-2"><?= $price_display ?></h3>
                    <?php if(!empty($unit_display)): ?>
                        <span class="text-muted pb-1"><?= htmlspecialchars($unit_display) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-detail">
                <h5 class="section-title"><i class="bi bi-file-text"></i> Mô tả chi tiết</h5>
                <div class="text-dark" style="line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($row['description'] ?? 'Chưa có mô tả chi tiết.')) ?>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded border border-light">
                    <strong><i class="bi bi-calendar-week me-2 text-brand"></i>Lịch học dự kiến:</strong> 
                    <span class="ms-1">Thỏa thuận trực tiếp với gia sư sau khi nhận lớp.</span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            
            <div class="card card-detail text-center">
                <h5 class="fw-bold mb-4">Thông tin Gia sư</h5>
                
                <a href="student/tutor_profile.php?id=<?= $row['tutor_id'] ?>" class="text-decoration-none" title="Xem hồ sơ chi tiết">
                    <?php if ($avatar_url): ?>
                        <img src="<?= $avatar_url ?>" class="tutor-avatar-img mb-3 shadow-sm hover-scale">
                    <?php else: ?>
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm hover-scale" style="width: 100px; height: 100px; font-size: 40px; font-weight: bold;">
                            <?= $initials ?>
                        </div>
                    <?php endif; ?>
                </a>
                
                <h6 class="fw-bold fs-5 mb-1">
                    <a href="student/tutor_profile.php?id=<?= $row['tutor_id'] ?>" class="text-dark text-decoration-none hover-brand">
                        <?= htmlspecialchars($tutorName) ?>
                    </a>
                </h6>
                
                <div class="small text-muted mb-3">
                    <a href="student/tutor_profile.php?id=<?= $row['tutor_id'] ?>" class="text-secondary text-decoration-none">
                        <i class="bi bi-eye"></i> Xem hồ sơ đầy đủ
                    </a>
                </div>

                <div class="text-warning mb-3">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>

                <div class="text-start bg-light p-3 rounded mb-3 small">
                    <div class="mb-2">
                        <i class="bi bi-mortarboard-fill text-muted me-2"></i>
                        <strong>Trình độ:</strong> <?= htmlspecialchars($row['degree'] ?? 'Chưa cập nhật') ?>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-book-half text-muted me-2"></i>
                        <strong>Chuyên ngành:</strong> <?= htmlspecialchars($row['major'] ?? 'Chưa cập nhật') ?>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-briefcase-fill text-muted me-2"></i>
                        <strong>Kinh nghiệm:</strong> <?= htmlspecialchars($row['experience'] ?? 'Chưa cập nhật') ?>
                    </div>
                    <div>
                        <i class="bi bi-geo-alt-fill text-muted me-2"></i>
                        <strong>Khu vực:</strong> <?= htmlspecialchars($row['tutor_address'] ?? 'Chưa cập nhật') ?>
                    </div>
                </div>
                
                <?php if ($registration_status == 'accepted'): ?>
                    <div class="d-grid gap-2 border-top pt-3">
                        <div class="alert alert-success py-2 small mb-2 fw-bold">
                            <i class="bi bi-check-circle"></i> Bạn đã vào lớp này
                        </div>
                        <a href="mailto:<?= htmlspecialchars($row['tutor_email']) ?>" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-envelope"></i> <?= htmlspecialchars($row['tutor_email']) ?>
                        </a>
                        <a href="tel:<?= htmlspecialchars($row['tutor_phone']) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-telephone"></i> <?= htmlspecialchars($row['tutor_phone'] ?? 'Chưa cập nhật SĐT') ?>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="small text-muted fst-italic border-top pt-3">
                        <i class="bi bi-lock-fill"></i> Số điện thoại và Email sẽ hiển thị sau khi yêu cầu đăng ký của bạn được chấp nhận.
                    </p>
                <?php endif; ?>
            </div>

            <div class="card card-detail p-4 register-box border-success border-top border-4">
                <h5 class="fw-bold mb-3">Đăng ký học</h5>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <p class="text-muted small mb-3">Bạn cần đăng nhập để đăng ký lớp học này.</p>
                    <a href="auth/login_register.php" class="btn btn-brand w-100">Đăng nhập ngay</a>

                <?php elseif ($row['status'] != 'active'): ?>
                    <div class="alert alert-secondary text-center m-0">
                        <i class="bi bi-lock-fill"></i> Lớp này đã đóng
                    </div>

                <?php elseif ($_SESSION['role'] == 'tutor' || $_SESSION['role'] == 'admin'): ?>
                     <div class="alert alert-warning text-center small m-0">
                        Bạn đang đăng nhập với tư cách Gia sư/Admin nên không thể đăng ký học.
                     </div>

                <?php else: ?>
                    <?php if ($registration_status == 'pending'): ?>
                        <div class="alert alert-info text-center m-0">
                            <i class="bi bi-hourglass-split"></i> Đã gửi yêu cầu. <br>Vui lòng chờ gia sư duyệt.
                        </div>
                    <?php elseif ($registration_status == 'accepted'): ?>
                        <div class="alert alert-success text-center m-0">
                            <i class="bi bi-check-circle-fill"></i> Yêu cầu đã được chấp nhận!
                        </div>
                    <?php elseif ($registration_status == 'rejected'): ?>
                        <div class="alert alert-danger text-center m-0">
                            <i class="bi bi-x-circle-fill"></i> Yêu cầu đã bị từ chối.
                        </div>
                    <?php else: ?>
                        <?php 
                            $check_again = $conn->query("SELECT id FROM class_registrations WHERE class_id=$class_id AND student_id=" . $_SESSION['user_id']);
                            if($check_again->num_rows == 0):
                        ?>
                        <form action="/N14LURNY/student/handle_register.php" method="POST" onsubmit="return confirm('Gửi đăng ký?');">
                            <input type="hidden" name="class_id" value="<?= $row['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Lời nhắn cho gia sư (Tùy chọn):</label>
                                <textarea name="message" class="form-control form-control-sm" rows="3" placeholder="Ví dụ: Em cần bổ trợ kiến thức hình học..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-2 fw-bold text-uppercase shadow-sm">
                                <i class="bi bi-send-fill me-2"></i> Gửi yêu cầu ngay
                            </button>
                        </form>
                        <?php else: ?>
                             <div class="alert alert-info text-center m-0">Bạn đã đăng ký lớp này.</div>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

            <div class="alert alert-warning border-warning shadow-sm mt-3">
                <div class="d-flex">
                    <i class="bi bi-shield-exclamation fs-4 me-3"></i>
                    <div>
                        <strong class="d-block mb-1 text-dark">Lưu ý an toàn</strong>
                        <small class="text-muted">Tuyệt đối không chuyển khoản đặt cọc trước khi gặp mặt gia sư.</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>