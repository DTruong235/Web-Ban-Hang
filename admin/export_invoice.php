<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    echo 'Không có đơn hàng';
    exit;
}
$order_id = (int)$_GET['id'];

$sql_order = "SELECT o.*, c.full_name AS customer_name, c.phone_number, c.address 
              FROM orders o 
              JOIN customers c ON o.customer_id = c.id 
              WHERE o.id = $order_id";
$res_order = $conn->query($sql_order);
if (!$res_order || $res_order->num_rows == 0) {
    echo 'Đơn hàng không tồn tại';
    exit;
}
$order = $res_order->fetch_assoc();

$sql_details = "SELECT od.*, p.name FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = $order_id";
$res_details = $conn->query($sql_details);

$today = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hóa Đơn #<?= $order_id ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .invoice-box { max-width: 900px; margin: 30px auto; background: #fff; padding: 40px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0,0,0,0.08); }
    .invoice-title { font-size: 36px; font-weight: 800; color: #0d4d4a; }
    .text-teal { color: #0d7f77; }
    .table thead th { background: #0d7f77; color: #fff; }
    @media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="invoice-box">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="invoice-title">HÓA ĐƠN</h1>
            <p class="mb-1">Ngày lập: <strong><?= $today ?></strong></p>
            <p class="mb-0">Mã đơn: <strong>#<?= $order_id ?></strong></p>
        </div>
        <div class="text-end">
            <h4 class="text-teal">Bách Hóa Pew</h4>
            <p class="mb-1">Địa chỉ: Phường Long Xuyên, An Giang</p>
            <p class="mb-1">Điện thoại: 0123456789</p>
            <p class="mb-0">Email: bachhoapew@gmail.com</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h5>Hóa đơn cho:</h5>
            <p class="mb-1"><strong><?= htmlspecialchars($order['customer_name']) ?></strong></p>
            <p class="mb-1"><?= htmlspecialchars($order['address']) ?></p>
            <p class="mb-0">Điện thoại: <?= htmlspecialchars($order['phone_number']) ?></p>
        </div>
        <div class="col-md-6 text-md-end">
            <h5>Thanh toán cho:</h5>
            <p class="mb-1"><strong>Bách Hóa Pew</strong></p>
            <p class="mb-1">Email: bachhoapew@gmail.com</p>
            <p class="mb-0">website: bachhoapew.local</p>
        </div>
    </div>

    <table class="table table-bordered text-center">
        <thead>
            <tr><th>Mô tả</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>
        </thead>
        <tbody>
        <?php while($item = $res_details->fetch_assoc()): ?>
        <tr>
            <td class="text-start"><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price']) ?> đ</td>
            <td><?= number_format($item['price'] * $item['quantity']) ?> đ</td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Tổng cộng</td>
                <td class="fw-bold text-danger"><?= number_format($order['total_money']) ?> đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="text-end mt-4 no-print">
        <button class="btn btn-success" onclick="window.print();"><i class="fa fa-print"></i> In hóa đơn</button>
        <a href="orders.php" class="btn btn-secondary">Quay lại</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
