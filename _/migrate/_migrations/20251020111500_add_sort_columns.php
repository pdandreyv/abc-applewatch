<?php

return [
    MIGRATION_UP => [
        // shop_wallpaper: add sort
        "ALTER TABLE `shop_wallpaper` ADD COLUMN `sort` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `price`;",
        "ALTER TABLE `shop_wallpaper` ADD INDEX `sort_idx` (`sort`);",

        // styles: add sort
        "ALTER TABLE `styles` ADD COLUMN `sort` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `name`;",
        "ALTER TABLE `styles` ADD INDEX `sort_idx` (`sort`);",

        // colors: add sort
        "ALTER TABLE `colors` ADD COLUMN `sort` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `code`;",
        "ALTER TABLE `colors` ADD INDEX `sort_idx` (`sort`);",

        // packages: add sort
        "ALTER TABLE `packages` ADD COLUMN `sort` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `price`;",
        "ALTER TABLE `packages` ADD INDEX `sort_idx` (`sort`);",
    ],
    MIGRATION_DOWN => [
        // shop_wallpaper
        "ALTER TABLE `shop_wallpaper` DROP INDEX `sort_idx`;",
        "ALTER TABLE `shop_wallpaper` DROP COLUMN `sort`;",

        // styles
        "ALTER TABLE `styles` DROP INDEX `sort_idx`;",
        "ALTER TABLE `styles` DROP COLUMN `sort`;",

        // colors
        "ALTER TABLE `colors` DROP INDEX `sort_idx`;",
        "ALTER TABLE `colors` DROP COLUMN `sort`;",

        // packages
        "ALTER TABLE `packages` DROP INDEX `sort_idx`;",
        "ALTER TABLE `packages` DROP COLUMN `sort`;",
    ],
];



