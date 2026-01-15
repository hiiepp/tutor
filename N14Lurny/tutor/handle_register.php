<?php
session_start();
require '../config/db.php'; 

// Thiết lập múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Bạn cần đăng nhập để thực hiện chức năng này.";
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['class_id'])) {
    
    $class_id = intval($_POST['class_id']);
    $student_id = $_SESSION['user_id'];
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Kiểm tra đã đăng ký chưa
    $check_sql = "SELECT id FROM class_registrations WHERE class_id = ? AND student_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $class_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Bạn đã gửi yêu cầu đăng ký cho lớp học này rồi! Vui lòng chờ phản hồi.";
        echo "<script>alert('Bạn đã đăng ký lớp này rồi!'); window.history.back();</script>";
    } else {
        // Insert đăng ký
        $insert_sql = "INSERT INTO class_registrations (class_id, student_id, status, message, created_at) VALUES (?, ?, 'pending', ?, NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt) {
            $insert_stmt->bind_param("iis", $class_id, $student_id, $message);
            
            if ($insert_stmt->execute()) {
                
                // --- BẮT ĐẦU: TẠO THÔNG BÁO CHO GIA SƯ ---
                // 1. Lấy thông tin lớp và ID gia sư
                $tutor_query = $conn->prepare("SELECT tutor_id, title FROM classes WHERE id = ?");
                $tutor_query->bind_param("i", $class_id);
                $tutor_query->execute();
                $tutor_res = $tutor_query->get_result()->fetch_assoc();

                if ($tutor_res) {
                    $notif_uid = $tutor_res['tutor_id']; // ID gia sư
                    $notif_title = "Đăng ký mới";
                    $notif_msg = "Có học viên vừa đăng ký lớp: " . $tutor_res['title'];
                    $notif_link = "see_details.php?id=" . $class_id; // Link đến trang quản lý lớp

                    // 2. Chèn vào bảng notifications
                    $notif_insert = $conn->prepare("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (?, ?, ?, ?, NOW())");
                    $notif_insert->bind_param("isss", $notif_uid, $notif_title, $notif_msg, $notif_link);
                    $notif_insert->execute();
                }
                // --- KẾT THÚC LOGIC THÔNG BÁO ---

                echo "<script>
                        alert('Gửi yêu cầu thành công! Gia sư sẽ sớm liên hệ với bạn.'); 
                        window.location.href='dashboard.php';
                      </script>";
            } else {
                echo "<script>alert('Lỗi hệ thống: " . $conn->error . "'); window.history.back();</script>";
            }
            $insert_stmt->close();
        } else {
            echo "<script>alert('Lỗi kết nối CSDL.'); window.history.back();</script>";
        }
    }
    $stmt->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>