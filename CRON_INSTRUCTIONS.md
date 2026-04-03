# Hướng dẫn CRON cho các tác vụ email tự động

Các tập tin script đã tạo:
- `scripts/send_weekly_report.php` : Gửi email báo cáo tuần (đơn hàng + doanh thu + khách hàng mới).
- `scripts/stock_alert_email.php` : Gửi email cảnh báo tồn kho khi sản phẩm <= ngưỡng.

## Thiết lập trên Linux (cron)
Sửa crontab với `crontab -e` và thêm:

- Gửi báo cáo tuần vào 8h sáng Chủ nhật:
`0 8 * * 0 /usr/bin/php /path/to/your/project/scripts/send_weekly_report.php`

- Kiểm tra tồn kho mỗi ngày 7h sáng:
`0 7 * * * /usr/bin/php /path/to/your/project/scripts/stock_alert_email.php`

## Thiết lập trên Windows Task Scheduler
1. Tạo Task mới.
2. Chọn `Action` -> `Start a program`.
3. `Program/Script`: đường dẫn đến PHP CLI, ví dụ: `C:\php\php.exe`.
4. `Add arguments`: `C:\path\to\your\project\scripts\send_weekly_report.php`.
5. Lập lịch hằng tuần/hằng ngày.

## Cấu hình Email
- PHP sử dụng hàm `mail()`; cần server mail (Sendmail/postfix) hoặc cấu hình SMTP trên php.ini (sendmail_path).
- Nếu máy chủ không hỗ trợ `mail()`, cân nhắc dùng thư viện PHPMailer và SMTP.
