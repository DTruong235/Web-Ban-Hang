<?php
// Hàm lấy sản phẩm tương tự dựa trên Category và Price
function getRelatedProducts($conn, $current_product_id, $category_id, $price) {
    // 1. Tính khoảng giá (ví dụ chênh lệch 30%)
    $min_price = $price * 0.7;
    $max_price = $price * 1.3;

    // 2. Câu SQL sử dụng logic tính điểm (Scoring)
    // Ưu tiên 1: Cùng category (điểm cao nhất)
    // Ưu tiên 2: Nằm trong khoảng giá (điểm cộng thêm)
    $sql = "SELECT *, 
            ( (CASE WHEN category_id = $category_id THEN 5 ELSE 0 END) + 
              (CASE WHEN price BETWEEN $min_price AND $max_price THEN 2 ELSE 0 END) 
            ) AS similarity_score
            FROM products 
            WHERE id != $current_product_id 
            ORDER BY similarity_score DESC, id DESC 
            LIMIT 4";

    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>