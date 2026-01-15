<?php
session_start();
require __DIR__ . '/../config/db.php';

// Kiểm tra quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

$activePage = 'classes';
$current_page = basename($_SERVER['PHP_SELF']);

// --- LỌC DỮ LIỆU ---
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$where_clause = "";

// 1. Xử lý Logic Lọc theo Tab
if ($filter_status == 'pending') {
    $where_clause = "WHERE classes.status = 'pending'";
} elseif ($filter_status == 'active') {
    $where_clause = "WHERE classes.status = 'active'";
} elseif ($filter_status == 'closed') {
    // Lọc các lớp đã đóng hoặc bị ẩn (hidden/closed)
    $where_clause = "WHERE classes.status IN ('hidden', 'closed')";
} elseif ($filter_status == 'rejected') {
    // (Tùy chọn) Lọc các lớp đã bị từ chối
    $where_clause = "WHERE classes.status = 'rejected'";
}

// 2. Tìm kiếm (Kết hợp với bộ lọc hiện tại)
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_keyword = $conn->real_escape_string($_GET['q']);
    $search_sql = "(classes.title LIKE '%$search_keyword%' OR classes.subject LIKE '%$search_keyword%' OR users.full_name LIKE '%$search_keyword%')";
    
    // Nếu chưa có WHERE thì thêm WHERE, nếu có rồi thì thêm AND
    $where_clause = ($where_clause == "") ? "WHERE $search_sql" : "$where_clause AND $search_sql";
}

$sql = "SELECT classes.*, users.full_name 
        FROM classes 
        LEFT JOIN users ON classes.tutor_id = users.id 
        $where_clause
        ORDER BY classes.created_at DESC";
$result = $conn->query($sql);

// Đếm số lượng chờ duyệt để hiện Badge đỏ thông báo
$count_pending = $conn->query("SELECT COUNT(*) as total FROM classes WHERE status = 'pending'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý bài đăng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .nav-tabs .nav-link { color: #6c757d; font-weight: 500; }
        .nav-tabs .nav-link:hover { border-color: #e9ecef #e9ecef #dee2e6; }
        .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; font-weight: bold; }
        
        /* Màu badge trạng thái */
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-active { background-color: #198754; }
        .badge-rejected { background-color: #dc3545; }
        .badge-closed { background-color: #6c757d; } /* Màu xám cho lớp đóng */
    </style>
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <h2 class="mb-4 text-dark border-bottom pb-2">Danh sách bài đăng</h2>
        
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link <?php echo ($filter_status == 'pending') ? 'active' : ''; ?>" href="?status=pending">
                    <i class="fas fa-clock me-2"></i>Chờ duyệt 
                    <?php if($count_pending > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-1"><?php echo $count_pending; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo ($filter_status == 'active') ? 'active' : ''; ?>" href="?status=active">
                    <i class="fas fa-check-circle me-2"></i>Đang hiển thị
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($filter_status == 'closed') ? 'active' : ''; ?>" href="?status=closed">
                    <i class="fas fa-lock me-2"></i>Lớp đã đóng
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($filter_status == 'all') ? 'active' : ''; ?>" href="classes.php">
                    <i class="fas fa-list me-2"></i>Tất cả
                </a>
            </li>
        </ul>

        <div class="row mb-3">
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="status" value="<?php echo $filter_status; ?>">
                    <input type="text" name="q" class="form-control" placeholder="Tìm tên lớp, môn học, gia sư..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th width="35%">Tiêu đề lớp</th>
                            <th width="15%">Gia sư</th>
                            <th width="15%">Học phí</th>
                            <th class="text-center" width="10%">Trạng thái</th>
                            <th class="text-center" width="20%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center text-muted"><?php echo $row['id']; ?></td>
                                
                                <td>
                                    <div class="fw-bold text-primary mb-1 text-truncate" style="max-width: 250px;">
                                        <a href="view_class.php?id=<?php echo $row['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-book me-1"></i><?php echo $row['subject']; ?>
                                    </small>
                                </td>
                                
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></div>
                                    <small class="text-muted"><?php echo date('d/m/y', strtotime($row['created_at'])); ?></small>
                                </td>
                                
                                <td class="text-success fw-bold">
                                    <?php echo htmlspecialchars($row['price']); ?>
                                </td>

                                <td class="text-center">
                                    <?php 
                                        $st = $row['status'];
                                        if ($st == 'pending') echo '<span class="badge badge-pending">Chờ duyệt</span>';
                                        elseif ($st == 'active') echo '<span class="badge badge-active">Hoạt động</span>';
                                        elseif ($st == 'rejected') echo '<span class="badge badge-rejected">Từ chối</span>';
                                        // Các trạng thái đóng/ẩn
                                        else echo '<span class="badge badge-closed"><i class="fas fa-lock small"></i> Đã đóng</span>';
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a href="view_class.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                                            <i class="fas fa-edit me-1"></i> Xem & Duyệt
                                        </a>
                                    <?php else: ?>
                                        <a href="view_class.php?id=<?php echo $row['id']; ?>" class="btn btn-light btn-sm border px-3">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="64" class="opacity-25 mb-3"><br>
                                    Không có bài đăng nào trong mục này.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>