<?php

return [
    MIGRATION_UP => [
        "CREATE TABLE IF NOT EXISTS `wallpaper_languages` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            `sort` INT UNSIGNED NOT NULL DEFAULT 0,
            `display` TINYINT(1) NOT NULL DEFAULT 1,
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            `localization` VARCHAR(16) NOT NULL DEFAULT '',
            `dictionary` MEDIUMTEXT NULL,
            PRIMARY KEY (`id`),
            KEY `sort_idx` (`sort`),
            KEY `display_idx` (`display`),
            UNIQUE KEY `localization_unique` (`localization`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `wallpaper_languages`;",
    ],
];


