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

// --- XỬ LÝ XÓA ẢNH MINH CHỨNG (Nếu có yêu cầu xóa) ---
if (isset($_GET['del_proof'])) {
    $proof_id = intval($_GET['del_proof']);
    // Kiểm tra ảnh đó có phải của user này không để tránh xóa bậy
    $check = $conn->query("SELECT image_path FROM tutor_proofs WHERE id=$proof_id AND user_id=$user_id");
    if ($row = $check->fetch_assoc()) {
        $file_path = "../assets/uploads/proofs/" . $row['image_path'];
        if (file_exists($file_path)) unlink($file_path); // Xóa file vật lý
        $conn->query("DELETE FROM tutor_proofs WHERE id=$proof_id"); // Xóa DB
        header("Location: personal_profile.php"); // Load lại trang
        exit();
    }
}

// 2. XỬ LÝ CẬP NHẬT THÔNG TIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['hoten']);
    $phone = trim($_POST['sdt']);
    $address = trim($_POST['diachi']);
    $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
    
    $degree = isset($_POST['trinhdo']) ? trim($_POST['trinhdo']) : null;
    $major = isset($_POST['chuyennganh']) ? trim($_POST['chuyennganh']) : null;
    $experience = isset($_POST['kinhnghiem']) ? trim($_POST['kinhnghiem']) : null;
    $bio = isset($_POST['gioithieu']) ? trim($_POST['gioithieu']) : null;

    // --- XỬ LÝ UPLOAD AVATAR (Giữ nguyên) ---
    $avatar_sql = ""; 
    $upload_ok = true;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $target_dir = "../assets/uploads/avatars/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_ext = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
        $new_name = "user_" . $user_id . "_" . time() . "." . $file_ext;
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_dir . $new_name)) {
            $avatar_sql = ", avatar = '$new_name'";
        }
    }

    // --- MỚI: XỬ LÝ UPLOAD ẢNH MINH CHỨNG (MULTIPLE) ---
    if (isset($_FILES['proofs']) && count($_FILES['proofs']['name']) > 0) {
        $proof_dir = "../assets/uploads/proofs/";
        if (!file_exists($proof_dir)) { mkdir($proof_dir, 0777, true); }
        
        $total_files = count($_FILES['proofs']['name']);
        
        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['proofs']['error'][$i] == 0) {
                $p_ext = strtolower(pathinfo($_FILES['proofs']['name'][$i], PATHINFO_EXTENSION));
                // Đặt tên file unique
                $p_name = "proof_" . $user_id . "_" . time() . "_" . $i . "." . $p_ext;
                
                if (move_uploaded_file($_FILES['proofs']['tmp_name'][$i], $proof_dir . $p_name)) {
                    // Chèn vào bảng tutor_proofs
                    $stmt_proof = $conn->prepare("INSERT INTO tutor_proofs (user_id, image_path) VALUES (?, ?)");
                    $stmt_proof->bind_param("is", $user_id, $p_name);
                    $stmt_proof->execute();
                }
            }
        }
    }

    if (empty($full_name)) {
        $message = "Họ tên không được để trống!";
        $msg_type = "danger";
    } elseif ($upload_ok) {
        $sql = "UPDATE users SET full_name=?, phone=?, address=?, dob=?, degree=?, major=?, experience=?, bio=? $avatar_sql WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $full_name, $phone, $address, $dob, $degree, $major, $experience, $bio, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['fullname'] = $full_name;
            echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location.href='view_detail.php';</script>";
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

// Lấy danh sách ảnh minh chứng
$proofs_res = $conn->query("SELECT * FROM tutor_proofs WHERE user_id = $user_id ORDER BY id DESC");

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
    <style>
        .proof-item { position: relative; width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 1px solid #ddd; }
        .proof-item img { width: 100%; height: 100%; object-fit: cover; }
        .btn-del-proof { position: absolute; top: 2px; right: 2px; background: rgba(255,255,255,0.9); color: red; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; text-decoration: none; }
        .btn-del-proof:hover { background: red; color: white; }
    </style>
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
                        <div class="col-md-6"><label class="form-label">Họ và tên <span class="text-danger">*</span></label><input type="text" name="hoten" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" disabled></div>
                        <div class="col-md-6"><label class="form-label">Số điện thoại</label><input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>"></div>
                        <div class="col-md-6"><label class="form-label">Ngày sinh</label><input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($user['dob'] ?? '') ?>"></div>
                        <div class="col-12"><label class="form-label">Địa chỉ</label><input type="text" name="diachi" class="form-control" value="<?= htmlspecialchars($user['address'] ?? '') ?>"></div>
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
                            <label class="form-label fw-bold">Ảnh bằng cấp / Chứng chỉ / Hoạt động dạy</label>
                            <div class="border rounded p-3 bg-white">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php if($proofs_res->num_rows > 0): ?>
                                        <?php while($img = $proofs_res->fetch_assoc()): ?>
                                            <div class="proof-item shadow-sm">
                                                <img src="../assets/uploads/proofs/<?= $img['image_path'] ?>" alt="Proof">
                                                <a href="?del_proof=<?= $img['id'] ?>" class="btn-del-proof" onclick="return confirm('Xóa ảnh này?')"><i class="bi bi-x"></i></a>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-muted small mb-0 fst-italic">Chưa có ảnh minh chứng nào.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <label class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-images me-1"></i> Chọn ảnh thêm (có thể chọn nhiều)
                                    <input type="file" name="proofs[]" hidden multiple accept="image/*" onchange="showFileCount(this)">
                                </label>
                                <span id="file-count" class="ms-2 small text-muted"></span>
                            </div>
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

function showFileCount(input) {
    const count = input.files.length;
    document.getElementById('file-count').innerText = count > 0 ? `Đã chọn ${count} ảnh` : '';
}
</script>
</body>
</html>