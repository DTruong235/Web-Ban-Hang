<?php
require_once '../db.php';

// --- LẤY DANH SÁCH KHÁCH HÀNG CHO FORM THÊM ĐƠN ---
$customers_list = [];
$cust_res = $conn->query("SELECT id, full_name, phone_number FROM customers");
if ($cust_res) while ($c = $cust_res->fetch_assoc()) {
    $customers_list[] = $c;
}

// --- 1. THÊM ĐƠN HÀNG ---
if (isset($_POST['add_order'])) {
    $customer_id = (int)$_POST['customer_id'];
    $total_money = (int)$_POST['total_money'];
    $payment_method = trim($_POST['payment_method']);
    $status = (int)$_POST['status'];

    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_money, payment_method, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $customer_id, $total_money, $payment_method, $status);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit();
}

// --- 2. CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (SỬA) ---
if (isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);
    $stmt->execute();
    header("Location: orders.php");
    exit();
}

// --- 3. XÓA ĐƠN HÀNG ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: orders.php");
    exit();
}

// Lấy danh sách hóa đơn kết hợp tên khách hàng
$sql = "SELECT o.*, c.full_name, c.phone_number 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Đơn Hàng - Admin</title>
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

        .sidebar a.active {
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
                    <a href="../logout.php" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
                </div>
            </div>

            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Quản lý Đơn Hàng</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                        <i class="fas fa-plus"></i> Tạo đơn hàng
                    </button>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Đơn</th>
                                    <th>Khách Hàng</th>
                                    <th>Ngày Đặt</th>
                                    <th>Tổng Tiền</th>
                                    <th>Trạng Thái</th>
                                    <th>Cập Nhật & Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?= $row['id'] ?></strong></td>
                                            <td class="text-start">
                                                <?= htmlspecialchars($row['full_name']) ?><br>
                                                <small class="text-muted"><i class="fas fa-phone"></i> <?= htmlspecialchars($row['phone_number']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['order_date'])) ?></td>
                                            <td><strong class="text-danger"><?= number_format($row['total_money']) ?> đ</strong></td>
                                            <td>
                                                <?php
                                                if ($row['status'] == 0) echo '<span class="badge bg-warning text-dark">Mới đặt</span>';
                                                elseif ($row['status'] == 1) echo '<span class="badge bg-primary">Đang xử lý</span>';
                                                elseif ($row['status'] == 2) echo '<span class="badge bg-info text-dark">Đang giao</span>';
                                                elseif ($row['status'] == 3) echo '<span class="badge bg-success">Hoàn thành</span>';
                                                else echo '<span class="badge bg-danger">Đã hủy</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <a href="order_detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info text-white me-2" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="orders.php" method="POST" class="d-flex align-items-center mb-0 me-2">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <select name="status" class="form-select form-select-sm me-1" style="width: auto;">
                                                            <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Mới đặt</option>
                                                            <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Đang xử lý</option>
                                                            <option value="2" <?= $row['status'] == 2 ? 'selected' : '' ?>>Đang giao</option>
                                                            <option value="3" <?= $row['status'] == 3 ? 'selected' : '' ?>>Hoàn thành</option>
                                                            <option value="4" <?= $row['status'] == 4 ? 'selected' : '' ?>>Hủy</option>
                                                        </select>
                                                        <button type="submit" name="update_status" class="btn btn-sm btn-dark">Lưu</button>
                                                    </form>
                                                    <a href="orders.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa đơn hàng này?');"><i class="fas fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">Chưa có đơn hàng nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Tạo Đơn Hàng Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="orders.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Khách hàng</label>
                            <select class="form-select" name="customer_id" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php foreach ($customers_list as $cust): ?>
                                    <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['full_name']) ?> - <?= htmlspecialchars($cust['phone_number']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tổng tiền (VNĐ)</label>
                            <input type="number" class="form-control" name="total_money" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select class="form-select" name="payment_method" required>
                                <option value="Tiền mặt">Tiền mặt (COD)</option>
                                <option value="Chuyển khoản">Chuyển khoản</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="0">Mới đặt</option>
                                <option value="1">Đang xử lý</option>
                                <option value="2">Đang giao</option>
                                <option value="3">Hoàn thành</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="add_order" class="btn btn-success fw-bold">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>