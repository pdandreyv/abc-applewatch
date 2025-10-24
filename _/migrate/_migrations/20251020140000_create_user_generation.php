<?php

return [
    MIGRATION_UP => [
        "CREATE TABLE IF NOT EXISTS `user_generation` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `style_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `color_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `prompt` TEXT,
        `img` VARCHAR(255) NOT NULL DEFAULT '',
        `created_at` DATETIME NULL,
        PRIMARY KEY (`id`),
        KEY `user_style_idx` (`user_id`,`style_id`),
        KEY `style_idx` (`style_id`),
        KEY `color_idx` (`color_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ",
    ],
    MIGRATION_DOWN => [
        "DROP TABLE IF EXISTS `user_generation`;",
    ],
];



