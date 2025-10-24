<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `user_generation` ADD COLUMN `display` TINYINT(1) NOT NULL DEFAULT 0 AFTER `img`;",
        "ALTER TABLE `user_generation` ADD INDEX `display_idx` (`display`);",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `user_generation` DROP INDEX `display_idx`;",
        "ALTER TABLE `user_generation` DROP COLUMN `display`;",
    ],
];


