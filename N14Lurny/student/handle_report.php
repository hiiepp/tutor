<?php
session_start();
require '../config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    $_SESSION['error'] = "Vui lòng đăng nhập.";
    header("Location: ../auth/login_register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_SESSION['user_id'];
    $class_id = intval($_POST['class_id']);
    $tutor_id = intval($_POST['tutor_id']);
    $reason = trim($_POST['reason']);
    $description = trim($_POST['description']);
    $image_proof = null; // Mặc định không có ảnh

    if (empty($reason) || empty($description)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ lý do và mô tả chi tiết.";
        header("Location: ../class-detail.php?id=$class_id");
        exit();
    }

    // --- XỬ LÝ UPLOAD ẢNH ---
    if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['proof_image']['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            // Tạo thư mục nếu chưa có
            $upload_dir = "../assets/uploads/reports/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Tạo tên file độc nhất: report_IDHS_Timestamp.ext
            $new_filename = "report_" . $student_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $destination)) {
                $image_proof = $new_filename;
            }
        } else {
            $_SESSION['error'] = "Định dạng ảnh không hợp lệ! Chỉ chấp nhận JPG, PNG.";
            header("Location: ../class-detail.php?id=$class_id");
            exit();
        }
    }
    // ------------------------

    // Lưu báo cáo vào DB (Thêm cột image_proof)
    $sql = "INSERT INTO reports (student_id, tutor_id, class_id, reason, description, image_proof, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // 's' cho image_proof vì nó là chuỗi (varchar)
        $stmt->bind_param("iiisss", $student_id, $tutor_id, $class_id, $reason, $description, $image_proof);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Đã gửi báo cáo thành công! Ban quản trị sẽ xem xét và xử lý.";
        } else {
            $_SESSION['error'] = "Lỗi hệ thống: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Lỗi kết nối CSDL.";
    }

    header("Location: ../class-detail.php?id=$class_id");
    exit();
}
?>