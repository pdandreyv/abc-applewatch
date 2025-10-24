<?php
$step_count = intval($_GET['rollback']);
$step_count = $step_count > 0 ? $step_count : 0;
$migrated_count = count($migrated_names);
//
if ($migrated_count < $step_count) {
    echo_error_message("Неверное значение количества миграций. Всего миграций {$migrated_count}, вы пытаетесь откатить {$step_count} шагов");
    exit;
}
//
if ($step_count > 0) {
    $migrated_names = array_slice($migrated_names, $step_count * -1);
}
//
$applied = 0;
foreach ($migrated_names as $migration_name) {
    $filename = $table . '/' . $migration_name;
    if (file_exists($filename)) {
        $migration_commands = require($filename);
        $queries = (isset($migration_commands[MIGRATION_DOWN]) && is_array($migration_commands[MIGRATION_DOWN]))
            ? $migration_commands[MIGRATION_DOWN] : [];
        try {
            foreach ($queries as $query) {
                mysql_fn('query', $query);
            }
            mysql_fn('delete', $table, [], " AND `name` LIKE '{$migration_name}'");
            $applied++;
            echo_success_message('Миграция "' . $migration_name . '" успешно удалена.');
        } catch (Exception $e) {
            echo_error_message('Ошибка удаления миграции: "' . $migration_name . '".');
        }
    } else {
        echo_error_message("Файл миграции '{$migrated_name}' не найден");
        mysql_fn('delete', $table, [], " AND `name` LIKE '{$migrated_name}'");
    }
}

if (!$applied) {
    echo_error_message("Нет миграций для отката.");
} else {
    echo_success_message("Успешно откачено {$applied} миграций.");
}
