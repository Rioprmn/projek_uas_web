-- SQL script to create database and tables for POS Warung
-- Run via phpMyAdmin or the included init_db.php

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
(1,'Beras 5kg','BR05',65000,20,NULL,'Beras','pack'),
(2,'Gula 1kg','GL01',15000,50,NULL,'Bumbu','pack'),
(3,'Minyak 2L','MK02',30000,30,NULL,'Minyak','botol'),
(4,'Sarden Kaleng','SD01',12000,60,NULL,'Makanan','pcs'),
(5,'Rokok A','RK01',20000,80,NULL,'Rokok','pcs'),
(6,'Kopi 250g','KP25',22000,40,NULL,'Minuman','pack');
