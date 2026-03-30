<?php
require_once '../db.php';

// Kiểm tra xem có truyền ID đơn hàng lên URL không
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = (int)$_GET['id'];

// 1. LẤY THÔNG TIN CHUNG CỦA ĐƠN HÀNG VÀ KHÁCH HÀNG
$sql_order = "SELECT o.*, c.full_name, c.phone_number, c.address 
              FROM orders o 
              JOIN customers c ON o.customer_id = c.id 
              WHERE o.id = $order_id";
$res_order = $conn->query($sql_order);

if ($res_order->num_rows == 0) {
    echo "<script>alert('Đơn hàng không tồn tại!'); window.location.href='orders.php';</script>";
    exit();
}
$order = $res_order->fetch_assoc();

// 2. LẤY CHI TIẾT TỪNG MÓN HÀNG TRONG ĐƠN (Lấy thêm tên và ảnh từ bảng products)
$sql_details = "SELECT od.*, p.name, p.cover_image 
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                WHERE od.order_id = $order_id";
$res_details = $conn->query($sql_details);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?= $order['id'] ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #005a3c;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #00452d;
            border-left: 4px solid #f9b612;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0 sidebar text-white">
                <div class="p-4 text-center border-bottom border-secondary">
                    <h4 class="fw-bold text-warning mb-0">TEMPI ADMIN</h4>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-user-shield"></i> Võ Văn Tỷ<br>
                        <span style="font-size: 0.8em;">ID: DTH235811</span>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="categorys.php"><i class="fas fa-list me-2"></i> Danh Mục</a>
                    <a href="products.php"><i class="fas fa-box me-2"></i> Sản Phẩm</a>
                    <a href="customers.php"><i class="fas fa-users me-2"></i> Khách Hàng</a>
                    <a href="orders.php" style="background-color: #00452d; border-left: 4px solid #f9b612;"><i class="fas fa-file-invoice-dollar me-2"></i> Đơn Hàng</a>
                    <a href="sub_categories.php"><i class="fas fa-tags me-2"></i> Thể Loại Con</a>
                    <a href="#" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
                </div>
            </div>

            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Chi Tiết Đơn Hàng <span class="text-danger">#<?= $order['id'] ?></span></h2>
                    <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i> Quay lại danh sách</a>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-dark text-white fw-bold">
                                <i class="fas fa-info-circle me-2"></i> Thông Tin Chung
                            </div>
                            <div class="card-body">
                                <p><strong><i class="fas fa-user text-muted me-2"></i> Khách hàng:</strong> <?= htmlspecialchars($order['full_name']) ?></p>
                                <p><strong><i class="fas fa-phone text-muted me-2"></i> Điện thoại:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
                                <p><strong><i class="fas fa-map-marker-alt text-muted me-2"></i> Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                                <hr>
                                <p><strong><i class="fas fa-calendar-alt text-muted me-2"></i> Ngày đặt:</strong> <?= date('d/m/Y H:i:s', strtotime($order['order_date'])) ?></p>
                                <p><strong><i class="fas fa-money-bill-wave text-muted me-2"></i> Thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                                <p><strong><i class="fas fa-clipboard-list text-muted me-2"></i> Trạng thái:</strong>
                                    <?php
                                    if ($order['status'] == 0) echo '<span class="badge bg-warning text-dark">Mới đặt</span>';
                                    elseif ($order['status'] == 1) echo '<span class="badge bg-primary">Đang xử lý</span>';
                                    elseif ($order['status'] == 2) echo '<span class="badge bg-info">Đang giao</span>';
                                    elseif ($order['status'] == 3) echo '<span class="badge bg-success">Hoàn thành</span>';
                                    else echo '<span class="badge bg-danger">Đã hủy</span>';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-header bg-success text-white fw-bold">
                                <i class="fas fa-shopping-basket me-2"></i> Danh Sách Sản Phẩm
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-hover align-middle mb-0 text-center">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($item = $res_details->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-start">
                                                    <div class="d-flex align-items-center">
                                                        <img src="../<?= $item['cover_image'] ?>" alt="Img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;" class="me-3 border">
                                                        <span class="fw-semibold"><?= htmlspecialchars($item['name']) ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-muted"><?= number_format($item['price']) ?> đ</td>
                                                <td><strong>x<?= $item['quantity'] ?></strong></td>
                                                <td class="text-danger fw-bold"><?= number_format($item['price'] * $item['quantity']) ?> đ</td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold fs-5">TỔNG CỘNG:</td>
                                            <td class="text-danger fw-bold fs-4"><?= number_format($order['total_money']) ?> đ</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>