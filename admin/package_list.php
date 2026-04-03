<?php
require_once '../db.php';

// Lấy danh sách đơn hàng chưa hoàn thành để đóng gói
$sql = "SELECT o.id AS order_id, o.order_date, o.status, c.full_name, c.phone_number, c.address,
               od.product_id, p.name AS product_name, od.quantity, od.price
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        JOIN order_details od ON od.order_id = o.id
        JOIN products p ON p.id = od.product_id
        WHERE o.status IN (0,1,2)
        ORDER BY o.order_date ASC, o.id ASC";
$res = $conn->query($sql);

$orders = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $oid = $row['order_id'];
        if (!isset($orders[$oid])) {
            $orders[$oid] = [
                'order_date' => $row['order_date'],
                'status' => $row['status'],
                'customer' => $row['full_name'],
                'phone' => $row['phone_number'],
                'address' => $row['address'],
                'items' => []
            ];
        }
        $orders[$oid]['items'][] = [
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }
}

function statusLabel($status) {
    return match ($status) {
        0 => '<span class="badge bg-warning text-dark">Mới đặt</span>',
        1 => '<span class="badge bg-primary">Đang xử lý</span>',
        2 => '<span class="badge bg-info text-dark">Đang giao</span>',
        3 => '<span class="badge bg-success">Hoàn thành</span>',
        default => '<span class="badge bg-danger">Đã hủy</span>',
    };
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đóng gói - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #005a3c; }
        .sidebar a { color: #fff; text-decoration: none; padding: 12px 20px; display: block; transition: 0.3s; }
        .sidebar a:hover { background-color: #00452d; border-left: 4px solid #f9b612; }
        .sidebar a.active { background-color: #00452d; border-left: 4px solid #f9b612; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0 sidebar text-white">
                <div class="p-4 text-center border-bottom border-secondary">
                    <h4 class="fw-bold text-warning mb-0">BÁCH HÓA PEW</h4>
                    <div class="mt-2 small opacity-75">
                        <i class="fas fa-user-shield"></i> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Quản trị viên' ?><br>
                        <span style="font-size: 0.8em;">ID: <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'ADMIN' ?></span>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="categorys.php"><i class="fas fa-list me-2"></i> Danh Mục Gốc</a>
                    <a href="sub_categories.php"><i class="fas fa-tags me-2"></i> Thể Loại Con</a>
                    <a href="brands.php"><i class="fas fa-copyright me-2"></i> Hãng Sản Xuất</a>
                    <a href="products.php"><i class="fas fa-box me-2"></i> Sản Phẩm</a>
                    <a href="customers.php"><i class="fas fa-users me-2"></i> Khách Hàng</a>
                    <a href="orders.php"><i class="fas fa-file-invoice-dollar me-2"></i> Đơn Hàng</a>
                    <a href="package_list.php" class="active"><i class="fas fa-box-open me-2"></i> Danh Sách Đóng Gói</a>
                    <a href="reviews.php"><i class="fas fa-star me-2"></i> Đánh giá</a>
                    <a href="contacts.php"><i class="fas fa-envelope me-2"></i> Liên hệ</a>
                    <a href="../logout.php" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
                </div>
            </div>

            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Danh sách đóng gói</h2>
                    <div>
                        <button class="btn btn-outline-secondary me-2" onclick="window.print();"><i class="fas fa-print"></i> In danh sách</button>
                        <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại Đơn Hàng</a>
                    </div>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">Không có đơn hàng cần đóng gói.</div>
                <?php else: ?>
                    <?php foreach ($orders as $orderId => $details): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">Đơn #<?= $orderId ?> - <?= statusLabel($details['status']) ?></h5>
                                    <small>Khách: <?= htmlspecialchars($details['customer']) ?> | SDT: <?= htmlspecialchars($details['phone']) ?> | <?= htmlspecialchars($details['address']) ?></small>
                                </div>
                                <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($details['order_date'])) ?></small>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr><th>Sản phẩm</th><th>Số lượng</th><th>đơn giá</th><th>Thành tiền</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($details['items'] as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= number_format($item['price']) ?> đ</td>
                                                <td><?= number_format($item['price'] * $item['quantity']) ?> đ</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>