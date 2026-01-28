<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $student_id = $_SESSION['user_id'];
    $class_id = intval($_POST['class_id']);
    $tutor_id = intval($_POST['tutor_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    // 1. Kiểm tra xem học viên này đã học lớp này chưa (Bảo mật)
    $check = $conn->query("SELECT id FROM class_registrations WHERE class_id=$class_id AND student_id=$student_id AND status='accepted'");
    if ($check->num_rows == 0) {
        $_SESSION['error'] = "Bạn chưa tham gia lớp này nên không thể đánh giá.";
        header("Location: ../class-detail.php?id=$class_id"); exit();
    }

    // 2. Kiểm tra xem đã đánh giá chưa (Mỗi lớp chỉ đánh giá 1 lần)
    $check_exist = $conn->query("SELECT id FROM reviews WHERE class_id=$class_id AND student_id=$student_id");
    if ($check_exist->num_rows > 0) {
        $_SESSION['error'] = "Bạn đã đánh giá lớp học này rồi.";
        header("Location: ../class-detail.php?id=$class_id"); exit();
    }

    // 3. Lưu đánh giá
    $stmt = $conn->prepare("INSERT INTO reviews (class_id, student_id, tutor_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiis", $class_id, $student_id, $tutor_id, $rating, $comment);
    
    if ($stmt->execute()) {
        // 4. TÍNH LẠI ĐIỂM TRUNG BÌNH CHO GIA SƯ
        // Lấy tất cả rating của gia sư này
        $sql_avg = "SELECT AVG(rating) as avg_score, COUNT(*) as total FROM reviews WHERE tutor_id = $tutor_id";
        $res_avg = $conn->query($sql_avg)->fetch_assoc();
        
        $new_avg = round($res_avg['avg_score'], 1); // Làm tròn 1 chữ số thập phân (VD: 4.5)
        $total_reviews = $res_avg['total'];

        // Cập nhật vào bảng users
        $conn->query("UPDATE users SET avg_rating = $new_avg, review_count = $total_reviews WHERE id = $tutor_id");

        $_SESSION['success'] = "Cảm ơn bạn đã gửi đánh giá!";
    } else {
        $_SESSION['error'] = "Lỗi hệ thống: " . $conn->error;
    }

    header("Location: ../class-detail.php?id=$class_id");
    exit();
}
?>