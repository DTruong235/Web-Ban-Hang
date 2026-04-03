<?php
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Về Chúng Tôi - Bách Hóa Pew</title>
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
                            <a class="nav-link" href="index.php">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                Danh mục
                            </a>
                            <ul class="dropdown-menu custom-dropdown" aria-labelledby="navbarDropdown">
                                <?php
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
                            <a class="nav-link active-yellow" href="about.php">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Liên hệ</a>
                        </li>
                    </ul>
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
                            <a href="#" class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#searchModal">
                                <i class="fas fa-search fs-5"></i>
                            </a>
                        </li>

                        <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a href="cart.php" class="cart-icon position-relative text-white">
                                <i class="fas fa-shopping-cart fs-5"></i>
                                <?php
                                $cart_count = 0;
                                if (isset($_SESSION['user_id'])) {
                                    $uid = (int)$_SESSION['user_id'];
                                    $c_res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
                                    if ($c_res && $c_row = $c_res->fetch_assoc()) {
                                        $cart_count = $c_row['total'] ?? 0;
                                    }
                                } else if (isset($_SESSION['cart'])) {
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

    <!-- Hero Section -->
    <section class="container-fluid bg-tempi-green text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Về Bách Hóa Pew</h1>
                    <p class="lead">Mang cả siêu thị về nhà chỉ bằng một cú chạm</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-store fa-6x opacity-50"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Sứ Mệnh & Tầm Nhìn -->
    <section class="container my-5 py-5">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 h-100 shadow-sm" style="border-left: 5px solid var(--tempi-green);">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-3 text-tempi-green">
                            <i class="fas fa-bullseye me-2"></i> Sứ Mệnh
                        </h3>
                        <p class="card-text lh-lg">
                            "Mang cả siêu thị về nhà chỉ bằng một cú chạm." Sứ mệnh của chúng tôi là cung cấp nguồn thực phẩm và nhu yếu phẩm chất lượng với mức giá hợp lý, thông qua một nền tảng công nghệ ổn định và dễ sử dụng nhất cho tất cả mọi người.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card border-0 h-100 shadow-sm" style="border-left: 5px solid var(--tempi-yellow);">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-3 text-tempi-green">
                            <i class="fas fa-rocket me-2"></i> Tầm Nhìn
                        </h3>
                        <p class="card-text lh-lg">
                            "Trở thành người bạn đồng hành số 1 trong căn bếp của mọi gia đình trẻ." Bách Hóa Pew hướng tới việc xây dựng một hệ sinh thái bán lẻ trực tuyến thông minh, nơi khách hàng không chỉ mua sắm mà còn được trải nghiệm dịch vụ tận tâm, hiện đại và phong cách.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Giới Thiệu -->
    <section class="container my-5 py-5 bg-white rounded shadow-sm">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="./image/Bánh/cate-mobile-compressed_202603060835210894.jpg" alt="Bách Hóa Pew" class="img-fluid rounded shadow-sm">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4 text-tempi-green">Câu Chuyện Của Chúng Tôi</h2>
                <p class="lh-lg text-muted mb-3">
                    Bách Hóa Pew là tiệm tạp hóa trực tuyến hiện đại, ra đời với mong muốn đơn giản hóa việc đi chợ hàng ngày của mọi người. Chúng tôi tập trung vào trải nghiệm mua sắm nhanh gọn, giao diện thân thiện và danh mục sản phẩm chọn lọc – từ thực phẩm thiết yếu đến đồ gia dụng tiện ích.
                </p>
                <p class="lh-lg text-muted">
                    Không chỉ là một trang web bán hàng, Bách Hóa Pew là nơi công nghệ phục vụ đời sống, giúp bạn có thêm thời gian cho những việc quan trọng hơn.
                </p>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-tempi-green text-white rounded-pill px-4 fw-bold">
                        Khám Phá Sản Phẩm
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Lợi Thế Cạnh Tranh -->
    <section class="container my-5 py-5">
        <h2 class="text-center fw-bold mb-5">Lợi Thế Của Chúng Tôi</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 text-center h-100 shadow-sm p-4 hover-shadow" style="transition: all 0.3s;">
                    <div class="display-4 text-tempi-green mb-3">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Tiện Lợi</h4>
                    <p class="text-muted">
                        Giao diện sạch sẽ, đặt hàng trong 30 giây. Không còn phải mất thời gian đi chợ, tất cả chỉ trong tầm tay.
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 text-center h-100 shadow-sm p-4 hover-shadow" style="transition: all 0.3s;">
                    <div class="display-4 text-tempi-green mb-3">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Minh Bạch</h4>
                    <p class="text-muted">
                        Giá cả, nguồn gốc và trạng thái đơn hàng luôn rõ ràng. Bạn biết chính xác những gì bạn mua.
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 text-center h-100 shadow-sm p-4 hover-shadow" style="transition: all 0.3s;">
                    <div class="display-4 text-tempi-green mb-3">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Tận Tâm</h4>
                    <p class="text-muted">
                        Chăm sóc khách hàng như người thân trong nhà. Hỗ trợ 24/7 để giải đáp mọi thắc mắc.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Thống Kê -->
    <section class="container-fluid bg-tempi-green text-white py-5 my-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Con Số Nói Lên Sự Tin Tưởng</h2>
            <div class="row text-center">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div>
                        <h2 class="display-4 fw-bold mb-2">500+</h2>
                        <p class="fs-5">Sản Phẩm Chọn Lọc</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div>
                        <h2 class="display-4 fw-bold mb-2">10K+</h2>
                        <p class="fs-5">Khách Hàng Hài Lòng</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div>
                        <h2 class="display-4 fw-bold mb-2">10</h2>
                        <p class="fs-5">Danh Mục Đa Dạng</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div>
                        <h2 class="display-4 fw-bold mb-2">2h</h2>
                        <p class="fs-5">Giao Hàng Nhanh Nhất</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gọi Hành Động -->
    <section class="container my-5 py-5 text-center">
        <h2 class="fw-bold mb-4">Sẵn Sàng Trải Nghiệm?</h2>
        <p class="lead mb-4 text-muted">Tham gia hàng ngàn khách hàng đang tin tưởng Bách Hóa Pew</p>
        <a href="index.php" class="btn btn-lg btn-tempi-green text-white rounded-pill px-5 fw-bold me-2">
            Bắt Đầu Mua Sắm
            <i class="fas fa-shopping-cart ms-2"></i>
        </a>
        <a href="contact.php" class="btn btn-lg btn-outline-tempi-green rounded-pill px-5 fw-bold">
            Liên Hệ Chúng Tôi
            <i class="fas fa-comments ms-2"></i>
        </a>
    </section>

    <!-- Footer -->
    <footer class="footer-bg text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h3 class="fw-bold mb-3">Bách Hóa Pew</h3>
                    <p class="small">Nền tảng bán hàng trực tuyến uy tín và hiện đại.</p>
                    <div class="social-icons">
                        <a href="https://facebook.com/BachHoaPew.Official" target="_blank" class="text-white me-3"><i class="fab fa-facebook fs-5"></i></a>
                        <a href="https://instagram.com/pew.grocery" target="_blank" class="text-white"><i class="fab fa-instagram fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Danh mục</h6>
                    <ul class="list-unstyled small">
                        <li><a href="category.php?id=1" class="text-white text-decoration-none">Rau Củ</a></li>
                        <li><a href="category.php?id=5" class="text-white text-decoration-none">Thịt & Gia Cầm</a></li>
                        <li><a href="category.php?id=6" class="text-white text-decoration-none">Nước Ngọt</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Hỗ trợ</h6>
                    <ul class="list-unstyled small">
                        <li><a href="about.php" class="text-white text-decoration-none">Về chúng tôi</a></li>
                        <li><a href="contact.php" class="text-white text-decoration-none">Liên hệ</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6 class="fw-bold text-uppercase">Liên Hệ</h6>
                    <p class="small mb-2">
                        <i class="fas fa-phone me-2"></i> 0123456789
                    </p>
                    <p class="small">
                        <i class="fas fa-envelope me-2"></i> bachhoapew@gmail.com
                    </p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center py-3">
                <p class="small mb-0">&copy; 2026 Bách Hóa Pew. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
