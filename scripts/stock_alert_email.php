<?php
require_once __DIR__ . '/../db.php';

$adminEmail = 'admin@bachhoapew.local';
$siteName = 'Bách Hóa Pew';
$threshold = 5; // Ngưỡng cảnh báo

$sqlLowStock = "SELECT id, name, stock_quantity FROM products WHERE stock_quantity <= $threshold ORDER BY stock_quantity ASC";
$result = $conn->query($sqlLowStock);

if (!$result || $result->num_rows == 0) {
    echo "Không có sản phẩm thiếu hàng hiện tại.\n";
    exit;
}

$lines = [];
while ($row = $result->fetch_assoc()) {
    $lines[] = "- {$row['name']} (ID: {$row['id']}) - còn {$row['stock_quantity']}";
}

$subject = "Cảnh báo tồn kho thấp - $siteName";
$message = "Danh sách sản phẩm tồn kho thấp (<= $threshold) tại $siteName:\n\n";
$message .= implode("\n", $lines);
$message .= "\n\nVui lòng nạp kho kịp thời.";

$headers = "From: no-reply@bachhoapew.local" . "\r\n" .
           "Reply-To: no-reply@bachhoapew.local" . "\r\n" .
           "X-Mailer: PHP/" . phpversion();

if (mail($adminEmail, $subject, $message, $headers)) {
    echo "Đã gửi cảnh báo tồn kho tới $adminEmail";
} else {
    echo "Gửi email cảnh báo thất bại. Kiểm tra cấu hình mail server.";
}
