<?php
session_start();
require_once '../config/db.php';

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

$tutor_id = $_SESSION['user_id'];

$registration_id = isset($_GET['reg_id']) ? intval($_GET['reg_id']) : 0;
$class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : ''; 

if ($registration_id == 0 || $class_id == 0 || !in_array($action, ['accept', 'reject'])) {
    die("Dữ liệu không hợp lệ.");
}

// 1. Kiểm tra quyền sở hữu & Lấy tiêu đề lớp (để ghi vào thông báo)
$check_owner = $conn->prepare("SELECT id, title, max_students FROM classes WHERE id = ? AND tutor_id = ?");
$check_owner->bind_param("ii", $class_id, $tutor_id);
$check_owner->execute();
$res_owner = $check_owner->get_result();

if ($res_owner->num_rows == 0) {
    die("Bạn không có quyền thao tác trên lớp học này.");
}

$class_info = $res_owner->fetch_assoc();
$max_students = $class_info['max_students'];
$class_title = $class_info['title']; // Lấy tên lớp

// 2. Lấy ID học sinh từ đơn đăng ký (để biết gửi thông báo cho ai)
$get_student = $conn->prepare("SELECT student_id FROM class_registrations WHERE id = ?");
$get_student->bind_param("i", $registration_id);
$get_student->execute();
$student_info = $get_student->get_result()->fetch_assoc();
$student_id = $student_info['student_id'] ?? 0;

if ($action == 'reject') {
    $stmt = $conn->prepare("UPDATE class_registrations SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $registration_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Đã từ chối học viên.";
        
        // --- GỬI THÔNG BÁO TỪ CHỐI ---
        if ($student_id > 0) {
            $notif_title = "Đăng ký bị từ chối ❌";
            $notif_msg = "Gia sư đã từ chối yêu cầu tham gia lớp: " . $class_title;
            $notif_link = "class-detail.php?id=" . $class_id;
            
            $n_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (?, ?, ?, ?, NOW())");
            $n_stmt->bind_param("isss", $student_id, $notif_title, $notif_msg, $notif_link);
            $n_stmt->execute();
        }
    }
} 
elseif ($action == 'accept') {
    
    $count_sql = "SELECT COUNT(*) as total FROM class_registrations WHERE class_id = ? AND status = 'accepted'";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param("i", $class_id);
    $stmt_count->execute();
    $current_accepted = $stmt_count->get_result()->fetch_assoc()['total'];

    if ($current_accepted >= $max_students) {
        echo "<script>alert('Lớp đã đủ số lượng học viên! Không thể duyệt thêm.'); window.location.href='see_details.php?id=$class_id';</script>";
        exit();
    }

    $stmt = $conn->prepare("UPDATE class_registrations SET status = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $registration_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Đã duyệt học viên thành công!";
        
        // --- GỬI THÔNG BÁO CHẤP NHẬN ---
        if ($student_id > 0) {
            $notif_title = "Đăng ký thành công! ✅";
            $notif_msg = "Chúc mừng! Gia sư đã duyệt bạn vào lớp: " . $class_title . ". Xem chi tiết để lấy SĐT liên hệ.";
            $notif_link = "class-detail.php?id=" . $class_id;
            
            $n_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (?, ?, ?, ?, NOW())");
            $n_stmt->bind_param("isss", $student_id, $notif_title, $notif_msg, $notif_link);
            $n_stmt->execute();
        }

        // Kiểm tra đóng lớp nếu đầy
        $stmt_count->execute();
        $new_total = $stmt_count->get_result()->fetch_assoc()['total'];

        if ($new_total >= $max_students) {
            $stmt_close = $conn->prepare("UPDATE classes SET status = 'hidden' WHERE id = ?");
            $stmt_close->bind_param("i", $class_id);
            $stmt_close->execute();
            $_SESSION['message'] .= " Lớp đã đủ học viên và tự động đóng.";
        }
    }
}

header("Location: see_details.php?id=" . $class_id);
exit();
?>