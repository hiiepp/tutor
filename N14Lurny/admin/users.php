<?php
session_start();
require __DIR__ . '/../config/db.php';

// 1. Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

$activePage = 'users';

// 2. Xử lý Xóa
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    if ($id != $_SESSION['user_id']) { 
        $conn->query("DELETE FROM users WHERE id=$id");
        header("Location: users.php?msg=deleted"); 
    } else {
        header("Location: users.php?msg=error");
    }
    exit();
}

// 3. Xử lý Tìm kiếm
$keyword = '';
$sql = "SELECT * FROM users WHERE 1=1";

if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $sql .= " AND (full_name LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%')";
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);

// 4. Phân loại User
$students = [];
$tutors = [];
$admins = [];

while ($row = $result->fetch_assoc()) {
    if ($row['role'] == 'student') { $students[] = $row; }
    elseif ($row['role'] == 'tutor') { $tutors[] = $row; }
    else { $admins[] = $row; }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <div class="mb-4">
            <h2 class="text-dark m-0 fw-bold">Danh sách Người dùng</h2>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-3">
                <form method="GET" class="row g-2 align-items-center">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0 ps-0" 
                                   placeholder="Tìm theo tên, email hoặc số điện thoại..." 
                                   value="<?php echo htmlspecialchars($keyword); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-end mb-3 border-bottom">
            <ul class="nav nav-tabs border-bottom-0 mb-0" id="roleTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-bold" id="student-tab" data-bs-toggle="tab" data-bs-target="#tab-student" type="button">
                        <i class="fas fa-user-graduate me-2"></i>Học viên (<?php echo count($students); ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="tutor-tab" data-bs-toggle="tab" data-bs-target="#tab-tutor" type="button">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Gia sư (<?php echo count($tutors); ?>)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-bold" id="admin-tab" data-bs-toggle="tab" data-bs-target="#tab-admin" type="button">
                        <i class="fas fa-user-shield me-2"></i>Admin (<?php echo count($admins); ?>)
                    </button>
                </li>
            </ul>
            <div class="mb-2">
                <a href="user_form.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i> Thêm người dùng</a>
            </div>
        </div>

        <div class="tab-content" id="roleTabsContent">
            <div class="tab-pane fade show active" id="tab-student" role="tabpanel"><?php renderTable($students); ?></div>
            <div class="tab-pane fade" id="tab-tutor" role="tabpanel"><?php renderTable($tutors); ?></div>
            <div class="tab-pane fade" id="tab-admin" role="tabpanel"><?php renderTable($admins); ?></div>
        </div>
    </main>
</div>

<?php
function renderTable($data) {
    if (empty($data)) {
        echo '<div class="alert alert-light text-center py-4 text-muted"><i class="fas fa-info-circle me-2"></i>Không tìm thấy dữ liệu.</div>';
        return;
    }
    ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%" class="text-center">STT</th>
                        <th width="25%">Họ tên</th>
                        <th width="30%">Email</th>
                        <th width="15%">SĐT</th> <th width="10%">Vai trò</th>
                        <th width="15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $stt = 1; 
                    foreach ($data as $row): 
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-secondary"><?php echo $stt++; ?></td>
                        <td class="fw-bold text-primary"><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone'] ? $row['phone'] : '<span class="text-muted small">---</span>'; ?></td>
                        <td>
                            <?php if($row['role'] == 'student'): ?><span class="badge bg-primary">Học viên</span>
                            <?php elseif($row['role'] == 'tutor'): ?><span class="badge bg-success">Gia sư</span>
                            <?php else: ?><span class="badge bg-danger">Admin</span><?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="user_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm text-white me-1"><i class="fas fa-eye"></i></a>
                            <a href="user_form.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm text-white me-1"><i class="fas fa-edit"></i></a>
                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Xóa người dùng này?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} 
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>