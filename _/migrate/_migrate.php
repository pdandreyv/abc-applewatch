<?php

$accept_name = !empty($_GET['name']) ? $_GET['name'] : null;
//
$applied = 0;
foreach ($migration_files as $filename) {
    $migration_commands = [];
    $migration_name = basename($filename);
    // пропускаем если установлена или задали конкретное имя миграции
    if (in_array($migration_name, $migrated_names)
        || ($accept_name && ($accept_name != $migration_name))
    ) {
        continue;
    }
    //
    $migration_commands = require($filename);
    $queries = (isset($migration_commands[MIGRATION_UP]) && is_array($migration_commands[MIGRATION_UP]))
        ? $migration_commands[MIGRATION_UP] : [];
    //
    try {
        foreach ($queries as $query) {
            mysql_fn('query', $query);
        }
        $applied++;
        echo_success_message('Миграция "' . $migration_name . '" успешно завершена.');
        mysql_fn('insert', $table, [
            'name' => $migration_name,
	        'ip'   => get_ip(),
           // 'migrated_at' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        echo_error_message('Ошибка накатывания миграции: "' . $migration_name . '".');
    }
}

if (!$applied) {
    echo_message('Все миграции уже установлены.');
}
