<?php

return [
    MIGRATION_UP => [
        "
        CREATE TABLE IF NOT EXISTS `shop_wallpaper` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            `category` INT UNSIGNED NOT NULL DEFAULT 0,
            `img` VARCHAR(255) DEFAULT '',
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            `display` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `category` (`category`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ",
        // обновление updated_at триггером можно добавить позже при необходимости
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `shop_wallpaper`;",
    ],
];


