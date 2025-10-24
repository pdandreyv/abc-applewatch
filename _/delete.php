<?php

/*
 * удаляет все папки с сервера и базу
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'functions/file_func.php');
require_once(ROOT_DIR.'_config2.php');
require_once(ROOT_DIR.'functions/mysql_func.php');

if (@$_GET['action']=='file') {
	//удаление файлов
	$root = ROOT_DIR;
	if ($handle = opendir($root)) {
		while (false !== ($dir = readdir($handle))) {
			if (strlen($dir)>2 && is_dir($root.$dir)) {
				delete_all($root.$dir.'/',true);
				echo $dir.'<br>';
			}
		}
	}
}

if (@$_GET['action']=='db'){
	mysql_connect_db();
	$query = "SHOW TABLES";
	echo $query.'<br>';
	if ($tables = mysql_select($query,'rows')) {
		foreach ($tables as $table) {
			$query = "DROP TABLE `" . array_shift($table) . "`";
			mysql_fn('query',$query);
			echo $query . '<br>';
		}
	}
}
?>
<br><br><a href="?action=file"><strong>Удалить все файлы</strong></a>
<br><br>
<?php if ($config['mysql_database']) {?>
<br><br>
<a href="/_/database.php">Посмотреть базу данных <?=$config['mysql_database']?></a>
<br><br><a href="?action=db"><strong>Очистить базу данных <?=$config['mysql_database']?></strong></a>
<?php } else {?>
невозможно подключится к базе, проверьте наличие файла настроект
<?php }?>
