<?php

/*
 * скрипт для создания полного архива сайта
*/

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'plugins/pclzip-2-8-2/pclzip.lib.php');

//название файла архива
$archive = new PclZip('../www.zip');
//папка которую нужно архивировать
$v_list = $archive->create('../',
	PCLZIP_OPT_REMOVE_PATH, 'data',
	PCLZIP_OPT_ADD_PATH, 'install');
if ($v_list == 0) {
	die("Error : ".$archive->errorInfo(true));
}
?>
<a href="/www.zip">www.zip</a>
