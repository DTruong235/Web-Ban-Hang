<?php
require_once '../db.php';

// --- 1. THÊM HÃNG ---
if (isset($_POST['add_brand'])) {
    $name = trim($_POST['name']);
    $stmt = $conn->prepare("INSERT INTO brands (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'brands.php';
    header("Location: $referer");
    exit();
}

// --- 2. SỬA HÃNG ---
if (isset($_POST['edit_brand'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $stmt = $conn->prepare("UPDATE brands SET name=? WHERE id=?");
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'brands.php';
    header("Location: $referer");
    exit();
}

// --- 3. XÓA HÃNG ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'brands.php';
    header("Location: $referer");
    exit();
}

// --- 4. TÌM KIẾM & LỌC ---
$search_kw = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT * FROM brands WHERE 1=1";

if ($search_kw !== '') {
    $escaped_search = $conn->real_escape_string($search_kw);
    $sql .= " AND name LIKE '%$escaped_search%'";
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hãng Sản Xuất - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #005a3c; }
        .sidebar a { color: #fff; text-decoration: none; padding: 12px 20px; display: block; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #00452d; border-left: 4px solid #f9b612; }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 sidebar text-white">
            <div class="p-4 text-center border-bottom border-secondary">
                <h4 class="fw-bold text-warning mb-0">BÁCH HÓA PEW</h4>
                <div class="mt-2 small opacity-75">
                    <i class="fas fa-user-shield"></i> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin' ?><br>
                    <span style="font-size: 0.8em;">ID: <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'ADMIN' ?></span>
                </div>
            </div>
            <div class="mt-3">
                <a href="categorys.php"><i class="fas fa-list me-2"></i> Danh Mục Gốc</a>
                <a href="sub_categories.php"><i class="fas fa-tags me-2"></i> Thể Loại Con</a>
                <a href="brands.php" class="active"><i class="fas fa-copyright me-2"></i> Hãng Sản Xuất</a>
                <a href="products.php"><i class="fas fa-box me-2"></i> Sản Phẩm</a>
                <a href="customers.php"><i class="fas fa-users me-2"></i> Khách Hàng</a>
                <a href="orders.php"><i class="fas fa-file-invoice-dollar me-2"></i> Đơn Hàng</a>
                <a href="../logout.php" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-10 p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Quản lý Hãng Sản Xuất</h2>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                    <i class="fas fa-plus"></i> Thêm Hãng
                </button>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body bg-white rounded">
                    <form action="brands.php" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nhập tên hãng cần tìm..." value="<?= htmlspecialchars($search_kw) ?>">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">Tìm</button>
                            <a href="brands.php" class="btn btn-outline-secondary" title="Tải lại tất cả"><i class="fas fa-redo"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Tên Hãng Sản Xuất</th>
                                <th>Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td class="fw-bold text-danger fs-5"><?= htmlspecialchars($row['name']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBrand<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                                            <a href="brands.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa hãng này?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="editBrand<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-start">
                                                <div class="modal-header bg-warning text-dark">
                                                    <h5 class="modal-title fw-bold">Sửa Hãng</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="brands.php" method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tên Hãng</label>
                                                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="edit_brand" class="btn btn-warning fw-bold">Cập nhật</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3">Không tìm thấy hãng nào</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Thêm Hãng Sản Xuất</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="brands.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên Hãng</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_brand" class="btn btn-success fw-bold">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>