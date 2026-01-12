<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `packages` ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`;",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `packages` DROP COLUMN `title`;",
    ],
];