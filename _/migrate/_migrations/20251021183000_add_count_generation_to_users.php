<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `users` ADD COLUMN `count_generation` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `apple_id`;",
        "ALTER TABLE `users` ADD INDEX `count_generation_idx` (`count_generation`);",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `users` DROP INDEX `count_generation_idx`;",
        "ALTER TABLE `users` DROP COLUMN `count_generation`;",
    ],
];


