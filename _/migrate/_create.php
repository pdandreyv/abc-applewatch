<?php
$migration_name = trim($_GET['create']) ?: '';
$migration_name = implode('_', [date('YmdHis'), $migration_name]) . '.php';
//создание папки
if (is_dir($table) || mkdir($table,0755,true)) {
	copy('_template.php', $table . '/' . $migration_name);
	echo_success_message('Миграция создана');
}

