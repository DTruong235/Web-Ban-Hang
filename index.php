<?php
require_once 'db.php';
// 1. Lấy danh sách 8 "Hàng Mới Về" (Sản phẩm mới nhất dựa vào ID)
$sql_new = "SELECT * FROM products WHERE status = 1 ORDER BY id DESC LIMIT 8";
$result_new = $conn->query($sql_new);
// 2. Lấy danh sách "Sản phẩm Giảm Giá" (Flash Sale / Best Seller)
// Lọc các sản phẩm có discount_price không bị NULL
$sql_sale = "SELECT * 
             FROM products
             WHERE status = 1 AND discount_price IS NOT NULL 
             ORDER BY ((price - discount_price) / price) DESC 
             LIMIT 4";
$result_sale = $conn->query($sql_sale);
// 3. Lấy sản phẩm danh mục "Nước Ngọt" (Giả sử ID danh mục Nước Ngọt trong DB của bạn là 6)
// Lấy 3 sản phẩm để nhét vừa layout ngang
$sql_drinks = "SELECT * FROM products WHERE category_id = 6 AND status = 1 LIMIT 6";
$result_drinks = $conn->query($sql_drinks);
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bách Hóa Pew</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <header class="header-bg sticky-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark py-3">
                <a class="navbar-brand fw-bold fs-3" href="#">Bách Hóa Pew</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link active-yellow" href="#">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Danh mục
                            </a>
                            <ul class="dropdown-menu custom-dropdown" aria-labelledby="navbarDropdown">
                                <?php
                                // Lấy danh sách danh mục từ Database để in ra menu
                                $menu_sql = "SELECT id, name FROM categories WHERE status = 1";
                                $menu_res = $conn->query($menu_sql);
                                if ($menu_res && $menu_res->num_rows > 0) {
                                    while ($cat = $menu_res->fetch_assoc()) {
                                        echo '<li><a class="dropdown-item" href="category.php?id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
                                    }
                                }
                                ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Liện hệ</a>
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
                                /// --- LOGIC ĐẾM SỐ LƯỢNG MỚI ---
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
    <!-- Banner -->
    <section id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-wrapper">
                    <img src="./image/banner_head.webp" alt="Banner 1" class="img-cover">
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-wrapper">
                    <img src="./image/banner_head2.webp" alt="Banner 2" class="img-cover">
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </section>
    <!-- Danh Mục -->
    <section class="container my-5">
        <h2 class="text-center mb-4 fw-bold">Danh mục</h2>
        <div class="row text-center category-row justify-content-center">
            
            <?php
            // Truy vấn lấy tất cả các danh mục đang có trạng thái "Hiển thị"
            $sql_cats = "SELECT * FROM categories WHERE status = 1";
            $result_cats = $conn->query($sql_cats);
            
            if ($result_cats && $result_cats->num_rows > 0):
                while ($cat = $result_cats->fetch_assoc()):
                    
                    // Tạo bộ lọc ảnh: Trùng ID cũ thì lấy ảnh cũ, danh mục mới thì lấy ảnh mặc định
                    $cat_images = [
                        1 => './image/raucai.webp',
                        2 => './image/banhmi.webp',
                        3 => './image/rượu.webp',
                        4 => './image/sua_egg.webp',
                        5 => './image/meat.webp',
                        6 => './image/drink.webp',
                        7 => './image/cleanning.webp',
                        8 => './image/snack.webp',
                        9 => './image/Mỳ/mi-an-lien_202511010301464949.png',
                        10 => './image/Đồ hộp/ca-hop_202508291625395702 (1).png'
                    ];
                    
                    // Nếu ID danh mục có trong danh sách trên thì lấy ảnh đó, không có thì lấy ảnh mặc định
                    $img_src = isset($cat_images[$cat['id']]) ? $cat_images[$cat['id']] : 'https://cdn-icons-png.flaticon.com/512/679/679821.png'; 
            ?>
            
            <div class="col-6 col-md-2 mb-3">
                <a href="category.php?id=<?= $cat['id'] ?>" class="text-decoration-none text-dark">
                    <div class="category-item">
                        <div class="category-wrapper mb-2">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="img-cover p-2">
                        </div>
                        <span class="small fw-semibold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </span>
                    </div>
                </a>
            </div>

            <?php 
                endwhile;
            else: 
            ?>
                <p class="text-center text-muted">Đang cập nhật danh mục...</p>
            <?php endif; ?>

        </div>
    </section>
    <!-- Best Seller -->
    <section class="container my-5">
        <h2 class="text-center mb-4 fw-bold">Best Seller (Đang Giảm Giá)</h2>
        <div class="row" id="sale-product-list">
            <?php if ($result_sale && $result_sale->num_rows > 0): ?>
                <?php while ($product = $result_sale->fetch_assoc()): ?>

                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card product-card h-100 border-0 shadow-sm position-relative">

                            <?php
                            $percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100);
                            ?>
                            <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded" style="z-index: 10;">
                                -<?= $percent ?>%
                            </div>

                            <!-- Chi Tiết Sản Phẩm -->
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                <div class="product-img-wrapper">
                                    <img src="<?= $product['cover_image'] ?>" alt="<?= $product['name'] ?>" class="img-cover">
                                </div>
                            </a>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">

                            </a>

                            <div class="card-body text-center">
                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                    <h6 class="card-title fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 40px;" title="<?= htmlspecialchars($product['name']) ?>">
                                        <?= htmlspecialchars($product['name']) ?></h6>
                                </a>
                                <!-- Hiển Thị Suất Còn Lại -->
                                <!-- <div class="text-muted text-decoration-line-through small"><?= number_format($product['price']) ?> đ</div>
                                <div class="fw-bold text-dark mb-2"><?= number_format($product['discount_price']) ?> đ</div>
                                <div class="stars text-warning mb-3">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                </div>
                                <div class="d-flex justify-content-center align-items-center gap-3 mt-2">
                                    <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ hàng">
                                        <i class="fas fa-cart-plus fs-4"></i>
                                    </a> -->
                                <div class="text-muted text-decoration-line-through small"><?= number_format($product['price']) ?> đ</div>
                                <div class="fw-bold text-dark mb-2"><?= number_format($product['discount_price']) ?> đ</div>

                                <div class="text-center fw-bold mb-1 mt-1" style="font-size: 0.75rem; color: #ff7f00;">
                                    Tối đa 5 sp/đơn
                                </div>
                                <div class="position-relative mb-3 mx-auto" style="max-width: 90%;">
                                    <div class="progress" style="height: 22px; border-radius: 50px; background-color: #fef0cd;">
                                        <?php
                                        $stock = $product['stock_quantity'];
                                        $percent = ($stock <= 100) ? $stock : 50;
                                        ?>
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%; border-radius: 50px;"></div>
                                    </div>
                                    <div class="position-absolute top-50 start-50 translate-middle w-100 text-center" style="font-size: 0.8rem; font-weight: 600; color: #333; line-height: 22px;">
                                        Còn <?= $stock ?> suất
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center align-items-center gap-3 mt-2">
                                    <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ hàng">
                                        <i class="fas fa-cart-plus fs-4"></i>
                                    </a>
                                    <!--  -->
                                    <a href="add_to_cart.php?id=<?= $product['id'] ?>&action=buy_now" class="btn btn-tempi-green flex-grow-1 text-white rounded-pill text-decoration-none text-center fw-bold shadow-sm">
                                        Mua ngay
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Chưa có sản phẩm giảm giá.</p>
            <?php endif; ?>
        </div>
        <!-- Nút Đọc Thêm -->
        <div class="text-center mt-3">
            <button class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold"
                onclick="loadMoreProducts('sale', this)" data-offset="4">
                Xem thêm sản phẩm
                <i class="fas fa-chevron-down ms-1"></i>
            </button>
            <button id="collapse-btn-sale" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold ms-2" onclick="collapseProducts('sale', 4)" style="display: none;">
                Thu gọn <i class="fas fa-chevron-up ms-1"></i>
            </button>
        </div>
    </section>
    <!-- Sale Cuối Tuần -->
    <section class="container-fluid banner-mid p-0 my-5">
        <div class="mid-banner-wrapper">
            <div class="mid-banner-overlay"></div>

            <img src="https://via.placeholder.com/1920x300?text=Banner+Deal" alt="Deal of the week" class="img-cover position-absolute top-0 start-0 w-100 h-100" style="z-index: 0;">

            <div class="mid-banner-content text-center text-white">
                <h4 class="text-uppercase mb-2">Siêu sale cuối tuần</h4>
                <h1 class="display-5 fw-bold mb-4">GIẢM ĐẾN 40%<br>THỰC PHẨM TƯƠI SỐNG</h1>
                <button class="btn btn-warning rounded-pill px-4 py-2 fw-bold">Mua ngay</button>
            </div>
        </div>
    </section>
    <!-- Deal Cuối Tuần - Giảm giá nước -->
    <section class="container my-5 py-4 bg-light rounded shadow-sm">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-12 mb-4 mb-lg-0 text-center text-lg-start px-4">
                <h4 class="text-uppercase mb-2 text-muted" style="letter-spacing: 1px;">Ưu đãi độc quyền</h4>
                <h1 class="display-4 fw-bold mb-0 text-warning">-30%</h1>
                <h2 class="h3 fw-bold mb-4">Các Loại Nước</h2>
                <a href="#" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark">Xem tất cả</a>
            </div>

            <div class="col-lg-9 col-md-12">
                <div class="row justify-content-start">
                    <?php if ($result_drinks && $result_drinks->num_rows > 0): ?>
                        <?php while ($product = $result_drinks->fetch_assoc()): ?>

                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="card product-card h-100 border-0 shadow-sm position-relative">

                                    <?php if ($product['discount_price'] != NULL): ?>
                                        <?php $percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>
                                        <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded" style="z-index: 10;">
                                            -<?= $percent ?>%
                                        </div>
                                    <?php endif; ?>
                                    <!-- Chi Tiết Sản Phẩm -->
                                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                        <div class="product-img-wrapper">
                                            <img src="<?= $product['cover_image'] ?>" alt="<?= $product['name'] ?>" class="img-cover">
                                        </div>
                                    </a>

                                    <div class="card-body text-center">
                                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                            <h6 class="card-title fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 40px;" title="<?= htmlspecialchars($product['name']) ?>">
                                                <?= htmlspecialchars($product['name']) ?></h6>
                                        </a>

                                        <?php if ($product['discount_price'] != NULL): ?>
                                            <div class="text-muted text-decoration-line-through small"><?= number_format($product['price']) ?> đ</div>
                                            <div class="fw-bold text-dark mb-2"><?= number_format($product['discount_price']) ?> đ</div>
                                        <?php else: ?>
                                            <div class="text-muted small">&nbsp;</div>
                                            <div class="fw-bold text-dark mb-2"><?= number_format($product['price']) ?> đ</div>
                                        <?php endif; ?>

                                        <div class="stars text-warning mb-3">
                                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center gap-3 mt-2">
                                            <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ hàng">
                                                <i class="fas fa-cart-plus fs-4"></i>
                                            </a>

                                            <a href="add_to_cart.php?id=<?= $product['id'] ?>&action=buy_now" class="btn btn-tempi-green flex-grow-1 text-white rounded-pill text-decoration-none text-center fw-bold shadow-sm">
                                                Mua ngay
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Đang cập nhật sản phẩm...</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

    <!-- Hàng Hóa Mới Về -->
    <section class="container-fluid my-5 bg-primary py-4">
        <h2 class="text-center mb-4 fw-bold" style="color: white">Hàng Hóa Mới Về</h2>
        <div class="container">
            <div class="row" id="new-product-list">
                <?php if ($result_new && $result_new->num_rows > 0): ?>
                    <?php while ($product = $result_new->fetch_assoc()): ?>

                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card product-card h-100 border-0 shadow-sm position-relative">

                                <?php if ($product['discount_price'] != NULL): ?>
                                    <?php $percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100); ?>
                                    <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded" style="z-index: 10;">
                                        -<?= $percent ?>%
                                    </div>
                                <?php endif; ?>

                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                    <div class="product-img-wrapper">
                                        <img src="<?= $product['cover_image'] ?>" alt="<?= $product['cover_image'] ?>" class="img-cover">
                                    </div>
                                </a>

                                <div class="card-body text-center">
                                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                                        <h6 class="card-title fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 40px;" title="<?= htmlspecialchars($product['name']) ?>">
                                            <?= htmlspecialchars($product['name']) ?></h6>
                                    </a>

                                    <?php if ($product['discount_price'] != NULL): ?>
                                        <div class="text-muted text-decoration-line-through small"><?= number_format($product['price']) ?> đ</div>
                                        <div class="fw-bold text-dark mb-2"><?= number_format($product['discount_price']) ?> đ</div>
                                    <?php else: ?>
                                        <div class="text-muted small">&nbsp;</div>
                                        <div class="fw-bold text-dark mb-2"><?= number_format($product['price']) ?> đ</div>
                                    <?php endif; ?>

                                    <div class="stars text-warning mb-3">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <div class="d-flex justify-content-center align-items-center gap-3 mt-2">
                                        <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ hàng">
                                            <i class="fas fa-cart-plus fs-4"></i>
                                        </a>

                                        <a href="add_to_cart.php?id=<?= $product['id'] ?>&action=buy_now" class="btn btn-tempi-green flex-grow-1 text-white rounded-pill text-decoration-none text-center fw-bold shadow-sm">
                                            Mua ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <!-- Nút Đọc Thêm -->
            <div class="text-center mt-3">
                <button class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold" onclick="loadMoreProducts('new', this)" data-offset="8">
                    Xem thêm sản phẩm <i class="fas fa-chevron-down ms-1"></i>
                </button>
                <button id="collapse-btn-new" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold ms-2" onclick="collapseProducts('new', 8)" style="display: none;">
                    Thu gọn <i class="fas fa-chevron-up ms-1"></i>
                </button>
            </div>
        </div>
    </section>
    <section class="container-fluid banner-mid p-0 my-5">
        <div class="mid-banner-wrapper">
            <div class="mid-banner-overlay"></div>

            <img src="./image/Bánh/cate-mobile-compressed_202603060835210894.jpg" alt="Deal of the week" class="img-cover position-absolute top-0 start-0 w-100 h-100" style="z-index: 999;">
        </div>
    </section>
    <section class="container my-5 py-4 border-bottom">
        <div class="row text-center">
            <div class="col-md-3 col-sm-6 mb-4">
                <div class="usp-icon-wrapper mb-3">
                    <i class="fas fa-truck-fast text-tempi-green fs-1 position-relative">
                        <span class="position-absolute text-warning" style="font-size: 0.5rem; top: 0; right: -5px;"><i class="fas fa-circle"></i></span>
                    </i>
                </div>
                <h5 class="fw-bold mb-3">Giao Hàng Hỏa Tốc</h5>
                <p class="text-muted small">Nhận hàng chỉ trong 2 giờ. Miễn phí vận chuyển cho các đơn hàng từ 300.000đ trong nội thành.</p>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="usp-icon-wrapper mb-3">
                    <i class="fas fa-location-crosshairs text-tempi-green fs-1 position-relative">
                        <span class="position-absolute text-warning" style="font-size: 0.5rem; top: 0; right: -5px;"><i class="fas fa-circle"></i></span>
                    </i>
                </div>
                <h5 class="fw-bold mb-3">Giao Hàng Tận Nơi</h5>
                <p class="text-muted small">Phủ sóng giao hàng rộng khắp mọi khu vực. Đảm bảo thực phẩm luôn giữ được độ tươi ngon nhất.</p>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="usp-icon-wrapper mb-3">
                    <i class="fas fa-headset text-tempi-green fs-1 position-relative"></i>
                </div>
                <h5 class="fw-bold mb-3">Hỗ Trợ 24/7</h5>
                <p class="text-muted small">Đội ngũ chăm sóc khách hàng luôn túc trực để giải đáp thắc mắc và xử lý đơn hàng nhanh chóng.</p>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <div class="usp-icon-wrapper mb-3">
                    <i class="fas fa-check text-tempi-green fs-1 position-relative">
                        <span class="position-absolute text-warning" style="font-size: 0.5rem; top: -5px; right: -10px;"><i class="fas fa-check"></i></span>
                    </i>
                </div>
                <h5 class="fw-bold mb-3">100% Chính Hãng</h5>
                <p class="text-muted small">Cam kết hoàn tiền 200% nếu phát hiện hàng giả, hàng kém chất lượng. Nguồn gốc xuất xứ rõ ràng.</p>
            </div>
        </div>
    </section>

    <section class="container-fluid bg-light py-5 mb-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">

                <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
                    <div class="bg-white p-5 rounded shadow-sm h-100">
                        <h3 class="fw-bold mb-4">
                            ADD SOME <span class="text-warning">OFFERS</span> TO YOUR <span class="text-warning">INBOX</span>!
                        </h3>
                        <p class="text-muted small mb-4">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.
                        </p>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <input type="email" class="form-control form-control-lg bg-light border-0" placeholder="Your email" required>
                            </div>
                            <button type="submit" class="btn btn-tempi-green text-white px-4 py-2 fw-semibold">
                                <i class="fas fa-envelope-open-text me-2"></i> Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="newsletter-img-wrapper ms-lg-4 position-relative">
                        <img src="./image/Bánh/cate-mobile-compressed_202603060835210894.jpg" alt="Super Sale Offers" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 350px;">
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
    <!-- Modal Tìm kiếm -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-5 text-center">
                    <h5 class="fw-bold mb-4" style="color: var(--tempi-green);">Bạn muốn tìm sản phẩm gì?</h5>
                    <form action="search.php" method="GET" class="d-flex w-100">
                        <input type="text" name="keyword" class="form-control rounded-start-pill py-3 ps-4 border-success" placeholder="Ví dụ: Mì Hảo Hảo, Sữa tươi..." required autofocus>
                        <button class="btn btn-warning rounded-end-pill px-4 fw-bold text-dark border-0" type="submit">
                            <i class="fas fa-search me-1"></i> Tìm
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Đăng nhập -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-tempi-green text-white">
                    <h5 class="modal-title fw-bold">Đăng nhập tài khoản</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="auth.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">ĐĂNG NHẬP</button>
                        <div class="text-center mt-3 small">
                            Chưa có tài khoản? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" class="text-decoration-none fw-bold text-success">Đăng ký ngay</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">Đăng ký thành viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="auth.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Địa chỉ giao hàng (Cụ thể)</label>
                            <input type="text" name="address" class="form-control" placeholder="Số nhà, Đường, Phường/Xã..." required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-bold">ĐĂNG KÝ TÀI KHOẢN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Phần thêm sản phẩm và thu gọn -->
    <script>
        // --- 1. BIẾN LƯU TRỮ GIAO DIỆN GỐC ---
        const originalContent = {};

        // --- 2. HÀM TẢI THÊM SẢN PHẨM ---
        function loadMoreProducts(type, btnElement) {
            let currentOffset = parseInt(btnElement.getAttribute('data-offset'));
            let targetId = (type === 'sale') ? 'sale-product-list' : 'new-product-list';

            // Lưu lại HTML gốc của danh sách nếu chưa lưu
            if (!originalContent[targetId]) {
                originalContent[targetId] = document.getElementById(targetId).innerHTML;
            }

            let originalText = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang tải...';
            btnElement.disabled = true;

            fetch(`ajax_load_products.php?type=${type}&offset=${currentOffset}`)
                .then(response => response.text())
                .then(html => {
                    if (html.trim() === '') {
                        // Trạng thái: Hết sản phẩm
                        btnElement.innerHTML = 'Đã hiển thị tất cả';
                        btnElement.disabled = true;
                        btnElement.classList.remove('btn-outline-success', 'btn-outline-light');
                        btnElement.classList.add('btn-secondary');
                    } else {
                        // Trạng thái: Thêm sản phẩm thành công
                        document.getElementById(targetId).insertAdjacentHTML('beforeend', html);
                        btnElement.setAttribute('data-offset', currentOffset + 4);
                        btnElement.innerHTML = originalText;
                        btnElement.disabled = false;
                    }

                    // Hiện nút Thu gọn lên
                    document.getElementById(`collapse-btn-${type}`).style.display = 'inline-block';
                })
                .catch(error => {
                    console.error('Lỗi khi tải thêm:', error);
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                });
        }

        // --- 3. HÀM THU GỌN DANH SÁCH ---
        function collapseProducts(type, defaultOffset) {
            let targetId = (type === 'sale') ? 'sale-product-list' : 'new-product-list';
            let loadMoreBtn = document.getElementById(`load-more-${type}`);
            let collapseBtn = document.getElementById(`collapse-btn-${type}`);

            // Khôi phục lại HTML ban đầu
            if (originalContent[targetId]) {
                document.getElementById(targetId).innerHTML = originalContent[targetId];
            }

            // Đặt lại các thuộc tính cho nút Xem thêm
            loadMoreBtn.setAttribute('data-offset', defaultOffset);
            loadMoreBtn.innerHTML = 'Xem thêm sản phẩm <i class="fas fa-chevron-down ms-1"></i>';
            loadMoreBtn.disabled = false;

            // Trả lại màu sắc ban đầu cho nút Xem thêm
            loadMoreBtn.classList.remove('btn-secondary');
            if (type === 'sale') loadMoreBtn.classList.add('btn-outline-success');
            if (type === 'new') loadMoreBtn.classList.add('btn-outline-light');

            // Ẩn nút Thu gọn đi
            collapseBtn.style.display = 'none';

            // Cuộn trang mượt mà lên ngay vị trí của danh sách đó
            document.getElementById(targetId).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    </script>
</body>

</html>