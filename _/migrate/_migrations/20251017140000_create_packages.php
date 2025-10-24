<?php

return [
    MIGRATION_UP => [
        "CREATE TABLE IF NOT EXISTS `packages` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `count` INT UNSIGNED NOT NULL DEFAULT 0,
            `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `packages`;",
    ],
];


