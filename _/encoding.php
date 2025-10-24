<?php

/*
 * скрипт для установления правильной кодировки базы
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/mysql_func.php');

mysql_connect_db();

//список скл запросов
$queries = array(
	//установка кодировки базы
	"ALTER DATABASE ".$config['mysql_database']." DEFAULT CHARACTER SET utf8 COLLATE utf8_bin",
	//установка кодировки таблицы
	"ALTER TABLE `news` CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin"
);
//установка всех таблиц
$query = "SHOW TABLES";
if ($tables = mysql_select($query,'rows')) {
	$i = 0;
	foreach ($tables as $table) {
		$queries[] = "ALTER TABLE `" . array_shift($table) . "` CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin";
	}
}
foreach ($queries as $query) {
	if ($query) {
		if (mysql_fn('query',$query,'affected_rows')) echo '<div style="color:#00f">'.$query.'</div>';
		else echo '<div style="color:#f00">'.$query.' - '.mysql_error().'</div>';
	}
}
