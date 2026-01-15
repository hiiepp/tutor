<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

if (isset($_GET['id'])) {
    $class_id = intval($_GET['id']); 
    $tutor_id = $_SESSION['user_id'];

    $sql = "DELETE FROM classes WHERE id = ? AND tutor_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $class_id, $tutor_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<script>
                        alert('Đã xóa lớp học thành công!');
                        window.location.href = 'class_management.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Lỗi: Không tìm thấy lớp hoặc bạn không có quyền xóa lớp này.');
                        window.location.href = 'class_management.php';
                      </script>";
            }
        } else {
            echo "Lỗi hệ thống: " . $conn->error;
        }
        $stmt->close();
    }
} else {
    header("Location: class_management.php");
}

$conn->close();
?>