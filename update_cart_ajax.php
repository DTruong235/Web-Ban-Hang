<?php
require_once 'db.php';

header('Content-Type: application/json');

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

// Xóa nếu số lượng <= 0
if ($quantity <= 0) {
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $conn->query("DELETE FROM cart WHERE user_id = $uid AND product_id = $product_id");
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    echo json_encode(['status' => 'deleted']);
} else {
    // Cập nhật số lượng
    if (isset($_SESSION['user_id'])) {
        $uid = (int)$_SESSION['user_id'];
        $conn->query("UPDATE cart SET quantity = $quantity WHERE user_id = $uid AND product_id = $product_id");
    } else {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        }
    }
    echo json_encode(['status' => 'updated', 'quantity' => $quantity]);
}
