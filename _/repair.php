<?php

/*
 * чинит все таблицы
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');

mysql_connect_db();

$query = "SHOW TABLES";
if ($tables = mysql_select($query,'rows')) {
	foreach ($tables as $table) {
		$query = "REPAIR TABLE `" . array_shift($table) . "`";
		echo $query . '<br />';
		mysql_fn('query',$query);
	}
}

?>