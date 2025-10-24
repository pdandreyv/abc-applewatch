<?php

/*
 * копирование основного языка в другие языки
 * v1.2.91
 * v1.2.92
 */

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once (ROOT_DIR.'_config.php');
include_once (ROOT_DIR.'_config2.php');
include_once (ROOT_DIR.'functions/image_func.php');
include_once (ROOT_DIR.'functions/common_func.php');
include_once (ROOT_DIR.'functions/mysql_func.php');
include_once (ROOT_DIR.'functions/file_func.php');

mysql_connect_db();

$copy = 1; //ИД основного языка, который будет скопирован
$paste = '2,3'; //ИД языков через запятую, куда будет скопировано все с основного языка

//массив независимых таблиц с полем language
$tables = array(
	'pages:left_key'=>false,
	'landing:rank desc'=>false,
	'landing_items:rank desc'=>false,
	/*
	'shop_categories:left_key'=>array(
		'shop_products:id'=>'category'
	),
	*/
);

//массив зеркальных таблиц тут
include_once (ROOT_DIR.'functions/lang_func.php');
include_once (ROOT_DIR.'admin/config_multilingual.php');

//все дополнительные языки
$languages = mysql_select("
	SELECT * 
	FROM languages 
	WHERE id IN (".$paste.") 
	ORDER BY `rank` DESC
",'rows_id');

foreach ($languages as $l) {
	echo $l['name'];
	echo '<br>';

	//1) копирование независимых таблиц
	foreach ($tables as $table=>$tables) {
		if (is_array($tables)) {
			//копируем основную таблицу
			$ids = copy_table($table,$copy,$l['id']);
			foreach ($tables as $k=>$v) {
				//копируем вложенные таблицы
				copy_table($k,$copy,$l['id'],$ids,$v);
			}
		}
		//копируем таблицу
		else copy_table($table,$copy,$l['id']);
	}

	//2) копирование зеркальных таблиц
	foreach ($config['lang_fields'] as $table=>$array) {
		$fields = array();
		foreach ($array as $k=>$v) {
			if ($copy!=1) $fields[] = $v[1].$l['id'].'='.$v[1].$copy;
			else $fields[] = $v[1].$l['id'].'='.$v[1];
		}
		$query = "UPDATE ".$table." SET ";
		$query.= implode(',',$fields);
		echo $query.'<br>';
		mysql_fn('query',$query);
	}

	//3) копирование шаблонов писем
	$letter_templates = mysql_select("SELECT id FROM letter_templates ORDER BY id",'rows');
	foreach ($letter_templates as $k=>$v) {
		$dir = ROOT_DIR . 'files/letter_templates/' . $v['id'] . '/';
		if (is_dir($dir . $copy . '/')) {
			echo $dir;
			echo ' файлы скопированы<br>';
			rcopy($dir . $copy . '/', $dir  . $l['id'] . '/');
		}
	}
}

/**
 * @param $table - таблица в которой будет производится копирование
 * @param $copy - ид копируемого языка
 * @param $paste - ид нового языка в которого будут вставлять язык $copy
 * @param bool $parents - массив с старыми и новыми ид
 * @param bool $parent - для пример при независимых товарах и независимых категориях к новым товарам нужно в поле category проставить ид новых категорий
 * @return array
 */
function copy_table ($table,$copy,$paste,$parents=false,$parent=false) {
	//КАРТА САЙТА
	$array = explode(':',$table);
	$table = $array[0];
	$order = $array[1];
	echo $table;
	echo '<br>';

	//очищаем таблицу
	$delete = mysql_select("SELECT id FROM ".$table." WHERE language=".$paste,'rows');
	//удаляем файлы
	foreach ($delete as $k=>$v) {
		delete_all(ROOT_DIR.'files/'.$table.'/'.$v['id'].'/',true);
	}
	//удаляем записи из БД
	mysql_fn('delete',$table,false," AND language=".$paste);

	//выбираем все записи с копируемой таблицы
	$pages = mysql_select("SELECT * FROM ".$table." WHERE language=".$copy." ORDER BY ".$order,'rows_id');
	//массив новых ид со значениями старых ид
	$ids = array(0=>0);
	foreach ($pages as $k=>$v) {
		$p = $v;
		$p['language'] = $paste; //заменяем ИД языка
		//$ids - массив из новых и старых ИД
		if (isset($v['parent']) AND isset($ids[$v['parent']])) {
			$p['parent'] = $ids[$v['parent']];
		}
		//если зеркальность независимая и есть связанные таблицы типа как категории и товары, то в товарах нужно заменить ид категории
		if ($parents) {
			$pp = $p[$parent];
			$p[$parent] = $parents[$pp];
		}
		//удаляем ИД
		unset($p['id']);
		$ids[$v['id']] = mysql_fn('insert',$table,$p);
		//копирование файлов
		if (is_dir(ROOT_DIR.'files/'.$table.'/'.$v['id'])) {
			rcopy(ROOT_DIR.'files/'.$table.'/'.$v['id'], ROOT_DIR.'files/'.$table.'/'.$ids[$v['id']]);
			echo ' файлы скопированы';
		}
		echo 'id: '.$v['id'].'-'.$ids[$v['id']].';';
		if (isset($v['parent'])) {
			echo ' parent: '.$v['parent'].'-'.$ids[$v['parent']].'';
		}
		echo '<br>';
	}
	return $ids;
	echo '<br>';
}