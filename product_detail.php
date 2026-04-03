<?php
require_once 'db.php';
require_once('getRelatedProducts.php');

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

$current_id = $product['id'];
$cat_id = $product['category_id'];
$current_price = $product['price'];

// Lấy sản phẩm tương tự
$related_products = getRelatedProducts($conn, $current_id, $cat_id, $current_price);

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
                            <a class="nav-link" href="about.php">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Liên hệ</a>
                        </li>
                    </ul>
                    <!-- Đăng nhập | Đăng ký -->
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

        <?php
        // Xử lý đánh giá sản phẩm
        if (isset($_POST['submit_review']) && isset($_SESSION['user_id'])) {
            $rating = (int)$_POST['rating'];
            $comment = trim($_POST['comment']);
            if ($rating >= 1 && $rating <= 5) {
                $uid = (int)$_SESSION['user_id'];
                $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, status) VALUES (?, ?, ?, ?, 0)");
                $stmt->bind_param('iiis', $current_id, $uid, $rating, $comment);
                $stmt->execute();
                $stmt->close();
                $review_success = 'Cảm ơn bạn đã đánh giá! Đánh giá đang chờ duyệt.';
            } else {
                $review_error = 'Vui lòng chọn số sao hợp lệ (1-5).';
            }
        }

        // Lấy đánh giá đã duyệt
        $reviews = [];
        $rev_res = $conn->query("SELECT r.*, u.fullname FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id = $current_id AND r.status = 1 ORDER BY r.created_at DESC");
        if ($rev_res && $rev_res->num_rows > 0) {
            while ($r = $rev_res->fetch_assoc()) {
                $reviews[] = $r;
            }
        }
        ?>

        <div class="mt-4">
            <h4 class="fw-bold">Đánh giá khách hàng</h4>
            <?php if (isset($review_success)): ?><div class="alert alert-success"><?= $review_success ?></div><?php endif; ?>
            <?php if (isset($review_error)): ?><div class="alert alert-danger"><?= $review_error ?></div><?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Chấm sao</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Chọn sao</option>
                            <option value="5">★★★★★</option>
                            <option value="4">★★★★</option>
                            <option value="3">★★★</option>
                            <option value="2">★★</option>
                            <option value="1">★</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Bình luận (tuỳ chọn)</label>
                        <textarea name="comment" class="form-control" rows="2" placeholder="Chia sẻ cảm nhận của bạn..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="submit_review" class="btn btn-success">Gửi đánh giá</button>
                    </div>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-info">Vui lòng đăng nhập để đánh giá sản phẩm.</div>
            <?php endif; ?>

            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong><?= htmlspecialchars($review['fullname'] ?: 'Khách') ?></strong>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($review['created_at'])) ?></small>
                        </div>
                        <div class="mb-2" style="color:#ffb400;"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5-$review['rating']) ?></div>
                        <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($review['comment'])) ?: '<em>Không có bình luận</em>' ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Chưa có đánh giá nào được duyệt.</p>
            <?php endif; ?>
        </div>

        <?php if (!empty($related_products) && count($related_products) > 0): ?>
            <div class="mt-5">
                <h3>Sản phẩm bạn có thể thích</h3>
                <?php
                    $showedCount = 10;
                    $loadMoreStep = 10;
                ?>
                <div id="related-container" class="related-carousel pt-2 pb-2" style="display:flex; gap: 12px; overflow-x: auto; overflow-y: hidden; padding-bottom: 8px;">
                    <?php foreach ($related_products as $index => $item): ?>
                        <div class="related-item card shadow-sm border-0" style="flex: 0 0 240px;" data-related-item>
                            <a href="product_detail.php?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                                <div style="height: 170px; overflow: hidden;">
                                    <img src="<?= $item['cover_image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 100%; height: 100%; object-fit: contain; background-color: #fff;" />
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title fs-6 mb-1" style="min-height: 40px;"><?= htmlspecialchars($item['name']) ?></h6>
                                    <p class="text-danger fw-bold mb-2" style="font-size: 0.95rem;"><?= number_format($item['price']) ?> đ</p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <style>
                .related-carousel::-webkit-scrollbar { height: 8px; }
                .related-carousel::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 4px; }
                .related-carousel::-webkit-scrollbar-track { background: rgba(255,255,255,0.7); }
            </style>
        <?php endif; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const loadMoreBtn = document.getElementById('btn-load-more');
                if (!loadMoreBtn) return;

                loadMoreBtn.addEventListener('click', function() {
                    const hiddenItems = document.querySelectorAll('[data-related-item][style*="display: none"]');
                    const showNext = 10; // hiển thị thêm 10
                    let shown = 0;

                    hiddenItems.forEach(function(item) {
                        if (shown < showNext) {
                            item.style.display = 'block';
                            shown++;
                        }
                    });

                    if (document.querySelectorAll('[data-related-item][style*="display: none"]').length === 0) {
                        loadMoreBtn.style.display = 'none';
                    }
                });
            });
        </script>
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