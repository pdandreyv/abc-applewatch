<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `packages` ADD COLUMN `display` TINYINT(1) NOT NULL DEFAULT 1 AFTER `sort`;",
        "ALTER TABLE `packages` ADD INDEX `display_idx` (`display`);",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `packages` DROP INDEX `display_idx`;",
        "ALTER TABLE `packages` DROP COLUMN `display`;",
    ],
];
