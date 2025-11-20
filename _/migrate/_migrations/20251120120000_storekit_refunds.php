<?php

return [
	"ALTER TABLE `user_payment` ADD COLUMN `product_id` VARCHAR(100) NULL AFTER `price`;",
	"ALTER TABLE `user_payment` ADD COLUMN `transaction_id` VARCHAR(100) NULL AFTER `product_id`;",
	"ALTER TABLE `user_payment` ADD COLUMN `original_transaction_id` VARCHAR(100) NULL AFTER `transaction_id`;",
	"ALTER TABLE `user_payment` ADD COLUMN `purchase_date` TIMESTAMP NULL DEFAULT NULL AFTER `original_transaction_id`;",
	"ALTER TABLE `user_payment` ADD COLUMN `signed_transaction_info` MEDIUMTEXT NULL AFTER `purchase_date`;",
	"ALTER TABLE `user_payment` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'completed' AFTER `signed_transaction_info`;",
	"ALTER TABLE `user_payment` ADD COLUMN `verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;",
	"ALTER TABLE `user_payment` ADD COLUMN `refund_date` TIMESTAMP NULL DEFAULT NULL AFTER `verified`;",
	"ALTER TABLE `user_payment` ADD COLUMN `revocation_reason` VARCHAR(50) NULL AFTER `refund_date`;",
	"CREATE INDEX IF NOT EXISTS `idx_transaction_id` ON `user_payment`(`transaction_id`);",
	"CREATE INDEX IF NOT EXISTS `idx_original_transaction_id` ON `user_payment`(`original_transaction_id`);",
	"CREATE INDEX IF NOT EXISTS `idx_user_status` ON `user_payment`(`user_id`, `status`);",
	// поле для анлока всех обоев у пользователя
	"ALTER TABLE `users` ADD COLUMN `has_unlocked_all` TINYINT(1) NOT NULL DEFAULT 0 AFTER `count_generation`;",
	// таблица логов возвратов
	"CREATE TABLE IF NOT EXISTS `refund_log` (\r\n\t`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\r\n\t`user_id` INT NOT NULL,\r\n\t`payment_id` INT NOT NULL,\r\n\t`transaction_id` VARCHAR(100) NULL,\r\n\t`product_id` VARCHAR(100) NULL,\r\n\t`revocation_reason` INT NULL,\r\n\t`processed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\r\n\tPRIMARY KEY (`id`),\r\n\tKEY `idx_user_payment` (`user_id`,`payment_id`)\r\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
];


