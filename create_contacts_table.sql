-- =========================================
-- BẢNG CONTACTS (LƯU CÁC LIÊN HỆ TỪ KHÁCH HÀNG)
-- =========================================

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) DEFAULT 0 COMMENT '0 = Chưa xử lý, 1 = Đã xử lý',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
