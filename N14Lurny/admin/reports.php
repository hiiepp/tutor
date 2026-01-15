<?php
session_start();
require __DIR__ . '/../config/db.php';

// 1. Check quyền Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_register.php"); exit();
}

$activePage = 'reports';

// 2. Xử lý hành động
// Xóa báo cáo
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM reports WHERE id=$id");
    header("Location: reports.php?msg=deleted");
    exit();
}

// Đánh dấu đã xử lý
if (isset($_GET['mark_processed'])) {
    $id = intval($_GET['mark_processed']);
    $conn->query("UPDATE reports SET status='processed' WHERE id=$id");
    header("Location: reports.php?msg=processed");
    exit();
}

// 3. Lấy danh sách báo cáo
// Join bảng reports với users (người báo cáo) và classes (lớp bị báo cáo)
$sql = "SELECT r.*, u.full_name as reporter_name, c.title as class_title, c.id as class_id
        FROM reports r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN classes c ON r.class_id = c.id
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Báo cáo Vi phạm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php include '../includes/admin_sidebar.php'; ?>

    <main class="admin-content">
        <h2 class="mb-4 text-dark border-bottom pb-2">Danh sách Báo cáo / Khiếu nại</h2>

        <?php if(isset($_GET['msg']) && $_GET['msg']=='processed'): ?>
            <div class="alert alert-success">Đã đánh dấu xử lý thành công!</div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">Người báo cáo</th>
                            <th width="20%">Lớp bị báo cáo</th>
                            <th width="30%">Lý do</th>
                            <th width="10%">Ngày gửi</th>
                            <th width="10%">Trạng thái</th>
                            <th width="10%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="<?php echo ($row['status'] == 'pending') ? 'table-warning' : ''; ?>">
                                <td>#<?= $row['id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['reporter_name']) ?></td>
                                <td>
                                    <a href="view_class.php?id=<?= $row['class_id'] ?>" target="_blank" class="text-decoration-none">
                                        <?= htmlspecialchars($row['class_title']) ?> <i class="fas fa-external-link-alt small"></i>
                                    </a>
                                </td>
                                <td class="text-danger"><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if($row['status'] == 'pending'): ?>
                                        <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Đã xử lý</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($row['status'] == 'pending'): ?>
                                        <a href="reports.php?mark_processed=<?= $row['id'] ?>" class="btn btn-sm btn-success" title="Đánh dấu đã xong">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="reports.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa báo cáo này?')" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-4">Không có báo cáo nào.</td></tr>
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