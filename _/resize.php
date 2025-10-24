<?php

/*
 * скрипт для изменений размеров картинок
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');

mysql_connect_db();

$table = 'gallery';
$param = array('p-'=>'cut 250x175');
$img = 'img';
$images = 'images';

$query = "SELECT * FROM $table ORDER BY id";
if ($rows = mysql_select($query,'rows')) foreach ($rows as $q) {
	echo $q['id'].'<br />';
	if ($q['img']) {
		$path = $table.'/'.$q['id'].'/img';
		$root = ROOT_DIR.'files/'.$path.'/';
		if (is_file($root.$q['img'])) {

			foreach ($param as $k=>$v) {
				$prm = explode(' ',$v);
				img_process($prm[0],$root.$q['img'],$prm[1],$root.$k.$q['img']);
				//если есть водяной знак
				//if (isset($prm[2])) img_watermark($root.$k.$q['img'],ROOT_DIR.'templates/images/'.$prm[2],$root.$k.$q['img'],@$prm[3]);
			}
		}
	}
	if ($q[$images]) {
		$imgs = unserialize($q[$images]);
		$path = $table.'/'.$q['id'].'/'.$images;
		echo '<br />'.$path;
		$root = ROOT_DIR.'files/'.$path.'/';
		//$param = array('p-'=>'cut 360x270');
		if (is_dir($root) AND $handle = opendir($root)) {
			while (false !== ($file = readdir($handle))) {
				if (isset($imgs[$file])) {
					$v1 = $root.$file.'/'.$imgs[$file]['file'];
					echo '<br />'.$v1;
					foreach ($param as $k=>$v) {
						$prm = explode(' ',$v);
						echo '<br />'.$v1;
						img_process($prm[0],$root.$file.'/'.$imgs[$file]['file'],$prm[1],$root.$file.'/'.$k.$imgs[$file]['file']);
						//если есть водяной знак
						//if (isset($prm[2])) img_watermark($root.$file.'/'.$k.$imgs[$file]['file'],ROOT_DIR.'templates/images/'.$prm[2],$root.$file.'/'.$k.$imgs[$file]['file'],@$prm[3]);
					}
				}
			}
			closedir($handle);
		}
	}
}

?>
