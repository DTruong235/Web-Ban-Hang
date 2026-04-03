<?php
require_once 'db.php'; // Đã có session_start()

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$is_ajax = isset($_GET['ajax']) ? 1 : 0;

if ($product_id > 0) {
    // 1. NẾU KHÁCH ĐÃ ĐĂNG NHẬP (Lưu thẳng vào Database)
    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        
        $check = $conn->query("SELECT id FROM cart WHERE user_id = $uid AND product_id = $product_id");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id = $uid AND product_id = $product_id");
        } else {
            $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($uid, $product_id, 1)");
        }
    } 
    // 2. NẾU LÀ KHÁCH VÃNG LAI (Lưu tạm vào Session)
    else {
        // Load thông tin sản phẩm đầy đủ
        $prod_res = $conn->query("SELECT id, name, price, discount_price, cover_image FROM products WHERE id = $product_id");
        if ($prod_res && $prod_row = $prod_res->fetch_assoc()) {
            $final_price = ($prod_row['discount_price'] != NULL) ? $prod_row['discount_price'] : $prod_row['price'];
            
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'product_id' => $product_id,
                    'name' => $prod_row['name'],
                    'price' => $final_price,
                    'cover_image' => $prod_row['cover_image'],
                    'quantity' => 1
                ];
            }
        }
    }
}

// 3. TÍNH LẠI TỔNG SỐ LƯỢNG MÓN ĐỂ HIỂN THỊ LÊN ICON MÀU ĐỎ (BADGE)
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $res = $conn->query("SELECT SUM(quantity) as total_qty FROM cart WHERE user_id = $uid");
    if ($res && $row = $res->fetch_assoc()) {
        $cart_count = $row['total_qty'] ?? 0;
    }
} else if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

// 4. TRẢ KẾT QUẢ VỀ (Cho JS xử lý hoặc chuyển trang)
if ($is_ajax) {
    echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
    exit();
} else {
    // Nếu bấm nút "Mua Ngay" thì chuyển thẳng sang giỏ hàng
    header("Location: cart.php");
    exit();
}
?>