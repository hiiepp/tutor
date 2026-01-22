<?php
session_start();
require '../config/db.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    $student_id = $_SESSION['user_id'];
    $class_id = intval($_POST['class_id']);

    // 1. Kiểm tra xem có đơn đăng ký trạng thái 'pending' không
    $check_sql = "SELECT id FROM class_registrations WHERE class_id = ? AND student_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $class_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Thực hiện xóa
        $del_sql = "DELETE FROM class_registrations WHERE class_id = ? AND student_id = ? AND status = 'pending'";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bind_param("ii", $class_id, $student_id);
        
        if ($del_stmt->execute()) {
            $_SESSION['success'] = "Đã hủy yêu cầu đăng ký thành công.";
        } else {
            $_SESSION['error'] = "Lỗi hệ thống: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Không thể hủy. Có thể yêu cầu đã được duyệt hoặc không tồn tại.";
    }

    // Quay lại trang chi tiết lớp
    header("Location: ../class-detail.php?id=$class_id");
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>