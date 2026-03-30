<?php
require_once 'db.php';

// 1. Bắt từ khóa tìm kiếm từ URL
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$escaped_keyword = $conn->real_escape_string($keyword);

// 2. Truy vấn tìm kiếm sản phẩm theo tên
// Lọc các sản phẩm đang được bán (status = 1) và tên có chứa từ khóa
$sql_search = "SELECT * FROM products WHERE LOWER(name) LIKE BINARY LOWER('%$escaped_keyword%') AND status = 1 ORDER BY id DESC";
$result_search = $conn->query($sql_search);

// 3. Lấy số lượng giỏ hàng cho Header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm: <?= htmlspecialchars($keyword) ?> - Bách Hóa Pew</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

    <header class="header-bg sticky-top shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark py-3">
                <a class="navbar-brand fw-bold fs-3" href="index.php">Bách Hóa Pew</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    
                    <form action="search.php" method="GET" class="d-flex mx-auto w-100 px-3" style="max-width: 600px;">
                        <div class="input-group shadow-sm">
                            <input type="text" name="keyword" class="form-control border-0 px-3 py-2" placeholder="Bạn muốn tìm sản phẩm gì?..." 
                                   value="<?= htmlspecialchars($keyword) ?>" required>
                            <button class="btn btn-warning text-dark fw-bold px-4" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <ul class="navbar-nav align-items-center">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold text-warning" href="#" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle fs-5 me-1"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                    <?php if($_SESSION['user_role'] == 1): ?>
                                        <li><a class="dropdown-item fw-bold text-success" href="admin/products.php"><i class="fas fa-shield-alt me-2"></i> Vào trang Admin</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-box me-2"></i> Đơn hàng của tôi</a></li>
                                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item ms-lg-2">
                            <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#searchModal" title="Tìm kiếm">
                                <i class="fas fa-search fs-5"></i>
                            </a>
                        </li>

                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="cart.php" class="cart-icon position-relative text-white">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <?php
                                // Tính toán giỏ hàng ngay tại đây để dễ copy sang trang khác
                                $cart_count = 0;
                                if (isset($_SESSION['cart'])) {
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

    <section class="container my-5 min-vh-100">
        
        <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
            <h3 class="fw-bold mb-0">
                Kết quả tìm kiếm cho: <span class="text-tempi-green">"<?= htmlspecialchars($keyword) ?>"</span>
            </h3>
            <span class="text-muted fw-bold">Tìm thấy <?= $result_search ? $result_search->num_rows : 0 ?> sản phẩm</span>
        </div>

        <div class="row">
            <?php if ($result_search && $result_search->num_rows > 0): ?>
                <?php while ($product = $result_search->fetch_assoc()): ?>
                    <?php
                        $original_price = $product['price'];
                        $final_price = ($product['discount_price'] != NULL) ? $product['discount_price'] : $original_price;
                    ?>
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                        <div class="card product-card h-100 border-0 shadow-sm position-relative">
                            
                            <?php if ($product['discount_price'] != NULL): ?>
                                <?php $percent = round((($original_price - $final_price) / $original_price) * 100); ?>
                                <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded" style="z-index: 10; font-size: 0.75rem;">
                                    -<?= $percent ?>%
                                </div>
                            <?php endif; ?>

                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                <div class="product-img-wrapper">
                                    <img src="<?= $product['cover_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-cover">
                                </div>
                            </a>

                            <div class="card-body text-center d-flex flex-column p-2">
                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none flex-grow-1">
                                    <h6 class="card-title text-dark" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($product['name']) ?>">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </h6>
                                </a>
                                <div class="mt-2">
                                    <?php if ($product['discount_price'] != NULL): ?>
                                        <div class="text-muted text-decoration-line-through small"><?= number_format($original_price) ?> đ</div>
                                    <?php endif; ?>
                                    <div class="fw-bold text-danger mb-2"><?= number_format($final_price) ?> đ</div>
                                    <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="btn btn-sm btn-tempi-green w-100 text-white rounded-pill text-decoration-none text-center shadow-sm">
                                        Chọn mua
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" alt="Not found" style="width: 120px; opacity: 0.5;" class="mb-3">
                    <h5 class="text-muted fw-bold">Rất tiếc, không tìm thấy sản phẩm nào!</h5>
                    <p class="text-muted">Vui lòng thử lại bằng các từ khóa khác chung chung hơn (VD: Mì, Sữa, Bia...)</p>
                    <a href="index.php" class="btn btn-outline-success mt-3 rounded-pill px-4">Quay lại Trang Chủ</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
        function addToCart(productId) {
            fetch(`add_to_cart.php?id=${productId}&ajax=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('cart-badge').innerText = data.cart_count;
                    }
                })
                .catch(error => console.error('Lỗi thêm giỏ hàng:', error));
        }
    </script>
</body>
</html>