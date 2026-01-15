<?php
require 'config/db.php'; 
$email_can_sua = "admin@gmail.com"; 

$mat_khau_moi = "123456"; 

$mat_khau_ma_hoa = password_hash($mat_khau_moi, PASSWORD_DEFAULT);

$sql = "UPDATE users SET password = '$mat_khau_ma_hoa' WHERE email = '$email_can_sua'";

if ($conn->query($sql) === TRUE) {
    echo "<h3>✅ Đã reset thành công!</h3>";
    echo "<p>Email: $email_can_sua</p>";
    echo "<p>Mật khẩu mới: $mat_khau_moi</p>";
    echo "<a href='auth/login_register.php'>Bấm vào đây để đăng nhập lại</a>";
} else {
    echo "Lỗi: " . $conn->error;
}
?>