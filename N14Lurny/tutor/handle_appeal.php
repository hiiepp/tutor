<?php
session_start();
require '../config/db.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tutor') {
    header("Location: ../auth/login_register.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tutor_id = $_SESSION['user_id'];
    $report_id = intval($_POST['report_id']);
    $content = trim($_POST['content']);
    $evidence_img = null;

    if (empty($content)) {
        $_SESSION['error'] = "Vui lòng nhập nội dung giải trình.";
        header("Location: violations.php"); exit();
    }

    // Xử lý upload ảnh
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['evidence']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $dir = "../assets/uploads/appeals/";
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            
            $new_name = "appeal_" . $tutor_id . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES['evidence']['tmp_name'], $dir . $new_name)) {
                $evidence_img = $new_name;
            }
        } else {
            $_SESSION['error'] = "Chỉ chấp nhận file ảnh JPG, PNG.";
            header("Location: violations.php"); exit();
        }
    } else {
        $_SESSION['error'] = "Vui lòng tải lên ảnh minh chứng.";
        header("Location: violations.php"); exit();
    }

    // Lưu vào CSDL
    $sql = "INSERT INTO appeals (report_id, tutor_id, content, evidence_image, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $report_id, $tutor_id, $content, $evidence_img);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Đã gửi khiếu nại thành công! Admin sẽ xem xét sớm nhất.";
    } else {
        $_SESSION['error'] = "Lỗi hệ thống: " . $conn->error;
    }

    header("Location: violations.php");
    exit();
}
?>