-- Migration: add image to products and payment fields to orders
ALTER TABLE `products` 
  ADD COLUMN IF NOT EXISTS `image` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(50) DEFAULT 'TUNAI',
  ADD COLUMN IF NOT EXISTS `payment_proof` VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(20) DEFAULT 'unpaid';

-- Note: MySQL versions prior to 8.0 do not support IF NOT EXISTS on ADD COLUMN.
-- If your server errors, run the following commands instead (one by one):
-- ALTER TABLE `products` ADD COLUMN `image` VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE `orders` ADD COLUMN `payment_method` VARCHAR(50) DEFAULT 'TUNAI';
-- ALTER TABLE `orders` ADD COLUMN `payment_proof` VARCHAR(255) DEFAULT NULL;
-- ALTER TABLE `orders` ADD COLUMN `payment_status` VARCHAR(20) DEFAULT 'unpaid';
