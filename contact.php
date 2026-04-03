<?php
require_once 'db.php';

// --- XỬ LÝ FORM LIÊN HỆ ---
if (isset($_POST['send_contact'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        $error_msg = "Vui lòng điền đầy đủ tất cả các trường!";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Email không hợp lệ!";
    } else if (!preg_match('/^0\d{9}$/', $phone)) {
        $error_msg = "Số điện thoại phải có 10 chữ số và bắt đầu bằng 0!";
    } else {
        // Lưu vào Database
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $success_msg = "✅ Cảm ơn bạn! Chúng tôi đã nhận liên hệ của bạn. Sẽ sớm phản hồi trong 24 giờ.";
            // Clear form
            $_POST = [];
        } else {
            $error_msg = "Có lỗi xảy ra, vui lòng thử lại!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ - Bách Hóa Pew</title>
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
                            <a class="nav-link" href="about.php">Về chúng tôi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active-yellow" href="contact.php">Liên hệ</a>
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
            <h1 class="display-4 fw-bold mb-3">Liên Hệ Với Chúng Tôi</h1>
            <p class="lead">Chúng tôi rất vui khi nhận được tin nhắn từ bạn. Điền form dưới đây và chúng tôi sẽ phản hồi trong 24 giờ.</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="container my-5 py-5">
        <div class="row g-5">
            <!-- Form Liên Hệ -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">
                            <i class="fas fa-envelope text-tempi-green me-2"></i> Gửi Cho Chúng Tôi Một Tin Nhắn
                        </h3>

                        <?php if (isset($error_msg)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i> <?= $error_msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($success_msg)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $success_msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="contact.php">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg" placeholder="Nhập tên của bạn" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Nhập email của bạn" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số Điện Thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control form-control-lg" placeholder="Ví dụ: 0987654321" required value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Chủ Đề <span class="text-danger">*</span></label>
                                    <select name="subject" class="form-select form-select-lg" required>
                                        <option value="">-- Chọn chủ đề --</option>
                                        <option value="Hỏi về sản phẩm">Hỏi về sản phẩm</option>
                                        <option value="Phản ánh vấn đề">Phản ánh vấn đề</option>
                                        <option value="Đơn hàng">Câu hỏi về đơn hàng</option>
                                        <option value="Hợp tác & đơn">Hợp tác & Quảng cáo</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nội Dung <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6" placeholder="Vui lòng nhập nội dung chi tiết..." required><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                            </div>

                            <button type="submit" name="send_contact" class="btn btn-lg btn-tempi-green text-white w-100 fw-bold rounded-pill">
                                <i class="fas fa-paper-plane me-2"></i> Gửi Liên Hệ
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Thông Tin Cửa Hàng -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">
                            <i class="fas fa-store text-tempi-green me-2"></i> Thông Tin Cửa Hàng
                        </h3>

                        <!-- Địa chỉ -->
                        <div class="mb-4 d-flex">
                            <div class="me-3">
                                <i class="fas fa-map-marker-alt text-tempi-green fs-5 mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Địa Chỉ</h6>
                                <p class="text-muted">Phường Long Xuyên, Tỉnh An Giang</p>
                            </div>
                        </div>

                        <!-- Điện thoại -->
                        <div class="mb-4 d-flex">
                            <div class="me-3">
                                <i class="fas fa-phone text-tempi-green fs-5 mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Số Điện Thoại</h6>
                                <a href="tel:0123456789" class="text-decoration-none text-tempi-green fw-bold">0123456789</a>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-4 d-flex">
                            <div class="me-3">
                                <i class="fas fa-envelope text-tempi-green fs-5 mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Email</h6>
                                <a href="mailto:bachhoapew@gmail.com" class="text-decoration-none text-tempi-green fw-bold">bachhoapew@gmail.com</a>
                            </div>
                        </div>

                        <!-- Giờ mở cửa -->
                        <div class="mb-4 d-flex">
                            <div class="me-3">
                                <i class="fas fa-clock text-tempi-green fs-5 mt-1"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold">Giờ Mở Cửa</h6>
                                <p class="text-muted mb-1">06:00 - 23:00</p>
                                <small class="text-muted">Hàng ngày (Cả thứ 7 và Chủ nhật)</small>
                            </div>
                        </div>

                        <hr>

                        <!-- Mạng xã hội -->
                        <h6 class="fw-bold mb-3">Theo Dõi Chúng Tôi</h6>
                        <div class="d-flex gap-3">
                            <a href="https://facebook.com/BachHoaPew.Official" target="_blank" class="btn btn-outline-tempi-green btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://instagram.com/pew.grocery" target="_blank" class="btn btn-outline-tempi-green btn-lg rounded-circle" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 bg-light">
                        <h5 class="fw-bold mb-3">❓ Câu Hỏi Thường Gặp</h5>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><strong>Q:</strong> Giao hàng mất bao lâu?</li>
                            <li class="mb-3"><strong>A:</strong> Thông thường 2 giờ trong nội thành.</li>

                            <li class="mb-2"><strong>Q:</strong> Có chính sách hoàn trả không?</li>
                            <li class="mb-3"><strong>A:</strong> Có, hoàn tiền 100% nếu hàng lỗi.</li>

                            <li class="mb-2"><strong>Q:</strong> Hỗ trợ các phương thức thanh toán nào?</li>
                            <li><strong>A:</strong> Tiền mặt, chuyển khoản và ví điện tử.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Map Iframe (Demo) -->
    <section class="container-fluid my-5">
        <h2 class="text-center fw-bold mb-4">Vị Trí Cửa Hàng</h2>
        <div class="ratio rounded overflow-hidden shadow-sm" style="--bs-aspect-ratio: 50%;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3925.4867254165326!2d105.41666971551556!3d10.352072992725984!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31a089b5eb0b1001%3A0x8c5c5b5c5b5c5b5c!2sLong%20Xuyen%2C%20An%20Giang!5e0!3m2!1svi!2s!4v1234567890" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
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
