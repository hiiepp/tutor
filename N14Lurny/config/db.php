<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'tutor_db';
$port = 3307; 
$conn = new mysqli($host, $user, $pass, $db_name, $port);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>