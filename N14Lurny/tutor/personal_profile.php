<?php 
session_start();
include '../includes/header_tutor.php'; 
require_once '../config/db.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$msg_type = '';

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['hoten']);
    $phone = trim($_POST['sdt']);
    $address = trim($_POST['diachi']);
    // --- MỚI THÊM: NGÀY SINH ---
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    
    $degree = isset($_POST['trinhdo']) ? trim($_POST['trinhdo']) : null;
    $major = isset($_POST['chuyennganh']) ? trim($_POST['chuyennganh']) : null;
    $experience = isset($_POST['kinhnghiem']) ? trim($_POST['kinhnghiem']) : null;
    $bio = isset($_POST['gioithieu']) ? trim($_POST['gioithieu']) : null;

    // --- XỬ LÝ UPLOAD ẢNH (Giữ nguyên) ---
    $avatar_sql = ""; 
    $upload_ok = true;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "../assets/uploads/avatars/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_ext = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed)) {
            $message = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF).";
            $msg_type = "danger";
            $upload_ok = false;
        } elseif ($_FILES["avatar"]["size"] > 2 * 1024 * 1024) {
            $message = "Ảnh quá lớn (Tối đa 2MB).";
            $msg_type = "danger";
            $upload_ok = false;
        } else {
            $new_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $new_name)) {
                $avatar_sql = ", avatar = '$new_name'";
            } else {
                $message = "Lỗi khi lưu ảnh lên server.";
                $msg_type = "danger";
                $upload_ok = false;
            }
        }
    }

    if (empty($full_name)) {
        $message = "Họ tên không được để trống!";
        $msg_type = "danger";
    } elseif ($upload_ok) {
        // Cập nhật Database (Thêm cột dob)
        $sql = "UPDATE users SET full_name=?, phone=?, address=?, dob=?, degree=?, major=?, experience=?, bio=? $avatar_sql WHERE id=?";
        $stmt = $conn->prepare($sql);
        // Lưu ý chuỗi định dạng: ssssssssi (Thêm 1 chữ s cho dob)
        $stmt->bind_param("ssssssssi", $full_name, $phone, $address, $dob, $degree, $major, $experience, $bio, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['fullname'] = $full_name;
            echo "<script>alert('Cập nhật thành công!'); window.location.href='view_detail.php';</script>";
            exit();
        } else {
            $message = "Lỗi hệ thống: " . $conn->error;
            $msg_type = "danger";
        }
    }
}

// 3. LẤY DỮ LIỆU HIỂN THỊ
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$avatar_url = (!empty($user['avatar']) && file_exists("../assets/uploads/avatars/" . $user['avatar'])) 
              ? "../assets/uploads/avatars/" . $user['avatar'] 
              : "";
$initials = mb_strtoupper(mb_substr($user['full_name'], 0, 1, "UTF-8"));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa hồ sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tutor.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <?php if ($message): ?>
        <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show mb-4" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card p-2 border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    <a href="personal_profile.php" class="list-group-item list-group-item-action active fw-bold border-0 rounded mb-1">
                        <i class="bi bi-person-circle me-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="change_password.php" class="list-group-item list-group-item-action border-0 rounded mb-1">
                        <i class="bi bi-shield-lock me-2"></i> Đổi mật khẩu
                    </a>
                    <?php if ($user['role'] == 'tutor'): ?>
                    <a href="#" class="list-group-item list-group-item-action border-0 rounded">
                        <i class="bi bi-patch-check me-2"></i> Xác minh danh tính
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold m-0 text-dark">Cập nhật hồ sơ</h4>
                <a href="view_detail.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye"></i> Xem trang cá nhân
                </a>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="card p-4 mb-4 border-0 shadow-sm">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-primary">Thông tin cơ bản</h6>

                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 position-relative">
                            <?php if($avatar_url): ?>
                                <img src="<?= $avatar_url ?>" id="preview" class="rounded-circle object-fit-cover border" style="width: 80px; height: 80px;">
                            <?php else: ?>
                                <div id="preview-placeholder" class="avatar-circle m-0" style="width: 80px; height: 80px; font-size: 32px;">
                                    <?= $initials ?>
                                </div>
                                <img id="preview" class="rounded-circle object-fit-cover border d-none" style="width: 80px; height: 80px;">
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="avatarInput" class="btn btn-sm btn-outline-primary mb-1">
                                <i class="bi bi-upload"></i> Tải ảnh mới
                            </label>
                            <input type="file" name="avatar" id="avatarInput" hidden accept="image/*" onchange="previewImage(this)">
                            <div class="small text-muted">JPG, PNG tối đa 2MB</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="diachi" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <?php if ($user['role'] == 'tutor'): ?>
                <div class="card p-4 mb-4 border-0 shadow-sm">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-primary">Hồ sơ Gia sư</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Trình độ</label>
                            <select name="trinhdo" class="form-select">
                                <option value="">-- Chọn --</option>
                                <?php 
                                    $degrees = ['Sinh viên', 'Cao đẳng', 'Đại học', 'Thạc sĩ', 'Giáo viên', 'Giảng viên'];
                                    foreach($degrees as $deg) {
                                        $selected = ($user['degree'] == $deg) ? 'selected' : '';
                                        echo "<option value='$deg' $selected>$deg</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chuyên ngành</label>
                            <input type="text" name="chuyennganh" class="form-control" value="<?= htmlspecialchars($user['major'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kinh nghiệm</label>
                            <input type="text" name="kinhnghiem" class="form-control" value="<?= htmlspecialchars($user['experience'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Giới thiệu bản thân</label>
                            <textarea name="gioithieu" class="form-control" rows="5"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-end gap-3 pb-5">
                    <a href="view_detail.php" class="btn btn-light px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-brand px-4 fw-bold shadow-sm">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('preview').classList.remove('d-none');
            if(document.getElementById('preview-placeholder')) {
                document.getElementById('preview-placeholder').classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>