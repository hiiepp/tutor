<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login_register.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $class_id = intval($_GET['id']);
    $action = $_GET['action'];
    $tutor_id = $_SESSION['user_id'];

    if ($action == 'open') {
        $check_sql = "SELECT max_students, 
                             (SELECT COUNT(*) FROM class_registrations WHERE class_id = ? AND status = 'accepted') as accepted_count
                      FROM classes WHERE id = ? AND tutor_id = ?";
        
        $stmt_check = $conn->prepare($check_sql);
        $stmt_check->bind_param("iii", $class_id, $class_id, $tutor_id);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        
        if ($res->num_rows > 0) {
            $data = $res->fetch_assoc();
            $max = $data['max_students'] ?? 1;
            
            if ($data['accepted_count'] >= $max) {
                header("Location: class_management.php?msg=class_full");
                exit();
            }
        }
    }

    $new_status = ($action == 'close') ? 'hidden' : 'active'; 

    $sql = "UPDATE classes SET status = ? WHERE id = ? AND tutor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $new_status, $class_id, $tutor_id);

    if ($stmt->execute()) {
        header("Location: class_management.php?msg=status_updated");
    } else {
        echo "Lỗi: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: class_management.php");
}
?>