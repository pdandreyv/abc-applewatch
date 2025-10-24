<?php

define('ROOT_DIR', __DIR__ . '/');
require_once(ROOT_DIR.'_config.php');
require_once(ROOT_DIR.'_config2.php');
require_once(ROOT_DIR.'functions/common_func.php');
require_once(ROOT_DIR.'functions/mysql_func.php');
require_once(ROOT_DIR.'functions/file_func.php');

// удаляем все user_generation, старше 1 часа и display=0
$q = mysql_select(
	"SELECT id, user_id, img FROM user_generation WHERE display=0 AND created_at < (NOW() - INTERVAL 1 HOUR)",
	'rows'
);

if ($q) {
	foreach ($q as $row) {
		// удалить файл, если есть
		if (!empty($row['img'])) {
			$path = ROOT_DIR.'files/user_generation/'.str_replace('..','',$row['img']);
			if (is_file($path)) @unlink($path);
			// удалить пустую папку уровня apple_id, если пустая
			$dir = dirname($path);
			@rmdir($dir);
		}
		// удалить запись
		mysql_fn('delete','user_generation', array('id'=>$row['id']));
	}
}

echo 'OK';


