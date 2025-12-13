

CREATE DATABASE IF NOT EXISTS `pos_warung` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pos_warung`;

CREATE TABLE IF NOT EXISTS `products` (
  `id` BIGINT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(100) NOT NULL,
  `price` INT NOT NULL DEFAULT 0,
  `stock` INT NOT NULL DEFAULT 0,
  `image` VARCHAR(255) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT 'Umum',
  `unit` VARCHAR(50) DEFAULT 'pcs'
);

CREATE TABLE IF NOT EXISTS `members` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `code` VARCHAR(100) NOT NULL,
  `points` INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `sales` (
  `id` BIGINT PRIMARY KEY,
  `date` DATETIME NOT NULL,
  `total` INT NOT NULL,
  `mode` VARCHAR(50) DEFAULT 'TUNAI'
);

CREATE TABLE IF NOT EXISTS `sale_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sale_id` BIGINT NOT NULL,
  `product_id` BIGINT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `price` INT NOT NULL,
  `qty` INT NOT NULL,
  FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_address` TEXT,
  `total_amount` INT NOT NULL DEFAULT 0,
  `discount` INT NOT NULL DEFAULT 0,
  `final_amount` INT NOT NULL DEFAULT 0,
  `payment_method` VARCHAR(50) DEFAULT 'TUNAI',
  `payment_proof` VARCHAR(255) DEFAULT NULL,
  `payment_status` VARCHAR(20) DEFAULT 'unpaid',
  `status` VARCHAR(50) DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` BIGINT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `price` INT NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` INT NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `monthly_reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `year` INT NOT NULL,
  `month` INT NOT NULL,
  `total_orders` INT DEFAULT 0,
  `total_revenue` INT DEFAULT 0,
  `total_items` INT DEFAULT 0,
  `avg_order_value` INT DEFAULT 0,
  `top_product` VARCHAR(255),
  `top_product_qty` INT DEFAULT 0,
  `report_data` LONGTEXT,
  `generated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `year_month` (`year`, `month`)
);

-- sample products
INSERT INTO `products` (`id`,`name`,`code`,`price`,`stock`,`image`,`category`,`unit`) VALUES
(1,'Royal Canin Kitten 1kg','RC01',85000,15,NULL,'Makanan Kucing','pack'),
(2,'Pedigree Adult 1kg','PD01',65000,25,NULL,'Makanan Anjing','pack'),
(3,'Tetra Fish Food 100g','TF01',25000,40,NULL,'Makanan Ikan','pack'),
(4,'Canary Seed Mix 500g','CS01',35000,30,NULL,'Makanan Burung','pack'),
(5,'Pet Collar Size M','PC01',45000,20,NULL,'Aksesoris Hewan','pcs'),
(6,'Pet Vitamins 60 tablets','PV01',75000,35,NULL,'Vitamin & Obat','botol');
