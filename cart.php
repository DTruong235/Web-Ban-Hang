<?php
require_once 'db.php'; // Đã có session_start()

// ===============================================
// 1. XỬ LÝ XÓA SẢN PHẨM KHỎI GIỎ
// ===============================================
if (isset($_GET['remove'])) {
    $id_to_remove = (int)$_GET['remove'];
    
    if (isset($_SESSION['user_id'])) {
        // Xóa trong Database nếu đã đăng nhập
        $uid = $_SESSION['user_id'];
        $conn->query("DELETE FROM cart WHERE user_id = $uid AND product_id = $id_to_remove");
    } else {
        // Xóa trong Session nếu là khách
        if (isset($_SESSION['cart'][$id_to_remove])) {
            unset($_SESSION['cart'][$id_to_remove]);
        }
    }
    header("Location: cart.php");
    exit();
}

// ===============================================
// 2. XỬ LÝ CẬP NHẬT SỐ LƯỢNG KHI BẤM "CẬP NHẬT GIỎ HÀNG"
// ===============================================
if (isset($_POST['update_cart']) && isset($_POST['qty'])) {
    if (isset($_SESSION['user_id'])) {
        // Cập nhật Database nếu đã đăng nhập
        $uid = $_SESSION['user_id'];
        foreach ($_POST['qty'] as $id => $quantity) {
            $id = (int)$id; $quantity = (int)$quantity;
            if ($quantity <= 0) {
                $conn->query("DELETE FROM cart WHERE user_id = $uid AND product_id = $id");
            } else {
                $conn->query("UPDATE cart SET quantity = $quantity WHERE user_id = $uid AND product_id = $id");
            }
        }
    } else {
        // Cập nhật Session nếu là khách
        foreach ($_POST['qty'] as $id => $quantity) {
            $id = (int)$id; $quantity = (int)$quantity;
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

// ===============================================
// 3. XUẤT DỮ LIỆU RA GIỎ HÀNG ĐỂ HIỂN THỊ
// ===============================================
$cart = [];
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    // A. LẤY TỪ DATABASE CHO TÀI KHOẢN ĐÃ ĐĂNG NHẬP
    $uid = (int)$_SESSION['user_id'];
    $sql = "SELECT c.product_id, c.quantity, p.name, p.price, p.discount_price, p.cover_image 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = $uid";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pid = $row['product_id'];
            // Nếu có giá giảm thì lấy giá giảm, không thì lấy giá gốc
            $final_price = ($row['discount_price'] != NULL) ? $row['discount_price'] : $row['price'];
            
            $cart[$pid] = [
                'product_id' => $pid,
                'name' => $row['name'],
                'price' => $final_price,
                'quantity' => $row['quantity'],
                'image' => $row['cover_image']
            ];
            $cart_count += $row['quantity'];
        }
    }
} else {
    // B. LẤY TỪ SESSION CHO KHÁCH VÃNG LAI
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Lấy danh sách các ID sản phẩm trong giỏ để truy vấn lấy tên, ảnh, giá
        $product_ids = implode(',', array_keys($_SESSION['cart']));
        $sql = "SELECT id, name, price, discount_price, cover_image FROM products WHERE id IN ($product_ids)";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pid = $row['id'];
                $final_price = ($row['discount_price'] != NULL) ? $row['discount_price'] : $row['price'];
                
                $cart[$pid] = [
                    'product_id' => $pid,
                    'name' => $row['name'],
                    'price' => $final_price,
                    'quantity' => $_SESSION['cart'][$pid]['quantity'],
                    'image' => $row['cover_image']
                ];
                $cart_count += $_SESSION['cart'][$pid]['quantity'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn - Bách Hóa Pew</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <header class="header-bg sticky-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark py-3">
                <a class="navbar-brand fw-bold fs-3" href="index.php">Bách Hóa Pew</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto align-items-center">
                        <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Danh mục</a></li>
                    </ul>
                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="cart.php" class="cart-icon position-relative text-warning">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <?php
                                    // --- LOGIC ĐẾM SỐ LƯỢNG MỚI ---
                                $cart_count = 0;
                                if (isset($_SESSION['user_id'])) {
                                    // Đã đăng nhập: Đếm trong database
                                    $uid = (int)$_SESSION['user_id'];
                                    $c_res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
                                    if ($c_res && $c_row = $c_res->fetch_assoc()) {
                                        $cart_count = $c_row['total'] ?? 0;
                                    }
                                } else if (isset($_SESSION['cart'])) {
                                    // Chưa đăng nhập: Đếm trong session
                                    foreach ($_SESSION['cart'] as $item) {
                                        $cart_count += $item['quantity'];
                                    }
                                }
                                ?>
                                <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?= $cart_count ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <section class="container my-5">
        <h2 class="fw-bold mb-4">Giỏ hàng của bạn</h2>

        <?php if (empty($cart)): ?>
            <div class="text-center bg-white p-5 rounded shadow-sm">
                <img src="https://cdn-icons-png.flaticon.com/512/11329/11329060.png" alt="Empty Cart" style="width: 150px; opacity: 0.5;" class="mb-4">
                <h4 class="text-muted">Giỏ hàng của bạn đang trống</h4>
                <p class="text-muted mb-4">Có vẻ như bạn chưa chọn mua sản phẩm nào.</p>
                <a href="index.php" class="btn btn-tempi-green px-4 py-2 text-white fw-bold rounded-pill">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            
            <form method="POST" id="cart-form">
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">
                                                <input class="form-check-input" type="checkbox" id="check-all" checked style="transform: scale(1.2); cursor: pointer;">
                                            </th>
                                            <th>Sản phẩm</th>
                                            <th class="text-center">Đơn giá</th>
                                            <th class="text-center" style="width: 120px;">Số lượng</th>
                                            <th class="text-center">Thành tiền</th>
                                            <th class="text-center"><i class="fas fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cart as $id => $item): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input class="form-check-input item-check" type="checkbox" name="selected_items[]" value="<?= $id ?>" data-price="<?= $item['price'] ?>" checked style="transform: scale(1.2); cursor: pointer;">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= $item['image'] ?>" alt="Img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" class="me-3 border">
                                                        <div>
                                                            <a href="product_detail.php?id=<?= $id ?>" class="text-dark text-decoration-none fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                                <?= htmlspecialchars($item['name']) ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center text-muted"><?= number_format($item['price']) ?>đ</td>
                                                <td class="text-center">
                                                    <input type="number" name="qty[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control form-control-sm text-center qty-input">
                                                </td>
                                                <td class="text-center fw-bold text-danger item-subtotal">
                                                    <?= number_format($item['price'] * $item['quantity']) ?>đ
                                                </td>
                                                <td class="text-center">
                                                    <a href="cart.php?remove=<?= $id ?>" class="text-danger" onclick="return confirm('Bỏ sản phẩm này khỏi giỏ hàng?');" title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Mua thêm</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="bg-white p-4 rounded shadow-sm border-top border-warning border-4 sticky-top" style="top: 90px;">
                            <h5 class="fw-bold mb-4">Tóm tắt đơn hàng</h5>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Tạm tính (<span id="selected-count">0</span> món):</span>
                                <span class="fw-bold" id="total-price">0 đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Phí giao hàng:</span>
                                <span class="text-success fw-bold">Miễn phí</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <span class="fs-5 fw-bold">Tổng cộng:</span>
                                <span class="fs-4 fw-bold text-danger" id="final-price">0 đ</span>
                            </div>

                            <button type="submit" formaction="checkout.php" name="proceed_checkout" class="btn btn-warning w-100 py-3 fw-bold fs-5 rounded-pill shadow-sm" id="btn-checkout">
                                TIẾN HÀNH THANH TOÁN
                            </button>
                            
                            <div class="text-center mt-3 small text-muted">
                                <i class="fas fa-shield-alt text-success me-1"></i> Thanh toán an toàn & bảo mật
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <footer class="footer-bg text-white pt-5 pb-3 mt-5">
        <div class="container text-center">
            <h3 class="fw-bold mb-3">Bách Hóa Pew</h3>
            <p class="small">Giải pháp bán hàng trực tuyến toàn diện.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('check-all');
            const itemChecks = document.querySelectorAll('.item-check');
            const qtyInputs = document.querySelectorAll('.qty-input');
            const totalPriceDisplay = document.getElementById('total-price');
            const finalPriceDisplay = document.getElementById('final-price');
            const selectedCountDisplay = document.getElementById('selected-count');
            const btnCheckout = document.getElementById('btn-checkout');

            // Hàm tính toán tổng tiền dựa trên các ô được tích
            function calculateTotal() {
                let total = 0;
                let count = 0;

                itemChecks.forEach(function(check) {
                    if (check.checked) {
                        count++;
                        let row = check.closest('tr');
                        let price = parseFloat(check.dataset.price);
                        let qty = parseInt(row.querySelector('.qty-input').value);
                        
                        // Cập nhật thành tiền từng dòng trên giao diện
                        let subtotal = price * qty;
                        row.querySelector('.item-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
                        
                        total += subtotal;
                    }
                });

                // Cập nhật tổng tiền chung
                let formattedTotal = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
                totalPriceDisplay.textContent = formattedTotal;
                finalPriceDisplay.textContent = formattedTotal;
                selectedCountDisplay.textContent = count;

                // Khóa nút thanh toán nếu không có món nào được chọn
                btnCheckout.disabled = (count === 0);
            }

            // Bắt sự kiện khi click ô "Chọn tất cả"
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    itemChecks.forEach(c => c.checked = checkAll.checked);
                    calculateTotal();
                });
            }

            // Bắt sự kiện khi click từng ô checkbox hoặc đổi số lượng
            itemChecks.forEach(c => c.addEventListener('change', function() {
                // Nếu có 1 ô bỏ tích, ô "Chọn tất cả" cũng phải bỏ tích
                if (!this.checked) checkAll.checked = false;
                
                // Nếu tất cả đều được tích, ô "Chọn tất cả" tự động tích
                const allChecked = Array.from(itemChecks).every(i => i.checked);
                if (allChecked) checkAll.checked = true;

                calculateTotal();
            }));

            // Tự động cập nhật số lượng khi thay đổi + tính lại tổng tiền
            qtyInputs.forEach(q => {
                q.addEventListener('change', function() {
                    const productId = this.name.match(/\d+/)[0];
                    const newQty = parseInt(this.value);
                    
                    // Gọi AJAX để cập nhật vào DB/Session
                    fetch('update_cart_ajax.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'product_id=' + productId + '&quantity=' + newQty
                    })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Cập nhật:', data);
                    })
                    .catch(err => console.error('Lỗi:', err));
                    
                    calculateTotal();
                });
                
                q.addEventListener('keyup', calculateTotal);
            });

            // Chạy tính toán lần đầu khi load trang
            if (itemChecks.length > 0) calculateTotal();
        });
    </script>
</body>
</html>