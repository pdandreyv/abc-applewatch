<?php

define('ROOT_DIR', dirname(__FILE__).'/../');
include_once(ROOT_DIR.'_config2.php');

$pages = array(
	'Начало' => array(
		'info_about'		=> 'Введение',
		'info_structure'	=> 'Архитектура',
		'info_install'		=> 'Установка',
	),
	'Основы' => array(
		'common_db'			=> 'База данных',
		'common_sql'		=> 'SQL запросы',
		'common_auth'		=> 'Авторизация'
	),
	'Админка (back-end)' => array(
		'admin_about'		=> 'Введение',
		'admin_config'		=> 'Настройка',
		'admin_variables'	=> 'Переменные',
		'admin_actions'		=> 'Действия',
		'admin_module'		=> 'Пример',
		'admin_events'		=> 'События',
		'admin_depend'		=> 'Многие ко многим',
		'admin_multilang'	=> 'Мультиязычность',
		'admin_landing'		=> 'Лендинг'
	),
	'Сайт (front-end)'=>array(
		'site_about'		=> 'Введение',
		'site_config'		=> 'Настройка',
		'site_variables'	=> 'Переменные',
		'site_modules'		=> 'Модули',
		'site_includes'		=> 'Шаблоны',
		'site_lang'			=> 'Словарь',
		'site_sources'		=> 'Скрипты',
		'site_form'			=> 'Формы',
		'site_debug'		=> 'Отладка',
	)
);

$page = 'info_about';
$name = 'Введение';
foreach ($pages as $k=>$v) {
	if (array_key_exists(@$_GET['page'],$v)) {
		$page = $_GET['page'];
		$name = $v[$page];
	}
}

include('template.php');

?>