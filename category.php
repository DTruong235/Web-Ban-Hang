<?php
require_once 'db.php';



// 1. Lấy ID danh mục từ URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Kiểm tra danh mục
$sql_cat = "SELECT * FROM categories WHERE id = $category_id AND status = 1";
$res_cat = $conn->query($sql_cat);
if ($res_cat->num_rows == 0) {
    header("Location: category.php");
    exit();
}
$category = $res_cat->fetch_assoc();

// --- BẮT ĐẦU PHẦN CODE MỚI ---

// 3. Lấy ID của thể loại con từ URL (nếu khách click vào)
$subcat_id = isset($_GET['subcat']) ? (int)$_GET['subcat'] : 0;

// 4. Truy vấn lấy các "Thể loại con" thuộc về Danh mục này để in ra thanh ngang
$sql_subcats = "SELECT * FROM sub_categories WHERE category_id = $category_id";
$res_subcats = $conn->query($sql_subcats);

// 5. Lấy SẢN PHẨM thuộc danh mục này (có kèm điều kiện lọc theo Thể loại con)
// --- BẮT ĐẦU CẬP NHẬT LOGIC LỌC ---
$subcat_id = isset($_GET['subcat']) ? (int)$_GET['subcat'] : 0;
$brand_id = isset($_GET['brand']) ? (int)$_GET['brand'] : 0; // Thêm biến nhận Hãng từ URL

// Lấy SẢN PHẨM thuộc danh mục này (có kèm điều kiện lọc)
$sql_pro = "SELECT * FROM products WHERE category_id = $category_id AND status = 1";

if ($subcat_id > 0) {
    $sql_pro .= " AND sub_category_id = $subcat_id"; // Nối thêm lọc thể loại con
}

if ($brand_id > 0) {
    $sql_pro .= " AND brand_id = $brand_id"; // Nối thêm lọc Hãng sản xuất
}

$sql_pro .= " ORDER BY id DESC";
$result_pro = $conn->query($sql_pro);
// --- KẾT THÚC CẬP NHẬT LOGIC LỌC ---

// 6. Lấy KHUYẾN MÃI SỐC (Sản phẩm có giảm giá trong danh mục này)
$sql_flash = "SELECT * 
              FROM products 
              WHERE category_id = $category_id AND discount_price IS NOT NULL AND status = 1 
              Order by ((price - discount_price) / price) DESC 
              LIMIT 4";
$res_flash = $conn->query($sql_flash);

// 7. Lấy danh sách HÃNG SẢN XUẤT chỉ thuộc về danh mục hiện tại (Đã cập nhật từ trước)
$sql_brands = "SELECT DISTINCT b.id, b.name 
               FROM brands b 
               JOIN products p ON b.id = p.brand_id 
               WHERE p.category_id = $category_id AND p.status = 1";
$res_brands = $conn->query($sql_brands);

// --- KẾT THÚC PHẦN CODE MỚI ---

// Lấy số lượng giỏ hàng cho Header
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
    <title><?= htmlspecialchars($category['name']) ?> - Bách Hóa Pew</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Nâng cao cho giao diện Category Mới */
        .filter-pill {
            display: inline-block;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            color: #333;
            text-decoration: none;
            background: #fff;
            white-space: nowrap;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .filter-pill:hover,
        .filter-pill.active {
            border-color: var(--tempi-green);
            color: var(--tempi-green);
            font-weight: bold;
        }

        .brand-logo {
            height: 30px;
            object-fit: contain;
        }

        .flash-sale-box {
            background-color: #61b87a;
            /* Màu xanh lá nhạt giống ảnh */
            border-radius: 12px;
            padding: 20px;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .seo-content {
            font-size: 0.95rem;
            color: #444;
            line-height: 1.6;
        }

        /* --- HIỆU ỨNG THU GỌN BÀI VIẾT SEO --- */
        .seo-text-container {
            position: relative;
            max-height: 150px;
            /* Chiều cao mặc định khi thu gọn (chứa khoảng 3-4 dòng) */
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }

        .seo-text-container.expanded {
            max-height: 2000px;
            /* Chiều cao mở rộng tối đa (đủ lớn để chứa hết bài viết) */
        }

        .seo-text-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 80px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1));
            pointer-events: none;
            transition: opacity 0.3s;
        }

        /* Khi mở rộng bài viết thì làm biến mất lớp phủ mờ trắng */
        .seo-text-container.expanded .seo-text-overlay {
            opacity: 0;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Hearder -->
    <header class="header-bg sticky-top shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark py-3">
                <a class="navbar-brand fw-bold fs-3" href="index.php">Bách Hóa Pew</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- <ul class="navbar-nav mx-auto align-items-center">
                        <li class="nav-item"><a class="nav-link" href="#">Trang chủ</a></li>
                    </ul> -->
                    <form action="search.php" method="GET" class="d-flex mx-auto w-100 px-3" style="max-width: 600px;">
                        <div class="input-group shadow-sm">
                            <input type="text" name="keyword" class="form-control border-0 px-3 py-2" placeholder="Bạn muốn tìm sản phẩm gì?..."
                                value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>" required>
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

    <div class="container mt-3 mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="category.php" class="text-decoration-none text-tempi-green">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
            </ol>
        </nav>
    </div>

    <!-- Thể loại -->
    <section class="container mb-3">
        <div class="d-flex gap-3 overflow-auto hide-scrollbar py-2">

            <a href="category.php?id=<?= $category_id ?>" class="filter-pill <?= ($subcat_id == 0) ? 'active' : 'text-muted' ?> text-center" style="min-width: 80px;">
                <img src="https://cdn-icons-png.flaticon.com/512/3014/3014502.png" alt="All" height="40" class="d-block mx-auto mb-2 <?= ($subcat_id == 0) ? '' : 'opacity-50' ?>">
                Tất cả
            </a>

            <?php if ($res_subcats && $res_subcats->num_rows > 0): ?>
                <?php while ($sub = $res_subcats->fetch_assoc()): ?>
                    <?php
                    // Kiểm tra xem thẻ này có đang được click không
                    $is_active = ($subcat_id == $sub['id']);

                    // Lấy ảnh từ database, nếu trống thì dùng ảnh mặc định
                    $sub_img = !empty($sub['image']) ? $sub['image'] : 'https://cdn-icons-png.flaticon.com/512/2515/2515150.png';
                    ?>
                    <a href="category.php?id=<?= $category_id ?>&subcat=<?= $sub['id'] ?>" class="filter-pill <?= $is_active ? 'active' : 'text-muted' ?> text-center" style="min-width: 80px;">
                        <img src="<?= $sub_img ?>" alt="<?= htmlspecialchars($sub['name']) ?>" height="60" class="d-block mx-auto mb-2 <?= $is_active ? '' : '' ?>">
                        <span style="font-size: 0.85rem;"><?= htmlspecialchars($sub['name']) ?></span>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </section>
    <!-- Lọc -->
    <section class="container mb-4">
        <div class="d-flex align-items-center flex-wrap gap-2">

            <span class="text-muted small fw-bold me-2">Thương hiệu:</span>

            <a href="category.php?id=<?= $category_id ?><?= $subcat_id > 0 ? '&subcat=' . $subcat_id : '' ?>"
                class="filter-pill <?= ($brand_id == 0) ? 'active' : 'text-muted' ?>">
                Tất cả
            </a>

            <?php if ($res_brands && $res_brands->num_rows > 0): ?>
                <?php while ($b = $res_brands->fetch_assoc()): ?>
                    <?php
                    // Kiểm tra xem Hãng này có đang được chọn không
                    $is_brand_active = ($brand_id == $b['id']);
                    ?>
                    <a href="category.php?id=<?= $category_id ?><?= $subcat_id > 0 ? '&subcat=' . $subcat_id : '' ?>&brand=<?= $b['id'] ?>"
                        class="filter-pill <?= $is_brand_active ? 'active text-danger fw-bold border-danger' : 'fw-semibold text-danger' ?>">
                        <?= htmlspecialchars($b['name']) ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>

            <div class="ms-auto d-flex gap-2 mt-2 mt-md-0">
                <select class="form-select form-select-sm" style="width: 130px;">
                    <option>Sắp xếp</option>
                    <option>Giá tăng dần</option>
                    <option>Giá giảm dần</option>
                </select>
            </div>
        </div>
    </section>
    <!-- Banner -->
    <section class="container mb-4">
        <img src="./image/Nước Ngọt/freecompress-trang-cate-pc_202603040932084457.jpg" alt="Banner" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 150px;">
    </section>
    <!-- Khuyến Mãi Sốc -->
    <?php if ($res_flash && $res_flash->num_rows > 0): ?>
        <section class="container mb-5">
            <div class="flash-sale-box shadow-sm">
                <div class="d-flex align-items-center mb-3">
                    <h3 class="text-white fw-bold mb-0 text-uppercase"><i class="fas fa-bolt text-warning me-2"></i> Khuyến Mãi Sốc</h3>
                </div>
                <div class="row">
                    <?php while ($flash = $res_flash->fetch_assoc()): ?>
                        <?php
                        $percent = round((($flash['price'] - $flash['discount_price']) / $flash['price']) * 100);
                        ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <div class="card product-card h-100 border-0 shadow-sm position-relative">
                                <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded shadow-sm" style="z-index: 10;">
                                    -<?= $percent ?>%
                                </div>
                                <a href="product_detail.php?id=<?= $flash['id'] ?>" class="text-decoration-none">
                                    <div class="product-img-wrapper p-2">
                                        <img src="<?= $flash['cover_image'] ?>" alt="<?= htmlspecialchars($flash['name']) ?>" class="img-cover rounded">
                                    </div>
                                </a>
                                <div class="card-body text-center d-flex flex-column pt-1">
                                    <a href="product_detail.php?id=<?= $flash['id'] ?>" class="text-decoration-none flex-grow-1">
                                        <h6 class="card-title fw-bold text-dark text-truncate" title="<?= htmlspecialchars($flash['name']) ?>">
                                            <?= htmlspecialchars($flash['name']) ?>
                                        </h6>
                                    </a>
                                    <div class="mt-auto">
                                        <div class="fs-4 fw-bold text-danger"><?= number_format($flash['discount_price']) ?> đ</div>
                                        <div class="text-muted text-decoration-line-through small"><?= number_format($flash['price']) ?> đ</div>

                                        <div class="text-start fw-bold mb-1 mt-2" style="font-size: 0.75rem; color: #ff7f00;">
                                            Tối đa 5 sp/đơn
                                        </div>

                                        <div class="position-relative mb-2">
                                            <div class="progress" style="height: 22px; border-radius: 50px; background-color: #fef0cd;">
                                                <?php
                                                // Lấy tồn kho thực tế từ DB
                                                $stock = $flash['stock_quantity'];

                                                // Mẹo UI: Vì chúng ta không có cột "Tổng số suất ban đầu", 
                                                // ta dùng mẹo nhỏ để thanh màu vàng luôn hiển thị một tỷ lệ đẹp mắt
                                                $percent = ($stock <= 100) ? $stock : 50;
                                                ?>
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent ?>%; border-radius: 50px;"></div>
                                            </div>
                                            <div class="position-absolute top-50 start-50 translate-middle w-100 text-center" style="font-size: 0.8rem; font-weight: 600; color: #333; line-height: 22px;">
                                                Còn <?= $stock ?> suất
                                            </div>
                                        </div>

                                        <button onclick="addToCart(<?= $flash['id'] ?>)" class="btn btn-outline-success w-100 rounded-pill mt-1 fw-bold">MUA NGAY</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <!-- Gợi ý sản phẩm -->
    <section class="container my-4">
        <h4 class="fw-bold mb-3 border-bottom pb-2">Gợi ý cho bạn</h4>
        <div class="row">
            <?php if ($result_pro && $result_pro->num_rows > 0): ?>
                <?php while ($product = $result_pro->fetch_assoc()): ?>
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
                                    <div class="fw-bold text-danger mb-1"><?= number_format($final_price) ?> đ</div>
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
                    <h5 class="text-muted">Chưa có sản phẩm nào trong danh mục này.</h5>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Mô tả Sản Phẩm -->
    <section class="container my-5">
        <?php if (!empty($category['description'])): ?>
            <div class="bg-white p-4 p-md-5 rounded shadow-sm border">
                <h4 class="fw-bold mb-4"><?= htmlspecialchars($category['name']) ?> là gì?</h4>

                <div class="seo-text-container" id="seoTextContainer">
                    <div class="text-muted" style="line-height: 1.8;">
                        <?= nl2br($category['description']) ?>
                    </div>
                    <div class="seo-text-overlay" id="seoTextOverlay"></div>
                </div>
                <!-- Nút Đọc Thêm -->
                <div class="text-center mt-3">
                    <button class="btn btn-outline-success rounded-pill px-4 py-2 fw-bold" id="btnReadMore" onclick="toggleReadMore()">
                        Đọc thêm <i class="fas fa-chevron-down ms-1"></i>
                    </button>
                </div>
            </div>
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
        function addToCart(productId) {
            fetch(`add_to_cart.php?id=${productId}&ajax=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        let badge = document.getElementById('cart-badge');
                        badge.innerText = data.cart_count;
                    }
                })
                .catch(error => console.error('Lỗi thêm giỏ hàng:', error));
        }
    </script>
    <script>
        // Hàm xử lý nút Đọc thêm / Thu gọn
        function toggleReadMore() {
            var container = document.getElementById("seoTextContainer");
            var btn = document.getElementById("btnReadMore");

            if (container.classList.contains("expanded")) {
                // Đang mở -> Thu gọn lại
                container.classList.remove("expanded");
                btn.innerHTML = 'Đọc thêm <i class="fas fa-chevron-down ms-1"></i>';
            } else {
                // Đang thu gọn -> Mở ra
                container.classList.add("expanded");
                btn.innerHTML = 'Thu gọn <i class="fas fa-chevron-up ms-1"></i>';
            }
        }
    </script>
</body>

</html>