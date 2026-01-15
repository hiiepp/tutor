<?php
session_start();
require '../config/db.php'; 

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    $_SESSION['error'] = "Bạn cần đăng nhập tài khoản Học viên để đăng ký!";
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['user_id'];
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // 1. Kiểm tra đã đăng ký chưa
    $check_sql = "SELECT id FROM class_registrations WHERE class_id = ? AND student_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $class_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Bạn đã gửi yêu cầu cho lớp này rồi!'); window.history.back();</script>";
        exit();
    } 
    
    // 2. Thêm đăng ký mới
    $insert_sql = "INSERT INTO class_registrations (class_id, student_id, status, message, created_at) VALUES (?, ?, 'pending', ?, NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    
    if ($insert_stmt) {
        $insert_stmt->bind_param("iis", $class_id, $student_id, $message);
        
        if ($insert_stmt->execute()) {
            echo "<script>
                    alert('Gửi yêu cầu thành công! Vui lòng chờ Gia sư duyệt.'); 
                    window.location.href='dashboard.php';
                  </script>";
        } else {
            echo "<script>alert('Lỗi hệ thống: " . $conn->error . "'); window.history.back();</script>";
        }
        $insert_stmt->close();
    } else {
        echo "<script>alert('Lỗi kết nối CSDL.'); window.history.back();</script>";
    }
    
    $stmt->close();

} else {
    header("Location: ../index.php");
    exit();
}
?>