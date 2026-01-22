<?php
session_start();
require '../config/db.php'; // Đảm bảo đường dẫn tới file db.php đúng

if (isset($_GET['id']) && isset($_GET['url'])) {
    $notif_id = intval($_GET['id']);
    $redirect_url = $_GET['url'];
    $user_id = $_SESSION['user_id'];

    // Cập nhật trạng thái đã đọc (is_read = 1)
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();

    // Chuyển hướng đến trang đích
    header("Location: " . $redirect_url);
    exit();
} else {
    // Nếu lỗi, quay về trang chủ
    header("Location: ../index.php");
    exit();
}
?>