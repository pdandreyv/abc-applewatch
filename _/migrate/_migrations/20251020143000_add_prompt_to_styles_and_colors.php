<?php

return [
    MIGRATION_UP => [
        "ALTER TABLE `styles` ADD COLUMN `prompt` TEXT AFTER `name`;",
        "ALTER TABLE `colors` ADD COLUMN `prompt` TEXT AFTER `code`;",
    ],
    MIGRATION_DOWN => [
        "ALTER TABLE `styles` DROP COLUMN `prompt`;",
        "ALTER TABLE `colors` DROP COLUMN `prompt`;",
    ],
];



