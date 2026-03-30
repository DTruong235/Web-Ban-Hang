<?php
require_once 'db.php';

// Lấy ID sản phẩm từ URL (ví dụ: product_detail.php?id=1)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Truy vấn lấy dữ liệu sản phẩm
$sql = "SELECT p.*, c.name as cat_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = $id AND p.status = 1";
$result = $conn->query($sql);

// Nếu không tìm thấy sản phẩm, chuyển hướng về trang chủ
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Bách Hóa Pew</title>
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
                        <li class="nav-item">
                            <a class="nav-link active-yellow" href="index.php">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Danh mục
                            </a>
                            <ul class="dropdown-menu custom-dropdown" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#">Rau củ</a></li>
                                <li><a class="dropdown-item" href="#">Bánh ngọt & Bánh mỳ</a></li>
                                <li><a class="dropdown-item" href="#">Nước ngọt</a></li>
                                <li><a class="dropdown-item" href="#">Thịt</a></li>
                                <li><a class="dropdown-item" href="#">Rượu & Bia</a></li>
                                <li><a class="dropdown-item" href="#">Tẩy rửa & Vệ sinh</a></li>

                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Liện hệ</a>
                        </li>
                    </ul>

                    <ul class="navbar-nav align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Đăng ký</a>
                        </li>
                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="cart.php" class="cart-icon position-relative text-white">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <?php
                                $cart_count = 0;
                                if (isset($_SESSION['cart'])) {
                                    foreach ($_SESSION['cart'] as $item) {
                                        $cart_count += $item['quantity']; // Cộng dồn số lượng các món
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

    <div class="container mt-4 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-tempi-green">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-tempi-green"><?= htmlspecialchars($product['cat_name']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>

    <section class="container my-4">
        <div class="bg-white p-4 p-md-5 rounded shadow-sm">
            <div class="row">

                <div class="col-md-5 mb-4 mb-md-0">
                    <div class="position-relative border rounded p-3 text-center" style="background-color: #f8f9fa;">
                        <?php if ($product['discount_price'] != NULL): ?>
                            <?php $percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>
                            <div class="badge-discount position-absolute top-0 end-0 m-3 bg-danger text-white px-2 py-1 rounded fs-6" style="z-index: 10;">
                                -<?= $percent ?>%
                            </div>
                        <?php endif; ?>

                        <img src="<?= $product['cover_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-detail-img img-fluid">
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <img src="<?= $product['cover_image'] ?>" class="thumbnail-img active" alt="Thumb">
                        <div class="thumbnail-img bg-light d-flex align-items-center justify-content-center text-muted border"><i class="fas fa-image"></i></div>
                        <div class="thumbnail-img bg-light d-flex align-items-center justify-content-center text-muted border"><i class="fas fa-image"></i></div>
                    </div>
                </div>

                <div class="col-md-7 ps-md-5">
                    <div class="stars text-warning mb-2 fs-5">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>

                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h2>

                    <div class="price-section mb-3">
                        <?php if ($product['discount_price'] != NULL): ?>
                            <span class="fs-2 fw-bold text-primary me-2"><?= number_format($product['discount_price']) ?> đ</span>
                            <span class="text-muted text-decoration-line-through fs-5"><?= number_format($product['price']) ?> đ</span>
                        <?php else: ?>
                            <span class="fs-2 fw-bold text-primary"><?= number_format($product['price']) ?> đ</span>
                        <?php endif; ?>
                    </div>

                    <p class="text-muted mb-4 small">Đã bán: <span class="text-dark fw-bold">128</span> (Dữ liệu giả lập)</p>

                    <div class="d-flex gap-3 mb-4">
                        <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ hàng">
                            <i class="fas fa-cart-plus fs-4"></i>
                        </a>
                        <!-- <button class="btn btn-tempi-green px-5 py-2 fw-bold text-white">
                            Mua ngay
                        </button> -->
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>&action=buy_now" class="btn btn-tempi-green flex-grow-1 text-white rounded-pill text-decoration-none text-center fw-bold shadow-sm">
                            Mua ngay
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="product-description">
                        <h5 class="fw-bold mb-3">Mô tả sản phẩm</h5>
                        <p class="text-muted" style="line-height: 1.8;">
                            <?php
                            if (!empty($product['description'])) {
                                echo nl2br(htmlspecialchars($product['description']));
                            } else {
                                echo "Sản phẩm này hiện tại chưa có mô tả chi tiết từ nhà cung cấp. Bách Hóa Pew cam kết cung cấp hàng chính hãng 100% với chất lượng tốt nhất.";
                            }
                            ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <footer class="footer-bg text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h3 class="fw-bold mb-3">Tempi</h3>
                    <p class="small">Giải pháp bán hàng trực tuyến toàn diện.</p>
                    <div class="social-icons">
                        <i class="fab fa-facebook me-2"></i>
                        <i class="fab fa-instagram me-2"></i>
                        <i class="fab fa-tiktok"></i>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Danh mục</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white text-decoration-none">Vegetables</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Meat</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Drinks</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Hỗ trợ</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-white text-decoration-none">Liên hệ</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Chính sách bảo mật</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Điều khoản</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Newsletter</h6>
                    <p class="small">Đăng ký để nhận ưu đãi mới nhất.</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Email của bạn">
                        <button class="btn btn-warning" type="button">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hàm xử lý chạy ngầm
        function addToCart(productId) {
            // Gọi ngầm tới file php kèm theo tín hiệu ajax=1
            fetch(`add_to_cart.php?id=${productId}&ajax=1`)
                .then(response => response.json()) // Nhận dữ liệu JSON trả về
                .then(data => {
                    if (data.status === 'success') {
                        // Tìm con số màu đỏ và cập nhật giá trị mới
                        let badge = document.getElementById('cart-badge');
                        badge.innerText = data.cart_count;

                        // Tùy chọn: Thêm hiệu ứng rung nhẹ báo hiệu cho khách biết đã thêm thành công
                        badge.classList.add('animate__animated', 'animate__rubberBand');
                        setTimeout(() => {
                            badge.classList.remove('animate__animated', 'animate__rubberBand');
                        }, 1000);
                    }
                })
                .catch(error => console.error('Lỗi thêm giỏ hàng:', error));
        }
    </script>
</body>

</html>