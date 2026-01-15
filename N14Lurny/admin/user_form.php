<?php
session_start();
require __DIR__ . '/../config/db.php';

// Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

$id = '';
$full_name = '';
$email = '';
$role = 'student'; // Mặc định
$isEdit = false;

// 1. Kiểm tra nếu có ID trên URL => Đang là chế độ SỬA
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $query = $conn->query("SELECT * FROM users WHERE id = $id");
    $user = $query->fetch_assoc();
    
    if ($user) {
        $full_name = $user['full_name'];
        $email = $user['email'];
        $role = $user['role'];
    }
}

// 2. Xử lý khi bấm nút LƯU (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    if ($isEdit) {
        // --- Cập nhật (Update) ---
        if (!empty($password)) {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name='$full_name', email='$email', role='$role', password='$hashed_pass' WHERE id=$id";
        } else {
            $sql = "UPDATE users SET full_name='$full_name', email='$email', role='$role' WHERE id=$id";
        }
    } else {
        // --- Thêm mới (Insert) ---
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$full_name', '$email', '$hashed_pass', '$role')";
    }

    if ($conn->query($sql)) {
        header("Location: users.php");
        exit();
    } else {
        $error = "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Sửa người dùng' : 'Thêm người dùng'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>
    
    <main class="admin-content">
        
        <div class="row justify-content-center mt-4"> 
            <div class="col-md-8 col-lg-6"> <h2 class="mb-4 text-center fw-bold text-uppercase">
                    <?php echo $isEdit ? 'Cập nhật Người dùng' : 'Thêm người dùng mới'; ?>
                </h2>
                
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-4 p-md-5"> <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control form-control-lg" value="<?php echo $full_name; ?>" required placeholder="Nhập họ tên đầy đủ">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email đăng nhập</label>
                                <input type="email" name="email" class="form-control form-control-lg" value="<?php echo $email; ?>" required placeholder="name@example.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Mật khẩu 
                                    <?php if($isEdit): ?>
                                        <small class="text-muted fw-normal">(Để trống nếu không muốn đổi)</small>
                                    <?php endif; ?>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control form-control-lg" 
                                        <?php echo $isEdit ? '' : 'required'; ?> placeholder="********">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Vai trò</label>
                                <select name="role" class="form-select form-select-lg">
                                    <option value="student" <?php if($role=='student') echo 'selected'; ?>>Học viên</option>
                                    <option value="tutor" <?php if($role=='tutor') echo 'selected'; ?>>Gia sư</option>
                                    <option value="admin" <?php if($role=='admin') echo 'selected'; ?>>Admin</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Lưu thông tin
                                </button>
                                <a href="users.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
        </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/admin_main.js"></script>
</body>
</body>
</html>