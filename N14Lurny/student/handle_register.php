<?php
session_start();
require '../config/db.php'; 

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    $_SESSION['error'] = "Bạn cần đăng nhập tài khoản Học viên để đăng ký!";
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['user_id'];
    $student_name = $_SESSION['fullname']; // Lấy tên học viên để hiện trong thông báo
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
            
            // --- BỔ SUNG: GỬI THÔNG BÁO CHO GIA SƯ ---
            
            // A. Lấy thông tin lớp và ID gia sư
            $sql_class = "SELECT title, tutor_id FROM classes WHERE id = ?";
            $stmt_class = $conn->prepare($sql_class);
            $stmt_class->bind_param("i", $class_id);
            $stmt_class->execute();
            $class_info = $stmt_class->get_result()->fetch_assoc();
            
            if ($class_info) {
                $tutor_id = $class_info['tutor_id'];
                $class_title = $class_info['title'];
                
                // B. Soạn nội dung thông báo
                $notif_title = "Học viên mới đăng ký 🎓";
                $notif_msg = "Học viên <strong>$student_name</strong> vừa đăng ký lớp: <strong>$class_title</strong>. Hãy kiểm tra ngay.";
                // Link dẫn đến trang chi tiết lớp của gia sư (chỉ cần tên file, vì header_tutor đã thêm /tutor/)
                $notif_link = "see_details.php?id=" . $class_id; 
                
                // C. Insert vào bảng notifications
                $sql_notif = "INSERT INTO notifications (user_id, title, message, link, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
                $stmt_notif = $conn->prepare($sql_notif);
                $stmt_notif->bind_param("isss", $tutor_id, $notif_title, $notif_msg, $notif_link);
                $stmt_notif->execute();
            }
            // ------------------------------------------

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