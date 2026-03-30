<?php
require_once 'db.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$limit = 4; // Mỗi lần bấm "Xem thêm" sẽ lấy thêm 4 sản phẩm

if ($type === 'sale') {
    // Lấy thêm sản phẩm Giảm giá
    $sql = "SELECT * FROM products 
            WHERE status = 1 AND discount_price IS NOT NULL 
            ORDER BY ((price - discount_price) / price) DESC 
            LIMIT $limit OFFSET $offset";
} elseif ($type === 'new') {
    // Lấy thêm sản phẩm Mới về
    $sql = "SELECT * FROM products 
            WHERE status = 1 
            ORDER BY id DESC 
            LIMIT $limit OFFSET $offset";
} else {
    exit();
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        $original_price = $product['price'];
        $final_price = ($product['discount_price'] != NULL) ? $product['discount_price'] : $original_price;
        
        ?>
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card product-card h-100 border-0 shadow-sm position-relative">
                
                <?php if($product['discount_price'] != NULL): ?>
                    <?php $percent = round((($original_price - $final_price) / $original_price) * 100); ?>
                    <div class="badge-discount position-absolute top-0 end-0 m-2 bg-danger text-white px-2 py-1 rounded" style="z-index: 10;">
                        -<?= $percent ?>%
                    </div>
                <?php endif; ?>

                <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                    <div class="product-img-wrapper">
                        <img src="<?= $product['cover_image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-cover">
                    </div>
                </a>

                <div class="card-body text-center">
                    <a href="product_detail.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                        <h6 class="card-title fw-bold" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 40px;" title="<?= htmlspecialchars($product['name']) ?>">
                            <?= htmlspecialchars($product['name']) ?>
                        </h6>
                    </a>

                    <?php if ($product['discount_price'] != NULL): ?>
                        <div class="text-muted text-decoration-line-through small"><?= number_format($original_price) ?> đ</div>
                        <div class="fw-bold text-dark mb-2"><?= number_format($final_price) ?> đ</div>
                        
                        <div class="text-center fw-bold mb-1 mt-1" style="font-size: 0.75rem; color: #ff7f00;">Tối đa 5 sp/đơn</div>
                        <div class="position-relative mb-3 mx-auto" style="max-width: 90%;">
                            <div class="progress" style="height: 22px; border-radius: 50px; background-color: #fef0cd;">
                                <?php 
                                    $stock = $product['stock_quantity']; 
                                    $percent_bar = ($stock <= 100) ? $stock : 50; 
                                ?>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent_bar ?>%; border-radius: 50px;"></div>
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle w-100 text-center" style="font-size: 0.8rem; font-weight: 600; color: #333; line-height: 22px;">
                                Còn <?= $stock ?> suất
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-muted small">&nbsp;</div>
                        <div class="fw-bold text-dark mb-2"><?= number_format($original_price) ?> đ</div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center align-items-center gap-3 mt-2">
                        <a href="javascript:void(0);" onclick="addToCart(<?= $product['id'] ?>)" class="text-tempi-green transition-zoom" title="Thêm vào giỏ">
                            <i class="fas fa-cart-plus fs-4"></i>
                        </a>
                        <a href="add_to_cart.php?id=<?= $product['id'] ?>&action=buy_now" class="btn btn-tempi-green flex-grow-1 text-white rounded-pill text-decoration-none text-center fw-bold shadow-sm">
                            Mua ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // Nếu hết sản phẩm, trả về rỗng để JS biết đường ẩn nút
    echo "";
}
?>