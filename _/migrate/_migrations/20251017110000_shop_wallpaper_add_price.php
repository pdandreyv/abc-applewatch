<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `shop_wallpaper` ADD COLUMN `price` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `name`;",
        // индекс может пригодиться для выборок по бесплатным
        "ALTER TABLE `shop_wallpaper` ADD INDEX `price_idx` (`price`);",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `shop_wallpaper` DROP INDEX `price_idx`;",
        "ALTER TABLE `shop_wallpaper` DROP COLUMN `price`;",
    ],
];


