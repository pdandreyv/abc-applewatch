<?php

return [
    MIGRATION_UP => [
        "CREATE TABLE IF NOT EXISTS `user_payment` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `package_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `user_idx` (`user_id`),
            KEY `package_idx` (`package_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `user_payment`;",
    ],
];


