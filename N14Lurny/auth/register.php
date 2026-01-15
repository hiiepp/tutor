<?php
session_start();
require __DIR__ . '/../config/db.php';

if (isset($_POST['btn_register'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Lấy số điện thoại (Nếu là học viên thì để NULL)
    $phone = isset($_POST['phone']) && $role === 'tutor' ? $_POST['phone'] : null;

    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        $_SESSION['message'] = "Email này đã được sử dụng!";
        header("Location: login_register.php");
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if ($phone) {
            $sql = "INSERT INTO users (full_name, email, password, role, phone, created_at) 
                    VALUES ('$fullname', '$email', '$hashed_password', '$role', '$phone', NOW())";
        } else {
            // Trường hợp học viên không có sđt
            $sql = "INSERT INTO users (full_name, email, password, role, created_at) 
                    VALUES ('$fullname', '$email', '$hashed_password', '$role', NOW())";
        }

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Đăng ký thành công! Mời bạn đăng nhập.";
            header("Location: login_register.php");
            exit();
        } else {
            $_SESSION['message'] = "Lỗi hệ thống: " . $conn->error;
            header("Location: login_register.php");
            exit();
        }
    }
}
?>