<?php
session_start();
// Include header
include '../includes/header.php'; 
require_once '../config/db.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// --- XỬ LÝ LƯU THÔNG TIN ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $dob       = $_POST['dob'];
    $gender    = $_POST['gender'];
    $address   = $_POST['address'];
    $school    = $_POST['school'];
    $grade     = $_POST['grade'];
    $phone     = $_POST['phone']; 

    // Xử lý Avatar
    $avatar_sql = "";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "../assets/uploads/avatars/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $ext = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        $new_name = "stu_" . $user_id . "_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $new_name)) {
            $avatar_sql = ", avatar = '$new_name'";
        }
    }

    // Câu lệnh Update
    $sql = "UPDATE users SET full_name=?, phone=?, dob=?, gender=?, address=?, school=?, grade=? $avatar_sql WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $full_name, $phone, $dob, $gender, $address, $school, $grade, $user_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success alert-dismissible fade show'><i class='bi bi-check-circle me-2'></i>Cập nhật hồ sơ thành công!<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
        $_SESSION['fullname'] = $full_name; 
    } else {
        $message = "<div class='alert alert-danger'>Lỗi: " . $conn->error . "</div>";
    }
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Xử lý hiển thị Avatar
$avatar_url = (!empty($student['avatar'])) ? "../assets/uploads/avatars/" . $student['avatar'] : "";
$initial = mb_strtoupper(mb_substr($student['full_name'], 0, 1, "UTF-8"));
?>

<link rel="stylesheet" href="/N14LURNY/assets/css/student.css">

<section class="tutor-dashboard py-5">
  <div class="container" style="max-width: 800px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h4 class="dashboard-title mb-1">Hồ sơ cá nhân</h4>
          <p class="text-muted small mb-0">Quản lý thông tin tài khoản và học tập</p>
      </div>
      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i> Quay lại
      </a>
    </div>

    <?= $message ?>

    <div class="class-card shadow-sm">
      <div class="card-body p-4">
        <form method="post" enctype="multipart/form-data">

            <div class="text-center mb-5">
                <div class="d-inline-block profile-avatar-box mb-2">
                    <?php if($avatar_url): ?>
                        <img src="<?= $avatar_url ?>" class="profile-avatar-img">
                    <?php else: ?>
                        <div class="profile-avatar-circle"><?= $initial ?></div>
                    <?php endif; ?>
                    
                    <label class="btn-upload-icon shadow-sm" title="Đổi ảnh đại diện">
                        <i class="bi bi-camera-fill"></i>
                        <input type="file" name="avatar" hidden accept="image/*" onchange="document.getElementById('saveBtn').click()">
                    </label>
                </div>
                <div class="fw-bold fs-5"><?= htmlspecialchars($student['full_name']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($student['email']) ?></div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-success fw-bold mb-3 border-bottom pb-2"><i class="bi bi-person-vcard me-2"></i>Thông tin cơ bản</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($student['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" value="<?= $student['dob'] ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-secondary">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="Nam" <?= ($student['gender']=='Nam')?'selected':'' ?>>Nam</option>
                                <option value="Nữ" <?= ($student['gender']=='Nữ')?'selected':'' ?>>Nữ</option>
                                <option value="Khác" <?= ($student['gender']=='Khác')?'selected':'' ?>>Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($student['address'] ?? '') ?>" placeholder="Số nhà, đường, quận...">
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-success fw-bold mb-3 border-bottom pb-2"><i class="bi bi-mortarboard me-2"></i>Thông tin học tập</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Trường đang học</label>
                        <input type="text" name="school" class="form-control" value="<?= htmlspecialchars($student['school'] ?? '') ?>" placeholder="VD: THPT Nguyễn Thị Minh Khai">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Khối lớp hiện tại</label>
                        <select name="grade" class="form-select">
                            <option value="">-- Chọn --</option>
                            <?php 
                            $grades = ['Lớp 1','Lớp 2','Lớp 3','Lớp 4','Lớp 5','Lớp 6','Lớp 7','Lớp 8','Lớp 9','Lớp 10','Lớp 11','Lớp 12','Đại học'];
                            foreach($grades as $g) {
                                $sel = ($student['grade'] == $g) ? 'selected' : '';
                                echo "<option value='$g' $sel>$g</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-light border small text-muted mt-4">
                        <i class="bi bi-info-circle me-1"></i> Thông tin chính xác giúp gia sư dễ dàng nắm bắt trình độ và liên hệ với bạn.
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="dashboard.php" class="btn btn-light px-4">Hủy</a>
                <button type="submit" id="saveBtn" class="btn btn-success fw-bold px-4 shadow-sm">
                    <i class="bi bi-save me-1"></i> Lưu thay đổi
                </button>
            </div>

        </form>
      </div>
    </div>

  </div>
</section>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>