<?php

/*
 * текущий словарь и таблицы пересохраняет в json
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'_config2.php');	//установка настроек
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции словаря

$type = 'object';
$data = array();

if (@$_GET['file']=='slider') {
	$type = 'array';
	$path = ROOT_DIR . 'layout/data/slider.txt';
	$data = mysql_select("
		SELECT
 			id,name,CONCAT ('/layout/images/',img) img
 		FROM slider
 		WHERE display=1
 		LIMIT 5
 		",'rows');
}
//товары
elseif (@$_GET['file']=='products') {
	$type = 'array';
	$path = ROOT_DIR . 'layout/data/products.txt';
	$data = mysql_select("
		SELECT
 			id,name,price,CONCAT ('/layout/images/',img) img,text
 		FROM shop_products
 		WHERE display=1
 		LIMIT 5
 		",'rows');
}
//словарь
else {
	$dir = ROOT_DIR . 'files/languages/1/dictionary';
	$path = ROOT_DIR . 'layout/data/dictionary.txt';
	if ($handle = opendir($dir)) {
		$files = array();
		while (false !== ($file = readdir($handle))) {
			if ($file == '.' || $file == '..') continue;
			if (is_file($dir . '/' . $file)) {
				include($dir . '/' . $file);
			}
		}
	}
}

//генерация файла

$str = $type == 'array' ? '[':'{';
$i = 0;
foreach ($data as $key => $val) {
	if ($i != 0) $str .= ',';
	if ($type == 'array') $str .= PHP_EOL.'	{' . PHP_EOL;
	else $str .= PHP_EOL.'	"' . $key . '" : {' . PHP_EOL;
	$ii = 0;
	foreach ($val as $k => $v) {
		if ($ii != 0) $str .= ',' . PHP_EOL;
		//else $str .= PHP_EOL;
		$value = str_replace('"', '\"', $v);
		$value = str_replace(PHP_EOL, '', $value);
		$value = str_replace('	', ' ', $value);
		$str .= PHP_EOL.'		"' . $k . '" : "' . $value . '"';
		$ii++;
	}
	$str .= PHP_EOL.'	}';
	$i++;
}
$str .= $type == 'array' ? PHP_EOL.']': PHP_EOL.'}';

$fp = fopen($path, 'w');
fwrite($fp, $str);
fclose($fp);

echo '<pre>';
echo $str;

