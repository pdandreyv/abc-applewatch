<?php

return [
    MIGRATION_UP => [
        // styles: add display
        "ALTER TABLE `styles` ADD COLUMN `display` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort`;",
        "ALTER TABLE `styles` ADD INDEX `display_idx` (`display`);",

        // colors: add display
        "ALTER TABLE `colors` ADD COLUMN `display` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort`;",
        "ALTER TABLE `colors` ADD INDEX `display_idx` (`display`);",

        // categories: add display
        "ALTER TABLE `shop_wallpaper_categores` ADD COLUMN `display` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort`;",
        "ALTER TABLE `shop_wallpaper_categores` ADD INDEX `display_idx` (`display`);",
    ],
    MIGRATION_DOWN => [
        // styles
        "ALTER TABLE `styles` DROP INDEX `display_idx`;",
        "ALTER TABLE `styles` DROP COLUMN `display`;",

        // colors
        "ALTER TABLE `colors` DROP INDEX `display_idx`;",
        "ALTER TABLE `colors` DROP COLUMN `display`;",

        // categories
        "ALTER TABLE `shop_wallpaper_categores` DROP INDEX `display_idx`;",
        "ALTER TABLE `shop_wallpaper_categores` DROP COLUMN `display`;",
    ],
];
