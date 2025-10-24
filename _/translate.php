<?php

ini_set("max_execution_time", "2600");
ini_set("memory_limit", "10048M");

/*
 * скрипт для перевода словаря и таблиц
 * v1.2.92
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

mysql_connect_db();

//направление перевода
$translate = 'ru-de';
$translate = 'ru-en';

$copy = 1; //копируем - только если словарь нужно копировать
$paste = 2; //вставляем

//массив ключей яндекса для сортировки - у каждого ключа есть ограничение, потому их нужно со временем обновлять
//https://translate.yandex.ru/developers/keys
$yandex_translate = array();

//начальный ключ с которого стартуем перевод
$yandex_key = 0;

/* *
// перевод словаря
$path = ROOT_DIR.'files/languages/'.$copy.'/dictionary/';
$files = scandir2($path,true);
foreach ($files as $k=>$val){
	$key = basename($val);
	$key = substr($key, 0, -4);
	echo '<b>'.$key.'.php</b><br />';
	$lang = array();
	include($path.$key.'.php');
	//echo $path.$key.'.php';
	//выбираем случайный ключ
	//$rand = array_rand($yandex_translate);
	//берем первый ключ из списка, если он не активный то коментируем его и переходим к следующему

	$translated = translate_yandex2 ($lang[$key]);
	//die('+');
	if ($translated){
		$str = '<?php' . PHP_EOL;
		$str .= '$lang[\'' . $key . '\'] = array(' . PHP_EOL;
		foreach ($translated as $k1 => $v1) {
			$str .= "	'" . $k1 . "'=>'" . str_replace("'", "\'", $v1) . "'," . PHP_EOL;
		}
		$str .= ');';
		$str .= '?>';
		$new_data = false;
	}
	$fp = fopen(ROOT_DIR . 'files/languages/'.$paste.'/dictionary/' . $key . '.php', 'w');
	fwrite($fp, $str);
	fclose($fp);
	//die('0');
}
/* */

/* *
// перевод независимой таблицы
$table = 'pages';
$data = mysql_select('SELECT * FROM `'.$table.'` WHERE `language`='.$paste,'rows');
foreach ($data as $q){
	$translate = array(
		'name'=>$q['name'],
		'title'=>$q['title'],
		'text'=>$q['text'],
	);
	$translated=translate_yandex2 ($translate);
	if ($translated) {
		//добавляем ИД
		$translated['id'] = $q['id'];
		//генерируем сеополя
		$translated['url'] = trunslit($translated['name']);
		$translated['description'] = description($translated['text']);
		//обновляем в базе
		mysql_fn('update', $table, $translated);
	}
}
/* */

/**
//перевод зеркальной таблицы
$table = 'shop_products';
$data = mysql_select('SELECT * FROM `'.$table.'`','rows');
foreach ($data as $q){
	$translate = array(
		'name'.$paste=>$q['name'.$paste],
		'title'.$paste=>$q['title'.$paste],
		'text'.$paste=>$q['text'.$paste],
	);
	$translated=translate_yandex2 ($translate);
	if ($translated) {
		//добавляем ИД
		$translated['id'] = $q['id'];
		//генерируем сеополя
		$translated['url'.$paste] = trunslit($translated['name'.$paste]);
		$translated['description'.$paste] = description($translated['text'.$paste]);
		//обновляем в базе
		mysql_fn('update', $table, $translated);
	}
}
 /* */


/**
 * @param $data
 * @return array|bool|string
 */
function translate_yandex2 ($data) {
	global $config,$yandex_translate,$yandex_key;
	$translated = false;
	while ($translated==false) {
		//если закончились ключи яндекса
		if (!isset($yandex_translate[$yandex_key])) {
			die('no keys');
		}
		$config['yandex_translate'] = $yandex_translate[$yandex_key];
		echo $config['yandex_translate'];
		echo '<br>';
		$translated = translate_yandex($data);
		if ($translated==false) {
			$yandex_key++;
		}
		else {
			dd($translated);
			return $translated;
		}
	}
	return false;
}


