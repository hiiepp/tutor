<?php
session_start();
require __DIR__ . '/../config/db.php';

// 1. Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php"); exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM users WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Người dùng không tồn tại!"; exit();
}

$user = $result->fetch_assoc();

// --- LOGIC XỬ LÝ DỮ LIỆU ---
// 1. Avatar
$avatar_path = "../assets/uploads/avatars/" . ($user['avatar'] ?? '');
$has_avatar = !empty($user['avatar']) && file_exists($avatar_path);
$initials = mb_strtoupper(mb_substr($user['full_name'], 0, 1, "UTF-8"));

// 2. Tính tuổi
$dob_display = 'Chưa cập nhật';
$age_display = '';
if (!empty($user['dob']) && $user['dob'] != '0000-00-00') {
    $dob_date = new DateTime($user['dob']);
    $now = new DateTime();
    $age = $now->diff($dob_date)->y;
    $dob_display = date('d/m/Y', strtotime($user['dob']));
    $age_display = "($age tuổi)";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết - <?php echo $user['full_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        
        <div class="container-fluid p-0">
            <div class="mb-4">
                <a href="users.php" class="text-decoration-none text-muted mb-2 d-inline-block">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
                <h2 class="fw-bold text-dark">Hồ sơ người dùng</h2>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 text-center p-4">
                        <div class="mb-3 d-flex justify-content-center">
                            <?php if($has_avatar): ?>
                                <img src="<?= $avatar_path ?>" class="rounded-circle object-fit-cover border" style="width: 120px; height: 120px;">
                            <?php else: ?>
                                <?php if($user['role'] == 'admin'): ?>
                                    <img src="https://cdn-icons-png.flaticon.com/512/2942/2942813.png" width="120">
                                <?php elseif($user['role'] == 'tutor'): ?>
                                    <img src="https://cdn-icons-png.flaticon.com/512/3429/3429440.png" width="120">
                                <?php else: ?>
                                    <img src="https://cdn-icons-png.flaticon.com/512/5850/5850276.png" width="120">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <h4 class="fw-bold"><?php echo $user['full_name']; ?></h4>
                        <p class="text-muted mb-1"><?php echo $user['email']; ?></p>
                        <p class="fw-bold text-primary"><?php echo $user['phone'] ?? 'Chưa có SĐT'; ?></p>
                        
                        <div class="mt-2">
                            <?php if($user['role'] == 'student'): ?>
                                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">Học viên</span>
                            <?php elseif($user['role'] == 'tutor'): ?>
                                <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">Gia sư</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill">Admin</span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 d-grid gap-2">
                            <a href="user_form.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i> Chỉnh sửa thông tin
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Thông tin cá nhân</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Mã số (ID):</div>
                                <div class="col-sm-8">#<?php echo $user['id']; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Họ và tên:</div>
                                <div class="col-sm-8 fw-bold"><?php echo $user['full_name']; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Email:</div>
                                <div class="col-sm-8"><a href="mailto:<?php echo $user['email']; ?>"><?php echo $user['email']; ?></a></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Số điện thoại:</div>
                                <div class="col-sm-8 text-danger fw-bold"><?php echo $user['phone'] ?? 'Chưa cập nhật'; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Ngày sinh:</div>
                                <div class="col-sm-8"><?php echo $dob_display . ' ' . $age_display; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Địa chỉ:</div>
                                <div class="col-sm-8"><?php echo $user['address'] ?? 'Chưa cập nhật'; ?></div>
                            </div>
                            <div class="row mb-3 pb-1">
                                <div class="col-sm-4 fw-bold text-secondary">Ngày tham gia:</div>
                                <div class="col-sm-8"><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if($user['role'] == 'tutor'): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-success"><i class="fas fa-graduation-cap me-2"></i>Hồ sơ chuyên môn</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Trình độ:</div>
                                <div class="col-sm-8 fw-bold"><?php echo $user['degree'] ?? '---'; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Chuyên ngành:</div>
                                <div class="col-sm-8"><?php echo $user['major'] ?? '---'; ?></div>
                            </div>
                            <div class="row mb-3 border-bottom pb-2">
                                <div class="col-sm-4 fw-bold text-secondary">Kinh nghiệm:</div>
                                <div class="col-sm-8"><?php echo $user['experience'] ?? '---'; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-4 fw-bold text-secondary">Giới thiệu bản thân:</div>
                                <div class="col-sm-8 text-muted fst-italic">
                                    "<?php echo nl2br(htmlspecialchars($user['bio'] ?? 'Chưa có thông tin giới thiệu.')); ?>"
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-info"><i class="fas fa-chalkboard me-2"></i>Các lớp đang dạy</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $tutor_id = $user['id'];
                            $sql_classes = "SELECT * FROM classes WHERE tutor_id = $tutor_id ORDER BY id DESC";
                            $result_classes = $conn->query($sql_classes);
                            
                            if ($result_classes->num_rows > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">ID</th>
                                            <th>Tên lớp học</th>
                                            <th>Trạng thái</th>
                                            <th>Học phí</th>
                                            <th class="text-center">Xem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($class = $result_classes->fetch_assoc()): ?>
                                        <tr>
                                            <td class="ps-4">#<?php echo $class['id']; ?></td>
                                            <td class="fw-bold text-primary text-truncate" style="max-width: 200px;">
                                                <?php echo htmlspecialchars($class['title']); ?>
                                            </td>
                                            <td>
                                                <?php if($class['status']=='active') echo '<span class="badge bg-success">Hoạt động</span>';
                                                      elseif($class['status']=='pending') echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                                      else echo '<span class="badge bg-secondary">Đóng</span>'; ?>
                                            </td>
                                            <td class="text-danger fw-bold"><?php echo htmlspecialchars($class['price']); ?></td>
                                            <td class="text-center">
                                                <a href="view_class.php?id=<?php echo $class['id']; ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết lớp dưới quyền Admin">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                    <p class="mb-0">Gia sư này chưa đăng lớp học nào.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>