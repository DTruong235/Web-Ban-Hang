<?php
require_once '../db.php';
// --- 1. XỬ LÝ THÊM DANH MỤC ---
if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']); // Thêm dòng này
    $status = (int)$_POST['status'];

    $stmt = $conn->prepare("INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $name, $slug, $description, $status); // Sửa lại bind_param
    $stmt->execute();
    $stmt->close();
    header("Location: categorys.php");
    exit();
}

// --- 2. XỬ LÝ SỬA DANH MỤC ---
if (isset($_POST['edit_category'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']); // Thêm dòng này
    $status = (int)$_POST['status'];

    $stmt = $conn->prepare("UPDATE categories SET name=?, slug=?, description=?, status=? WHERE id=?");
    $stmt->bind_param("sssii", $name, $slug, $description, $status, $id); // Sửa lại bind_param
    $stmt->execute();
    $stmt->close();
    header("Location: categorys.php");
    exit();
}

// --- 3. XỬ LÝ XÓA DANH MỤC (DELETE) ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: categorys.php?msg=delete_success");
    exit();
}

// --- 4. LẤY DANH SÁCH DANH MỤC (READ) ---
$sql = "SELECT * FROM categories ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tempi</title>
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
                    <h2 class="fw-bold">Quản lý Danh Mục</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Thêm mới
                    </button>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Danh Mục</th>
                                    <th>Đường dẫn (Slug)</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= htmlspecialchars($row['slug']) ?></td>
                                            <td>
                                                <?php if ($row['status'] == 1): ?>
                                                    <span class="badge bg-success">Hiển thị</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Đã ẩn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </button>
                                                <a href="categorys.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?');">
                                                    <i class="fas fa-trash"></i> Xóa
                                                </a>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-warning text-dark">
                                                        <h5 class="modal-title fw-bold">Sửa Danh Mục</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="categorys.php" method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên danh mục</label>
                                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Slug (Đường dẫn không dấu)</label>
                                                                <input type="text" class="form-control" name="slug" value="<?= htmlspecialchars($row['slug']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Bài viết giới thiệu (SEO)</label>
                                                                <textarea class="form-control" name="description" rows="5" placeholder="Nhập bài viết mô tả danh mục..."><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Trạng thái</label>
                                                                <select class="form-select" name="status">
                                                                    <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Hiển thị</option>
                                                                    <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Ẩn</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" name="edit_category" class="btn btn-warning fw-bold">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa có dữ liệu</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">Thêm Danh Mục Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="categorys.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên danh mục</label>
                            <input type="text" class="form-control" name="name" placeholder="Ví dụ: Đồ hộp" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug (Đường dẫn không dấu)</label>
                            <input type="text" class="form-control" name="slug" placeholder="Ví dụ: do-hop" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bài viết giới thiệu (SEO)</label>
                            <textarea class="form-control" name="description" rows="5" placeholder="Nhập bài viết mô tả danh mục..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="add_category" class="btn btn-success fw-bold">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>