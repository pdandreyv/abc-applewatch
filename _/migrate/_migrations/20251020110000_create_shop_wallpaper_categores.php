<?php

return [
    MIGRATION_UP => [
        "\n        CREATE TABLE IF NOT EXISTS `shop_wallpaper_categores` (\n            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,\n            `name` VARCHAR(255) NOT NULL DEFAULT '',\n            `img` VARCHAR(255) NOT NULL DEFAULT '',\n            `sort` INT UNSIGNED NOT NULL DEFAULT 0,\n            PRIMARY KEY (`id`),\n            KEY `sort_idx` (`sort`)\n        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n        ",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `shop_wallpaper_categores`;",
    ],
];



