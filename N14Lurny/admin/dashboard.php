<?php
session_start();

require __DIR__ . '/../config/db.php'; 

// Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Sửa lại đường dẫn login cho đúng với cấu trúc thư mục của bạn
    header("Location: ../auth/login_register.php"); exit();
}

$activePage = 'dashboard';

// Lấy số liệu thống kê từ Database
// 1. Đếm học viên
$countStudent = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'];
// 2. Đếm gia sư
$countTutor = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='tutor'")->fetch_assoc()['total'];
// 3. Đếm lớp học
$countClasses = $conn->query("SELECT COUNT(*) as total FROM classes")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="admin-wrapper">
    
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <h2 class="mb-4 border-bottom pb-2">Tổng quan hệ thống</h2>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card-counter bg-primary text-white p-3 rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="m-0 fw-bold counter-value" data-target="<?php echo $countStudent; ?>">0</h3>
                            <p class="m-0">Học viên</p>
                        </div>
                        <i class="fas fa-user-graduate fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-counter bg-success text-white p-3 rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="m-0 fw-bold counter-value" data-target="<?php echo $countTutor; ?>">0</h3>
                            <p class="m-0">Gia sư</p>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-counter bg-warning text-white p-3 rounded mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="m-0 fw-bold counter-value" data-target="<?php echo $countClasses; ?>">0</h3>
                            <p class="m-0">Lớp học</p>
                        </div>
                        <i class="fas fa-book-open fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="alert alert-info">
                Chào mừng Admin <strong><?php echo $_SESSION['fullname']; ?></strong> quay trở lại!
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../assets/js/admin_main.js"></script>
</body>
</body>
</html>