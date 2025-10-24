<?php

/*
 * скрипт для розархивации
 * путь к архиву передаем черех GET
 * пример /_/zip.php?file=test.zip
*/

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'plugins/pclzip-2-8-2/pclzip.lib.php');

$file = @$_GET['file'];
echo $file;
echo '</br>';
if ($file AND file_exists(ROOT_DIR.$file)) {
	$archive = new PclZip(ROOT_DIR.$file);
	//тут указывается относительный путь
	$list = $archive->extract('../');
	if ($list) {
		foreach ($list as $k => $v) {
			echo $v['filename'] . ' ' . $v['status'] . '<br>';
		}
	}
	else echo 'пустой архив';
}
else echo '</br>нет файла';

?>
