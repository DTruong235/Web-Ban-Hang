<?php
require_once '../db.php';

// XỬ LÝ CẬP NHẬT TRẠNG THÁI
if (isset($_POST['update_contact'])) {
    $id = (int)$_POST['id'];
    $status = (int)$_POST['status'];
    $stmt = $conn->prepare("UPDATE contacts SET status = ? WHERE id = ?");
    $stmt->bind_param('ii', $status, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: contacts.php'); exit;
}

// XÓA
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: contacts.php'); exit;
}

// LẤY DỮ LIỆU
$sql = "SELECT * FROM contacts ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Liên hệ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container-fluid">
 <div class="row">
  <div class="col-md-2 p-0 sidebar text-white" style="min-height:100vh;background:#005a3c;">
    <div class="p-3 text-center border-bottom border-secondary">
      <h4 class="fw-bold text-warning">BÁCH HÓA PEW</h4>
      <div class="small opacity-75">Admin</div>
    </div>
    <a href="categorys.php">Danh Mục</a>
    <a href="sub_categories.php">Thể Loại</a>
    <a href="brands.php">Hãng</a>
    <a href="products.php">Sản Phẩm</a>
    <a href="customers.php">Khách Hàng</a>
    <a href="orders.php">Đơn Hàng</a>
    <a href="reviews.php">Đánh giá</a>
    <a href="contacts.php" class="text-warning">Liên hệ</a>
    <a href="../logout.php" class="text-danger mt-5">Đăng xuất</a>
  </div>
  <div class="col-md-10 p-4">
    <h2 class="mb-3">Danh sách liên hệ</h2>
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <table class="table table-bordered table-hover table-sm align-middle">
          <thead class="table-light"><tr>
            <th>ID</th><th>Họ tên</th><th>Email</th><th>Phone</th><th>Chủ đề</th><th>Câu hỏi</th><th>Ngày</th><th>Trạng thái</th><th>Hành động</th>
          </tr></thead>
          <tbody>
          <?php if ($result && $result->num_rows): while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['subject']) ?></td>
            <td style="max-width:250px;word-wrap:break-word;"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
            <td>
              <?php if ($row['status']==0) echo '<span class="badge bg-warning text-dark">Chưa xử lý</span>';
                    elseif ($row['status']==1) echo '<span class="badge bg-success">Đã xử lý</span>'; ?>
            </td>
            <td>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="status" value="<?= $row['status']==1 ? 0 : 1 ?>">
                <button class="btn btn-sm btn-<?= $row['status']==1 ? 'secondary' : 'success' ?>"><?= $row['status']==1 ? 'Bỏ xử lý' : 'Đã xử lý' ?></button>
              </form>
              <a href="contacts.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa liên hệ?');">Xóa</a>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="9" class="text-center">Chưa có liên hệ mới</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
 </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
