<?php
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($q === '') {
    echo json_encode(['success' => false, 'items' => []]);
    exit;
}

$q_escaped = $conn->real_escape_string($q);
$sql = "SELECT id, name, price, discount_price, cover_image FROM products " .
       "WHERE status = 1 AND name LIKE '%$q_escaped%' ORDER BY id DESC LIMIT 8";
$res = $conn->query($sql);
$items = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'discount_price' => $row['discount_price'],
            'cover_image' => $row['cover_image'],
        ];
    }
}

echo json_encode(['success' => true, 'items' => $items]);
