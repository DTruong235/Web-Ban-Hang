<?php
//Để dùng giỏ hàng
session_start();

$host = "127.0.0.1";
$user = "root";       // Mặc định của XAMPP/WAMP
$pass = "vertrigo";           // Mặc định là rỗng
$dbname = "ban_hang_db";
//$port = 3307;

// Tạo kết nối bằng mysqli
$conn = new mysqli($host, $user, $pass, $dbname /*$port*/);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập charset utf8mb4 để không bị lỗi font tiếng Việt
$conn->set_charset("utf8mb4");
?>