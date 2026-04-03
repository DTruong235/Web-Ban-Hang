<?php
require_once '../db.php';

// XỬ LÝ CHẤP THUẬN / TỪ CHỐI
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $conn->query("UPDATE reviews SET status = 1 WHERE id = $id");
    } elseif ($_GET['action'] === 'reject') {
        $conn->query("UPDATE reviews SET status = 2 WHERE id = $id");
    }
    header('Location: reviews.php'); exit;
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM reviews WHERE id = $id");
    header('Location: reviews.php'); exit;
}

$sql = "SELECT r.*, p.name AS product_name, u.fullname AS user_name
        FROM reviews r 
        LEFT JOIN products p ON r.product_id = p.id
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Đánh giá</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container-fluid">
 <div class="row">
  <div class="col-md-2 p-0 sidebar text-white" style="min-height:100vh;background:#005a3c;">
    <div class="p-3 text-center border-bottom border-secondary"><h4 class="fw-bold text-warning">BÁCH HÓA PEW</h4></div>
    <a href="categorys.php">Danh Mục</a><a href="sub_categories.php">Thể Loại</a><a href="brands.php">Hãng</a><a href="products.php">Sản Phẩm</a><a href="customers.php">Khách Hàng</a><a href="orders.php">Đơn Hàng</a><a href="reviews.php" class="text-warning">Đánh giá</a><a href="contacts.php">Liên hệ</a><a href="../logout.php" class="text-danger mt-5">Đăng xuất</a>
  </div>
  <div class="col-md-10 p-4">
    <h2 class="mb-3">Quản lý đánh giá sản phẩm</h2>
    <div class="card shadow-sm border-0"><div class="card-body">
      <table class="table table-hover table-bordered table-sm align-middle">
      <thead class="table-light"><tr><th>ID</th><th>Sản phẩm</th><th>Người đánh giá</th><th>Sao</th><th>Bình luận</th><th>Thời gian</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
      <tbody>
      <?php if ($result && $result->num_rows >0): while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['product_name']) ?></td>
        <td><?= htmlspecialchars($row['user_name'] ?: 'Khách') ?></td>
        <td><?= str_repeat('★', $row['rating']) . str_repeat('☆', 5-$row['rating']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['comment'])) ?: '<span class="text-muted">Không có</span>' ?></td>
        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
        <td>
          <?php if ($row['status']==0) echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                elseif ($row['status']==1) echo '<span class="badge bg-success">Đã duyệt</span>';
                else echo '<span class="badge bg-danger">Từ chối</span>'; ?>
        </td>
        <td>
          <?php if ($row['status'] != 1): ?><a class="btn btn-sm btn-success" href="reviews.php?action=approve&id=<?= $row['id'] ?>">Duyệt</a><?php endif; ?>
          <?php if ($row['status'] != 2): ?><a class="btn btn-sm btn-secondary" href="reviews.php?action=reject&id=<?= $row['id'] ?>">Từ chối</a><?php endif; ?>
          <a class="btn btn-sm btn-danger" href="reviews.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Xóa đánh giá?');">Xóa</a>
        </td>
      </tr>
      <?php endwhile; else: ?>
      <tr><td colspan="8" class="text-center">Chưa có đánh giá</td></tr>
      <?php endif; ?>
      </tbody></table>
    </div></div>
  </div>
 </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
