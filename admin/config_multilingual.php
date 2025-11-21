<?php

/**
 * настройки многоязычности
 * v1.2.21 - добавил $languages
 * v1.4.42 - исправление ошибок в многоязычности
 */

//добавляем выборку всех языков
$languages = mysql_select("SELECT id,name FROM languages ORDER BY `rank` DESC", 'array');

//добавочные поля в формах в разных модулях
$config['lang_fields'] = array(
	//товары
	'shop_products'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
		array('tinymce td12', 'text', array('name' => a18n('text'))),
		array('input td12','url',array('name' => a18n('url'))),
		array('input td12','title', array('name' => a18n('title'))),
		array('input td12','description', array('name' => a18n('description')))
	),
	//категории
	'shop_categories'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
		array('tinymce td12', 'text', array('name' => a18n('text'))),
		array('input td12','url', array('name' => a18n('url'))),
		array('input td12','title',array('name' => a18n('title'))),
		array('input td12','description', array('name' => a18n('description'))),
	),
	// категории обоев
	'shop_wallpaper_categores'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
	),
	// стили генерации (название)
	'styles'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
	),
	// цвета (название)
	'colors'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
	),
	//параметры пользователя
	'user_fields'=>array(
		array('input td6', 'name', array('name' => a18n('name'))),
		array('input td6', 'hint',array('name' => a18n('hint'))),
	),
	//статусы заказа
	'order_types'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
		array('textarea td12', 'text', array('name' => a18n('text')))
	),
	//доставка
	'order_deliveries'=>array(
		array('input td12', 'name', array('name' => a18n('name'))),
		array('textarea td12', 'text',array('name' => a18n('text'))),
	),
);

//добавочные поля языков в других таблицах
$config['lang_tables'] = array(
	//товары
	'shop_products'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
		'text'=>'TEXT NOT NULL',
		'url'=>'VARCHAR( 255 ) NOT NULL',
		'title'=>'VARCHAR( 255 ) NOT NULL',
		'description'=>'VARCHAR( 255 ) NOT NULL'
	),
	//категории
	'shop_categories'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
		'text'=>'TEXT NOT NULL',
		'url'=>'VARCHAR( 255 ) NOT NULL',
		'title'=>'VARCHAR( 255 ) NOT NULL',
		'description'=>'VARCHAR( 255 ) NOT NULL'
	),
	// категории обоев (название)
	'shop_wallpaper_categores'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL'
	),
	// стили генерации (название и текст подсказки prompt)
	'styles'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
	),
	// цвета (название и текст подсказки prompt)
	'colors'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
	),
	//параметры пользователя
	'user_fields'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
		'hint'=>'VARCHAR( 255 ) NOT NULL'
	),
	//статусы заказа
	'order_types'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
		'text'=>'TEXT NOT NULL',
	),
	//доставка
	'order_deliveries'=>array(
		'name'=>'VARCHAR( 255 ) NOT NULL',
		'text'=>'TEXT NOT NULL',
	),
);