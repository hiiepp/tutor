<?php
session_start();
require_once '../config/db.php';

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

$check_owner = $conn->prepare("SELECT id, max_students FROM classes WHERE id = ? AND tutor_id = ?");
$check_owner->bind_param("ii", $class_id, $tutor_id);
$check_owner->execute();
$res_owner = $check_owner->get_result();

if ($res_owner->num_rows == 0) {
    die("Bạn không có quyền thao tác trên lớp học này.");
}

$class_info = $res_owner->fetch_assoc();
$max_students = $class_info['max_students'];

if ($action == 'reject') {
    $stmt = $conn->prepare("UPDATE class_registrations SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $registration_id);
    $stmt->execute();
    $_SESSION['message'] = "Đã từ chối học viên.";
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