<?php
require_once 'db.php';

// --- 1. XỬ LÝ KHI NGƯỜI DÙNG BẤM "XÁC NHẬN ĐẶT HÀNG" ---
if (isset($_POST['place_order'])) {
    $full_name = trim($_POST['full_name']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    $payment_method = trim($_POST['payment_method']);
    
    $selected_items = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];
    $qty = isset($_POST['qty']) ? $_POST['qty'] : [];

    if (empty($selected_items) || empty($full_name) || empty($phone_number) || empty($address)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.history.back();</script>";
        exit();
    }

    // Tính lại tổng tiền cho chắc chắn (bảo mật)
    $total_money = 0;
    $order_items = [];
    foreach ($selected_items as $id) {
        if (isset($_SESSION['cart'][$id])) {
            $item = $_SESSION['cart'][$id];
            $quantity = (int)$qty[$id];
            $price = $item['price'];
            $total_money += ($price * $quantity);
            $order_items[$id] = [
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }

    // Bắt đầu Transaction (Đảm bảo lưu đủ 3 bảng, lỗi 1 cái là hủy toàn bộ để tránh sai sót data)
    $conn->begin_transaction();
    try {
        // Bước 1: Lưu Khách Hàng
        $stmt = $conn->prepare("INSERT INTO customers (full_name, phone_number, address) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $full_name, $phone_number, $address);
        $stmt->execute();
        $customer_id = $conn->insert_id; // Lấy ID khách hàng vừa tạo
        $stmt->close();

        // Bước 2: Lưu Đơn Hàng chính
        $status = 0; // Trạng thái 0 = Mới đặt
        $stmt2 = $conn->prepare("INSERT INTO orders (customer_id, total_money, payment_method, status) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("iisi", $customer_id, $total_money, $payment_method, $status);
        $stmt2->execute();
        $order_id = $conn->insert_id; // Lấy ID đơn hàng vừa tạo
        $stmt2->close();

        // Bước 3: Lưu Chi Tiết Đơn Hàng & Xóa món đó khỏi Giỏ
        $stmt3 = $conn->prepare("INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
        foreach ($order_items as $p_id => $data) {
            $stmt3->bind_param("iiii", $order_id, $p_id, $data['price'], $data['quantity']);
            $stmt3->execute();
            
            // Đặt hàng xong thì phải xóa món đó khỏi Session giỏ hàng
            unset($_SESSION['cart'][$p_id]);
        }
        $stmt3->close();

        // Hoàn tất Transaction
        $conn->commit();
        
        // Thông báo và chuyển về trang chủ
        echo "<script>
                alert('🎉 Đặt hàng thành công! Mã đơn hàng của bạn là #$order_id. Cảm ơn bạn đã mua sắm tại Bách Hóa Pew!'); 
                window.location.href='index.php';
              </script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback(); // Bị lỗi thì hoàn tác
        echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!'); window.history.back();</script>";
        exit();
    }
}

// --- 2. HIỂN THỊ GIAO DIỆN THANH TOÁN ---
// Hứng dữ liệu từ file cart.php truyền qua
$selected_ids = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];
$quantities = isset($_POST['qty']) ? $_POST['qty'] : [];

// Nếu không có món nào được chọn mà cố tình vào trang này -> Đuổi về Giỏ hàng
if (empty($selected_ids) || !isset($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

$checkout_items = [];
$total_price = 0;

// Lọc ra đúng những món khách đã tick chọn
foreach ($selected_ids as $id) {
    if (isset($_SESSION['cart'][$id])) {
        $item = $_SESSION['cart'][$id];
        $item['quantity'] = (int)$quantities[$id];
        $checkout_items[$id] = $item;
        $total_price += ($item['price'] * $item['quantity']);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - Bách Hóa Pew</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <header class="bg-white border-bottom py-3 sticky-top shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold fs-3 text-tempi-green" href="index.php"><i class="fas fa-store me-2"></i>Bách Hóa Pew</a>
            <h5 class="mb-0 text-muted border-start ps-3 d-none d-md-block">Thanh Toán An Toàn</h5>
        </div>
    </header>

    <section class="container my-5">
        <form action="checkout.php" method="POST">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="bg-white p-4 p-md-5 rounded shadow-sm border-top border-tempi-green border-4">
                        <h4 class="fw-bold mb-4"><i class="fas fa-map-marker-alt text-danger me-2"></i>Thông Tin Giao Hàng</h4>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Họ và tên người nhận</label>
                                <input type="text" name="full_name" class="form-control form-control-lg bg-light" placeholder="Nhập họ tên" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="tel" name="phone_number" class="form-control form-control-lg bg-light" placeholder="Ví dụ: 0987654321" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Địa chỉ giao hàng cụ thể</label>
                            <textarea name="address" class="form-control bg-light" rows="3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..." required></textarea>
                        </div>

                        <h4 class="fw-bold mb-3 mt-5"><i class="fas fa-wallet text-tempi-green me-2"></i>Phương Thức Thanh Toán</h4>
                        
                        <div class="border rounded p-3 mb-2 bg-light cursor-pointer">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Tiền mặt" checked style="transform: scale(1.2);">
                                <label class="form-check-label fw-semibold ms-2 w-100" for="cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>
                        </div>
                        
                        <div class="border rounded p-3 bg-light cursor-pointer">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="banking" value="Chuyển khoản" style="transform: scale(1.2);">
                                <label class="form-check-label fw-semibold ms-2 w-100" for="banking">
                                    Chuyển khoản ngân hàng (Sẽ có nhân viên gọi xác nhận)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-white p-4 rounded shadow-sm border-top border-warning border-4 sticky-top" style="top: 90px;">
                        <h4 class="fw-bold mb-4">Đơn Hàng Của Bạn</h4>
                        
                        <div class="checkout-items-list mb-4" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
                            <?php foreach ($checkout_items as $id => $item): ?>
                                
                                <input type="hidden" name="selected_items[]" value="<?= $id ?>">
                                <input type="hidden" name="qty[<?= $id ?>]" value="<?= $item['quantity'] ?>">

                                <div class="d-flex align-items-center mb-3">
                                    <div class="position-relative me-3">
                                        <img src="<?= $item['image'] ?>" alt="Img" style="width: 65px; height: 65px; object-fit: cover; border-radius: 8px;" class="border">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary border border-white">
                                            <?= $item['quantity'] ?>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($item['name']) ?>">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </h6>
                                        <span class="text-danger fw-bold"><?= number_format($item['price'] * $item['quantity']) ?>đ</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tạm tính:</span>
                            <span class="fw-semibold"><?= number_format($total_price) ?> đ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <span class="text-success fw-bold">Miễn phí</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4 bg-light p-3 rounded border">
                            <span class="fs-5 fw-bold">Tổng Thanh Toán:</span>
                            <span class="fs-4 fw-bold text-danger"><?= number_format($total_price) ?> đ</span>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-warning w-100 py-3 fw-bold fs-5 rounded-pill shadow-sm">
                            XÁC NHẬN ĐẶT HÀNG
                        </button>
                        
                        <div class="text-center mt-3 mb-0">
                            <a href="cart.php" class="text-decoration-none text-muted small"><i class="fas fa-arrow-left me-1"></i> Quay lại giỏ hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>

</body>
</html>