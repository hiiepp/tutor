<?php
session_start();
include '../includes/header.php'; 
require_once '../config/db.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin học viên
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Xử lý hiển thị Avatar
$avatar_url = (!empty($student['avatar'])) ? "../assets/uploads/avatars/" . $student['avatar'] : "";
$initial = mb_strtoupper(mb_substr($student['full_name'], 0, 1, "UTF-8"));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa hồ sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/student.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>

<section class="tutor-dashboard py-5">
  <div class="container" style="max-width: 800px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h4 class="dashboard-title mb-1">Hồ sơ cá nhân</h4>
          <p class="text-muted small mb-0">Xem thông tin tài khoản của bạn</p>
      </div>
      <a href="edit_profile.php" class="btn btn-primary btn-sm shadow-sm">
          <i class="bi bi-pencil-square me-1"></i> Chỉnh sửa hồ sơ
      </a>
    </div>

    <div class="class-card shadow-sm">
      <div class="card-body p-4">
        
        <div class="text-center mb-5">
            <div class="d-inline-block profile-avatar-box mb-2">
                <?php if($avatar_url && file_exists($avatar_url)): ?>
                    <img src="<?= $avatar_url ?>" class="profile-avatar-img">
                <?php else: ?>
                    <div class="profile-avatar-circle"><?= $initial ?></div>
                <?php endif; ?>
            </div>
            <div class="fw-bold fs-4"><?= htmlspecialchars($student['full_name']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($student['email']) ?></div>
            <div class="badge bg-success mt-2">HỌC VIÊN</div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="text-success fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-person-vcard me-2"></i>Thông tin cơ bản
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <small class="text-muted d-block">Số điện thoại</small>
                        <span class="fw-medium"><?= htmlspecialchars($student['phone'] ?? 'Chưa cập nhật') ?></span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Ngày sinh</small>
                        <span class="fw-medium">
                            <?= !empty($student['dob']) ? date('d/m/Y', strtotime($student['dob'])) : 'Chưa cập nhật' ?>
                        </span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Giới tính</small>
                        <span class="fw-medium"><?= htmlspecialchars($student['gender'] ?? 'Chưa cập nhật') ?></span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Địa chỉ</small>
                        <span class="fw-medium"><?= htmlspecialchars($student['address'] ?? 'Chưa cập nhật') ?></span>
                    </li>
                </ul>
            </div>

            <div class="col-md-6">
                <h6 class="text-success fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-mortarboard me-2"></i>Thông tin học tập
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <small class="text-muted d-block">Trường học</small>
                        <span class="fw-medium"><?= htmlspecialchars($student['school'] ?? 'Chưa cập nhật') ?></span>
                    </li>
                    <li class="mb-3">
                        <small class="text-muted d-block">Lớp / Khối</small>
                        <span class="fw-medium"><?= htmlspecialchars($student['grade'] ?? 'Chưa cập nhật') ?></span>
                    </li>
                </ul>
                
                <div class="alert alert-light border small text-muted mt-3">
                    <i class="bi bi-info-circle me-1"></i> Cập nhật thông tin đầy đủ giúp gia sư dễ dàng nắm bắt trình độ của bạn.
                </div>
            </div>
        </div>

      </div>
    </div>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>