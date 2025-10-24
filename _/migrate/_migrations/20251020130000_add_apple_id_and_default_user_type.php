<?php

return [
    MIGRATION_UP => [
        // users.apple_id
        "ALTER TABLE `users` ADD COLUMN `apple_id` VARCHAR(255) NOT NULL DEFAULT '' AFTER `phone`;",
        "ALTER TABLE `users` ADD UNIQUE KEY `apple_id_unique` (`apple_id`);",

        // user_types default row
        "INSERT INTO `user_types` (`ut_name`,`access_admin`,`access_delete`,`access_ftp`,`access_editable`) SELECT 'Пользователь','',0,0,'' WHERE NOT EXISTS (SELECT 1 FROM `user_types` WHERE `ut_name`='Пользователь');",
    ],
    MIGRATION_DOWN => [
        // users.apple_id
        "ALTER TABLE `users` DROP INDEX `apple_id_unique`;",
        "ALTER TABLE `users` DROP COLUMN `apple_id`;",
        // delete default user type
        "DELETE FROM `user_types` WHERE `ut_name`='Пользователь';",
    ],
];



