<?php
require_once __DIR__ . '/../db.php';

// Cấu hình email quản trị
$adminEmail = 'admin@bachhoapew.local';
$siteName = 'Bách Hóa Pew';

$startDate = date('Y-m-d', strtotime('-7 days'));
$endDate = date('Y-m-d');

// Thống kê đơn hàng trong tuần
$sqlOrders = "SELECT COUNT(*) AS total_orders, SUM(total_money) AS total_revenue 
              FROM orders 
              WHERE DATE(order_date) BETWEEN '$startDate' AND '$endDate'";
$orderStats = $conn->query($sqlOrders)->fetch_assoc();

// Khách hàng mới trong tuần dựa trên đơn hàng tuần
$sqlCustomers = "SELECT COUNT(DISTINCT c.id) AS new_customers 
                 FROM customers c 
                 JOIN orders o ON o.customer_id = c.id 
                 WHERE DATE(o.order_date) BETWEEN '$startDate' AND '$endDate'";
$customerStats = $conn->query($sqlCustomers)->fetch_assoc();

$subject = "Báo cáo tuần: $startDate đến $endDate";
$message = "Báo cáo kinh doanh của $siteName từ $startDate đến $endDate:\n\n";
$message .= "Tổng đơn hàng: " . ($orderStats['total_orders'] ?: 0) . "\n";
$message .= "Doanh thu: " . number_format($orderStats['total_revenue'] ?: 0) . " đ\n";
$message .= "Khách hàng mới: " . ($customerStats['new_customers'] ?: 0) . "\n\n";
$message .= "Hãy kiểm tra hệ thống để biết chi tiết.\n";
$message .= "Trân trọng,\n$siteName";

$headers = "From: no-reply@bachhoapew.local" . "\r\n" .
           "Reply-To: no-reply@bachhoapew.local" . "\r\n" .
           "X-Mailer: PHP/" . phpversion();

if (mail($adminEmail, $subject, $message, $headers)) {
    echo "Báo cáo tuần đã được gửi tới $adminEmail";
} else {
    echo "Gửi email thất bại. Vui lòng kiểm tra cấu hình mail server.";
}
