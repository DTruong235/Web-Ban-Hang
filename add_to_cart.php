<?php
require_once 'db.php'; // Chứa sẵn session_start()

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Lấy thông tin sản phẩm
    $sql = "SELECT * FROM products WHERE id = $id AND status = 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $price = ($product['discount_price'] != NULL) ? $product['discount_price'] : $product['price'];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $price,
                'image' => $product['cover_image'],
                'quantity' => 1
            ];
        }
    }
}

// Đếm tổng số lượng sản phẩm trong giỏ
$cart_count = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

// 1. NẾU LÀ YÊU CẦU CHẠY NGẦM TỪ JAVASCRIPT (AJAX)
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    // Trả về dữ liệu JSON chứa tổng số lượng mới và dừng chạy
    echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
    exit();
}

// 2. NẾU BẤM NÚT "MUA NGAY" (Vẫn cần chuyển trang)
if (isset($_GET['action']) && $_GET['action'] == 'buy_now') {
    header("Location: cart.php");
} else {
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $referer");
}
exit();
?>