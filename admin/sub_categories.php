<?php
require_once '../db.php';

// Lấy danh sách danh mục gốc để chọn
$categories = [];
$cat_res = $conn->query("SELECT id, name FROM categories WHERE status = 1");
if ($cat_res) while ($c = $cat_res->fetch_assoc()) { $categories[] = $c; }

// --- 1. THÊM THỂ LOẠI CON ---
if (isset($_POST['add_subcat'])) {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $image = NULL;

    // Xử lý upload ảnh icon cho thể loại
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . '_sub_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "uploads/" . $file_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO sub_categories (category_id, name, image) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $category_id, $name, $image);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sub_categories.php';
    header("Location: $referer");
    exit();
}

// --- 2. SỬA THỂ LOẠI CON ---
if (isset($_POST['edit_subcat'])) {
    $id = (int)$_POST['id'];
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    
    // Nếu có upload ảnh mới thì cập nhật, không thì giữ nguyên
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . '_sub_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = "uploads/" . $file_name;
            $stmt = $conn->prepare("UPDATE sub_categories SET category_id=?, name=?, image=? WHERE id=?");
            $stmt->bind_param("issi", $category_id, $name, $image, $id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        $stmt = $conn->prepare("UPDATE sub_categories SET category_id=?, name=? WHERE id=?");
        $stmt->bind_param("isi", $category_id, $name, $id);
        $stmt->execute();
        $stmt->close();
    }
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sub_categories.php';
    header("Location: $referer");
    exit();
}

// --- 3. XÓA THỂ LOẠI CON ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM sub_categories WHERE id = $id");
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sub_categories.php';
    header("Location: $referer");
    exit();
}
// ==========================================
// --- 4. XỬ LÝ LỌC & TÌM KIẾM THỂ LOẠI CON ---
// ==========================================

$search_kw = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_cat = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT sc.*, c.name as cat_name 
        FROM sub_categories sc 
        JOIN categories c ON sc.category_id = c.id 
        WHERE 1=1";

if ($search_kw !== '') {
    $escaped_search = $conn->real_escape_string($search_kw);
    $sql .= " AND sc.name LIKE '%$escaped_search%'";
}

if ($filter_cat > 0) {
    $sql .= " AND sc.category_id = $filter_cat";
}

$sql .= " ORDER BY sc.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thể Loại Con - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { min-height: 100vh; background-color: #005a3c; }
        .sidebar a { color: #fff; text-decoration: none; padding: 12px 20px; display: block; transition: 0.3s; }
        .sidebar a:hover { background-color: #00452d; border-left: 4px solid #f9b612; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
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
                    <h2 class="fw-bold">Quản lý Thể Loại Con</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus"></i> Thêm thể loại</button>
                </div>
                <!-- Lọc và Tìm Kiếm -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body bg-white rounded">
                        <!-- Form Lọc -->
                        <form action="sub_categories.php" method="GET" class="row g-3 align-items-center">
                            
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nhập tên thể loại con..." value="<?= htmlspecialchars($search_kw) ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <select name="category" class="form-select">
                                    <option value="0">-- Tất cả danh mục gốc --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $filter_cat == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 fw-bold">Lọc</button>
                                <a href="sub_categories.php" class="btn btn-outline-secondary" title="Tải lại tất cả"><i class="fas fa-redo"></i></a>
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
                                    <th>Hình Ảnh</th>
                                    <th>Tên Thể Loại Con</th>
                                    <th>Thuộc Danh Mục Gốc</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td>
                                                <?php if (!empty($row['image'])): ?>
                                                    <img src="../<?= $row['image'] ?>" alt="Img" style="width: 50px; height: 50px; object-fit: contain; border-radius: 5px;">
                                                <?php else: ?>
                                                    <span class="text-muted small">Chưa có</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['cat_name']) ?></span></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                                                <a href="sub_categories.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thể loại này?');"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-warning text-dark">
                                                        <h5 class="modal-title fw-bold">Sửa Thể Loại Con</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="sub_categories.php" method="POST" enctype="multipart/form-data">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Thuộc danh mục gốc</label>
                                                                <select class="form-select" name="category_id" required>
                                                                    <?php foreach ($categories as $cat): ?>
                                                                        <option value="<?= $cat['id'] ?>" <?= $row['category_id']==$cat['id']?'selected':'' ?>><?= $cat['name'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên thể loại con</label>
                                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Đổi hình ảnh (Tùy chọn)</label>
                                                                <input type="file" class="form-control" name="image" accept="image/*">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" name="edit_subcat" class="btn btn-warning fw-bold">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="py-4 text-muted">Chưa có thể loại con nào.</td></tr>
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
                    <h5 class="modal-title fw-bold">Thêm Thể Loại Con</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="sub_categories.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Chọn Danh Mục Gốc</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tên Thể Loại Con (VD: Mì ly, Phở gói...)</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh đại diện</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="add_subcat" class="btn btn-success fw-bold">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>