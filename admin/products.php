<?php
require_once '../db.php';

// --- LẤY DANH MỤC & HÃNG SẢN XUẤT$Thể Loại sản phẩm CHO DROPDOWN ---
$categories = [];
$cat_res = $conn->query("SELECT id, name FROM categories WHERE status = 1");
if ($cat_res) while ($c = $cat_res->fetch_assoc()) {
    $categories[] = $c;
}

$brands = [];
$brand_res = $conn->query("SELECT id, name FROM brands");
if ($brand_res) while ($b = $brand_res->fetch_assoc()) {
    $brands[] = $b;
}
// Thể loại sản phẩm
$sub_categories = [];
$sub_res = $conn->query("SELECT sc.*, c.name as cat_name FROM sub_categories sc JOIN categories c ON sc.category_id = c.id");
if ($sub_res) while ($sc = $sub_res->fetch_assoc()) {
    $sub_categories[] = $sc;
}

// --- 1. THÊM SẢN PHẨM ---
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $cat_id = $_POST['category_id'] ? (int)$_POST['category_id'] : NULL;
    $brand_id = $_POST['brand_id'] ? (int)$_POST['brand_id'] : NULL;
    $price = (int)$_POST['price'];

    // Lấy giá khuyến mãi, nếu người dùng bỏ trống thì lưu là NULL
    $discount_price = !empty($_POST['discount_price']) ? (int)$_POST['discount_price'] : NULL;

    $stock = (int)$_POST['stock_quantity'];
    $status = (int)$_POST['status'];

    $cover_image = NULL;

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            $cover_image = "uploads/" . $file_name;
        }
    }

    $sub_cat_id = !empty($_POST['sub_category_id']) ? (int)$_POST['sub_category_id'] : NULL; // Bổ sung biến này

    // Câu lệnh INSERT mới
    $stmt = $conn->prepare("INSERT INTO products (category_id, sub_category_id, brand_id, name, price, discount_price, stock_quantity, status, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisiiiis", $cat_id, $sub_cat_id, $brand_id, $name, $price, $discount_price, $stock, $status, $cover_image);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header("Location: $referer");
    exit();
}

// --- 2. SỬA SẢN PHẨM ---
if (isset($_POST['edit_product'])) {
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $cat_id = $_POST['category_id'] ? (int)$_POST['category_id'] : NULL;
    $brand_id = $_POST['brand_id'] ? (int)$_POST['brand_id'] : NULL;
    $price = (int)$_POST['price'];

    // Lấy giá khuyến mãi cho phần cập nhật
    $discount_price = !empty($_POST['discount_price']) ? (int)$_POST['discount_price'] : NULL;

    $stock = (int)$_POST['stock_quantity'];
    $status = (int)$_POST['status'];

    $sub_cat_id = !empty($_POST['sub_category_id']) ? (int)$_POST['sub_category_id'] : NULL; // Bổ sung biến này

    // Câu lệnh UPDATE mới
    $stmt = $conn->prepare("UPDATE products SET category_id=?, sub_category_id=?, brand_id=?, name=?, price=?, discount_price=?, stock_quantity=?, status=? WHERE id=?");
    $stmt->bind_param("iiisiiiii", $cat_id, $sub_cat_id, $brand_id, $name, $price, $discount_price, $stock, $status, $id);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header("Location: $referer");
    exit();
}

// --- 3. XÓA SẢN PHẨM ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'products.php';
    header("Location: $referer");
    exit();
}

// ==========================================
// --- 4. XỬ LÝ LỌC & TÌM KIẾM SẢN PHẨM ---
// ==========================================

// Nhận dữ liệu từ form tìm kiếm (nếu có)
$search_kw = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_cat = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Khởi tạo câu lệnh SQL cơ bản
$sql = "SELECT p.*, c.name as cat_name, b.name as brand_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN brands b ON p.brand_id = b.id 
        WHERE 1=1"; // 1=1 để dễ dàng nối thêm các điều kiện AND bên dưới

// 4.1. Lọc theo từ khóa tìm kiếm
if ($search_kw !== '') {
    $escaped_search = $conn->real_escape_string($search_kw);
    $sql .= " AND p.name LIKE '%$escaped_search%'";
}

// 4.2. Lọc theo danh mục
if ($filter_cat > 0) {
    $sql .= " AND p.category_id = $filter_cat";
}

// 4.3. Sắp xếp dữ liệu
if ($sort_by === 'price_asc') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort_by === 'price_desc') {
    $sql .= " ORDER BY p.price DESC";
} else {
    $sql .= " ORDER BY p.id DESC"; // Mặc định là mới nhất
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản Phẩm - Admin</title>
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
                    <a href="products.php" style="background-color: #00452d; border-left: 4px solid #f9b612;"><i class="fas fa-box me-2"></i> Sản Phẩm</a>
                    <a href="customers.php"><i class="fas fa-users me-2"></i> Khách Hàng</a>
                    <a href="orders.php"><i class="fas fa-file-invoice-dollar me-2"></i> Đơn Hàng</a>
                    <a href="sub_categories.php"><i class="fas fa-tags me-2"></i> Thể Loại Con</a>
                    <a href="#" class="text-danger mt-5"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
                </div>
            </div>

            <div class="col-md-10 p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Quản lý Sản Phẩm</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Thêm sản phẩm
                    </button>
                </div>

                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body bg-white rounded">
                        <!-- Form Lọc -->
                        <form action="products.php" method="GET" class="row g-3 align-items-center">
                            <!-- Tìm Kiếm Sản Phẩm -->
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nhập tên sản phẩm..." value="<?= htmlspecialchars($search_kw) ?>">
                                </div>
                            </div>
                            <!-- Lọc dữ liệu theo danh mục -->
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="0">-- Tất cả danh mục --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $filter_cat == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="sort" class="form-select">
                                    <option value="newest" <?= $sort_by == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                                    <option value="price_asc" <?= $sort_by == 'price_asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                                    <option value="price_desc" <?= $sort_by == 'price_desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 fw-bold">Lọc</button>
                                <a href="products.php" class="btn btn-outline-secondary" title="Tải lại tất cả"><i class="fas fa-redo"></i></a>
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
                                    <th>Tên Sản Phẩm</th>
                                    <th>Danh Mục</th>
                                    <th>Giá bán</th>
                                    <th>Giá khuyến mãi</th>
                                    <th>Tồn kho</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td>
                                                <?php if (!empty($row['cover_image'])): ?>
                                                    <img src="../<?= $row['cover_image'] ?>" alt="Img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                <?php else: ?>
                                                    <span class="text-muted small">No Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-start"><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= $row['cat_name'] ?? '<span class="text-muted">Trống</span>' ?></td>
                                            <td><strong class="text-danger"><?= number_format($row['price']) ?> đ</strong></td>
                                            <td><strong class="text-primary"><?= number_format($row['discount_price']) ?> đ</strong></td>
                                            <td><?= $row['stock_quantity'] ?></td>
                                            <td>
                                                <?= $row['status'] == 1 ? '<span class="badge bg-success">Đang bán</span>' : '<span class="badge bg-secondary">Ngừng bán</span>' ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                                <a href="products.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?');" title="Xóa"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header bg-warning text-dark">
                                                        <h5 class="modal-title fw-bold">Sửa Sản Phẩm</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="products.php" method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tên sản phẩm</label>
                                                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Giá bán (VNĐ)</label>
                                                                    <input type="number" class="form-control" name="price" value="<?= $row['price'] ?>" required>
                                                                </div>
                                                                <div class="col-4 mb-3">
                                                                    <label class="form-label">Giá khuyến mãi</label>
                                                                    <input type="number" class="form-control" name="discount_price" value="<?= $row['discount_price'] ?>" placeholder="Trống nếu ko giảm">
                                                                </div>
                                                                <div class="col-6 mb-3">
                                                                    <label class="form-label">Tồn kho</label>
                                                                    <input type="number" class="form-control" name="stock_quantity" value="<?= $row['stock_quantity'] ?>">
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Danh mục</label>
                                                                <select class="form-select" name="category_id">
                                                                    <option value="">-- Chọn danh mục --</option>
                                                                    <?php foreach ($categories as $cat): ?>
                                                                        <option value="<?= $cat['id'] ?>" <?= $row['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <!-- Thể loại con -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Thể loại con (Tùy chọn)</label>
                                                                <select class="form-select" name="sub_category_id">
                                                                    <option value="">-- Không phân loại --</option>
                                                                    <?php foreach ($sub_categories as $sc): ?>
                                                                        <option value="<?= $sc['id'] ?>" <?= (isset($row) && $row['sub_category_id'] == $sc['id']) ? 'selected' : '' ?>>
                                                                            [<?= $sc['cat_name'] ?>] - <?= $sc['name'] ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Hãng sản xuất</label>
                                                                <select class="form-select" name="brand_id">
                                                                    <option value="">-- Chọn hãng --</option>
                                                                    <?php foreach ($brands as $b): ?>
                                                                        <option value="<?= $b['id'] ?>" <?= $row['brand_id'] == $b['id'] ? 'selected' : '' ?>><?= $b['name'] ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Trạng thái</label>
                                                                <select class="form-select" name="status">
                                                                    <option value="1" <?= $row['status'] == 1 ? 'selected' : '' ?>>Đang bán</option>
                                                                    <option value="0" <?= $row['status'] == 0 ? 'selected' : '' ?>>Ngừng bán</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <button type="submit" name="edit_product" class="btn btn-warning fw-bold">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="py-4 text-muted">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</td>
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
                    <h5 class="modal-title fw-bold">Thêm Sản Phẩm Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="products.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh đại diện</label>
                            <input type="file" class="form-control" name="cover_image" accept="image/*">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Giá bán (VNĐ)</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Giá khuyến mãi</label>
                                <input type="number" class="form-control" name="discount_price" placeholder="Trống nếu ko giảm">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" class="form-control" name="stock_quantity" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id">
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thể loại con (Tùy chọn)</label>
                            <select class="form-select" name="sub_category_id">
                                <option value="">-- Không phân loại --</option>
                                <?php foreach ($sub_categories as $sc): ?>
                                    <option value="<?= $sc['id'] ?>" <?= (isset($row) && $row['sub_category_id'] == $sc['id']) ? 'selected' : '' ?>>
                                        [<?= $sc['cat_name'] ?>] - <?= $sc['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hãng sản xuất</label>
                            <select class="form-select" name="brand_id">
                                <option value="">-- Chọn hãng --</option>
                                <?php foreach ($brands as $b): ?>
                                    <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="1">Đang bán</option>
                                <option value="0">Ngừng bán</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="add_product" class="btn btn-success fw-bold">Lưu lại</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>