<?php
session_start();
require __DIR__ . '/../config/db.php';

if (isset($_POST['btn_login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        
        if (password_verify($password, $row['password'])) {
            
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['fullname'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            
            if ($row['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } 
            elseif ($row['role'] == 'tutor') {
                
                header("Location: ../tutor/class_management.php");
            } 
            else {
            
                header("Location: ../index.php");
            }    
            exit();
        } else {
            $_SESSION['message'] = "Sai mật khẩu!";
            header("Location: login_register.php");
        }
    } else {
        $_SESSION['message'] = "Email không tồn tại!";
        header("Location: login_register.php");
    }
}
?>