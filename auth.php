<?php
require_once 'db.php'; // Đã có sẵn session_start() trong db.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ===================================
    // 1. XỬ LÝ ĐĂNG KÝ
    // ===================================
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $password = $_POST['password'];
        $role = 0; // Mặc định đăng ký là khách hàng (0)

        // Kiểm tra xem email đã tồn tại chưa
        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            echo "<script>alert('Email này đã được đăng ký!'); window.history.back();</script>";
            exit();
        }

        // Mã hóa mật khẩu bảo mật
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $fullname, $email, $hashed_password, $phone, $address, $role);
        
        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); window.history.back();</script>";
        }
        $stmt->close();
    }

    // ===================================
    // 2. XỬ LÝ ĐĂNG NHẬP
    // ===================================
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Xác thực mật khẩu
            if (password_verify($password, $user['password'])) {
                // Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['user_role'] = $user['role'];

                // Lưu thông tin vào Session (Của bạn đang có sẵn)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['fullname'];
                $_SESSION['user_role'] = $user['role'];

                // --- BẮT ĐẦU THÊM MỚI: ĐỒNG BỘ GIỎ HÀNG TỪ SESSION VÀO DATABASE ---
                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                    $uid = $user['id'];
                    foreach ($_SESSION['cart'] as $product_id => $item) {
                        $qty = (int)$item['quantity'];
                        
                        // Kiểm tra xem món này đã có trong giỏ của tài khoản chưa
                        $check_cart = $conn->query("SELECT id FROM cart WHERE user_id = $uid AND product_id = $product_id");
                        if ($check_cart->num_rows > 0) {
                            // Có rồi thì cộng dồn số lượng
                            $conn->query("UPDATE cart SET quantity = quantity + $qty WHERE user_id = $uid AND product_id = $product_id");
                        } else {
                            // Chưa có thì thêm mới
                            $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($uid, $product_id, $qty)");
                        }
                    }
                    // Xóa giỏ hàng tạm sau khi đã gom thành công
                    unset($_SESSION['cart']); 
                }
                // --- KẾT THÚC PHẦN THÊM MỚI ---

                // Phân luồng điều hướng
                if ($user['role'] == 1) { 
                    header("Location: admin/products.php"); // Nếu là Admin -> Vào trang quản trị
                } else {
                    header("Location: index.php"); // Nếu là khách -> Về trang chủ
                }
            } else {
                echo "<script>alert('Sai mật khẩu!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại!'); window.history.back();</script>";
        }
        $stmt->close();
    }
}
?>