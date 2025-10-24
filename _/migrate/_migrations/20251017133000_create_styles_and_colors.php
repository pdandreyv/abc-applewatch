<?php

return [
    MIGRATION_UP => [
        // styles
        "CREATE TABLE IF NOT EXISTS `styles` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",

        // colors
        "CREATE TABLE IF NOT EXISTS `colors` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL DEFAULT '',
            `code` VARCHAR(32) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `colors`;",
        "DROP TABLE IF EXISTS `styles`;",
    ],
];


