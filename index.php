<?php

/**
 * основной файл обработки всех урл для сайта
 * v1.4.42 - правки в многоязычности
 */

//session_start();

// загрузка настроек *********************************************************
define('ROOT_DIR', dirname(__FILE__).'/');
require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
//require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками
require_once (ROOT_DIR.'functions/array_func.php'); //функции работы с массивами

$request_url = explode('?',$_SERVER['REQUEST_URI'],2); //dd($request_url);
//создание массива $u
$u = explode('/',$request_url[0]);
$lang = false;
//v1.4.42 - правки в многоязычности
if ($config['multilingual'] AND $u[1]) {
	//все языки
	$langs = mysql_select("SELECT url id,id name FROM languages WHERE display=1 ORDER by `rank` DESC", 'array');
	$lang_id = isset($langs[$u[1]]) ? $langs[$u[1]] : 1;
	//если не первый язык или u0 включена
	if ($lang_id != 1 OR $config['multilingual_u0']) {
		//смещаем на 1 влево все $u для того чтобы в $u[0] был урл языка
		foreach ($u as $k => $v) {
			$k1 = $k + 1;
			if (isset($u[$k1])) $u[$k] = $u[$k1];
			else unset($u[$k]);
		}
		//получаем язык
		$lang = lang($u[0], 'url');
	}
}
//если язык не определен то язык по умолчанию
if (!$lang) $lang = lang();

//создаем до 10 уровня вложеность урл
for ($i=0; $i<10; $i++) if (empty($u[$i])) $u[$i] = '';


require_once(ROOT_DIR.'functions/data_func.php');	//общие массивы
$abc['lang'] = $lang;

//v1.3.3 переменные для шаблонов
$abc['template'] = '_template'; //основной шаблон с header
$abc['layout'] = 'page'; //внутренний шаблон модуля
$abc['module'] = 'page'; //модуль

//список модулей на сайте
$modules = mysql_select("SELECT url name,module id FROM pages WHERE module!='pages' AND language=".$lang['id']." AND display=1",'array',60*60);

//аутентификация - создание массива с данными пользователя
$abc['user'] = $user = user('auth'); //print_r($user);
//принудительная авторизация под админом - для демки
//$_SESSION['user'] = $user = mysql_select("SELECT ut.*,u.*FROM users u LEFT JOIN user_types ut ON u.type = ut.id WHERE u.id=1 LIMIT 1",'row');

//v1.2.45 - редирект на основной домен
if (@$config['domain_main_redirect'] AND @$config['domain_main']) {
	if ($config['local']==false AND $config['domain_main']!=$_SERVER['HTTP_HOST']) {
		header('HTTP/1.1 301 Moved Permanently');
		header('location: ' . $config['http_domain'] . $_SERVER['REQUEST_URI']);
		die();
	}
}

//v1.2.34 - переадресация https
if (@$config['https']==1) {
	if(
		(isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS']!='on')
		OR (isset($_SERVER['HTTP_X_FORWARDED_PROTOCOL']) AND $_SERVER['HTTP_X_FORWARDED_PROTOCOL']!='https')
		OR (isset($_SERVER['REQUEST_SCHEME']) AND $_SERVER['REQUEST_SCHEME']=='http')
	) {
		header('HTTP/1.1 301 Moved Permanently');
		header('location: ' . $config['http_domain'] . $_SERVER['REQUEST_URI']);
		die();
	}
}

//включена заглушка для всех кроме администраторов или если ошибка с БД
if (($config['dummy']==1 AND access('user admin')==false) OR @$config['mysql_error']) {
	echo html_render('layouts/_dummy');
	die();
}

//редиректы с админки
if ($config['redirects']) {
	//$request_url = explode('?',$_SERVER['REQUEST_URI']); //print_r($request_url);
	if ($redirect = mysql_select("SELECT * FROM redirects WHERE old_url='".mysql_res($request_url[0])."'",'row')) {
		header('HTTP/1.1 301 Moved Permanently');
		header('location: '.$config['http_domain'].$redirect['new_url']);
		die();
	}
}

$error = 0;
//мультиязычность
if ($config['multilingual']) {
	//если урл языка не совпадает
	if ($u[0]!='' AND $u[0]!=$lang['url']) $error++;
}

//нет задвоения // в урл
if (strripos($request_url[0], '//')===false) {
	//условие для главной страницы или модуля
	$where = ($u[1] == '') ? "module='index'" : "url='" . mysql_res($u[1]) . "'";
	//sql-запрос в таблицу pages
	$query = "
		SELECT *, id AS pid
		FROM pages
		WHERE display=1 AND language=" . $lang['id'] . " AND " . $where . "
		LIMIT 1
	"; //echo $query;
	//массив $abc['page] содержит начальную информацию для страницы, которая может быть изменена/дополнена в модуле
	if ($error == 0 AND $abc['page'] = mysql_select($query, 'row', 60 * 60)) {
		$abc['module'] = $abc['page']['module'];
		$abc['layout'] = $abc['page']['module'];
		//хлебные крошки, начинаем с главной
		$abc['breadcrumb'] = array();
		$abc['breadcrumb'][] = array(
			'name'=>i18n('common|site_name'),
			'url'=>get_url('index')
		);
		if ($abc['page']['level'] > 1) {
			$query = "
				SELECT name,url,module
				FROM pages
				WHERE left_key <= " . $abc['page']['left_key'] . "
					AND right_key >= " . $abc['page']['right_key'] . "
				ORDER BY left_key ASC
			";
			$breadcrumb = breadcrumb($query, get_url('page',array('url'=>'{url}','module'=>'{module}')), 60 * 60);
			$abc['breadcrumb'] = array_merge($abc['breadcrumb'],$breadcrumb);
		}
		else {
			$abc['breadcrumb'][] = array(
				'name'=>$abc['page']['name'],
				'url'=>get_url('page', $abc['page'])
			);
		}
		//загрузка модуля
		$file_module = ROOT_DIR . 'modules/' . $abc['module'] . '.php';
		if (is_file($file_module)) {
			require_once($file_module);
			//v1.3.3 если нет layout то ставим default, нужно для $abc['content'] (ранее $html['content'])
			if (!file_exists(ROOT_DIR.$config['style'].'/includes/layouts/'.$abc['layout'].'.php')) {
				$abc['layout'] = 'default';
			}
		}
		else {
			trigger_error('file not exists ' . $file_module, E_USER_DEPRECATED);
			$error++;
		}
	}
	else $error++;
}
else $error++;
//404
if ($error>0) {
	header("HTTP/1.0 404 Not Found");
	$abc['page']['title'] = $abc['page']['name'] = $abc['page']['description'] = i18n('common|str_no_page_name');
	$abc['layout'] = 'error';
}
//РЕДИРЕКТЫ если не 404`
else{
	//1) 301 редирект при неккоректном урл
	if($_SERVER['REQUEST_URI']) {
		//$request_url = explode('?',$_SERVER['REQUEST_URI']);
		if (substr($request_url[0], -1)!='/') {
			$url = isset($request_url[1]) ? '?'.$request_url[1] : '';
			header('HTTP/1.1 301 Moved Permanently');
			die(header('location: '.$request_url[0].'/'.$url));
		}
	}
	//2) редирект если запрашивают через index.php
	if (strpos($_SERVER['REQUEST_URI'],'/index.php')!==false) { // проверяем есть ли вхождение строки
		header('HTTP/1.1 301 Moved Permanently');
		// редиректим на адрес без '/index.php', будет выбивать на 404, либо главную
		die(header('location: '.$config['http_domain'].str_replace('/index.php','',$_SERVER['REQUEST_URI'])));
	}
	//3) v1.2.30 редирект для uppercase
	if (@$config['redirect_uppercase']) {
		//$request_url = explode('?',$_SERVER['REQUEST_URI']); //print_r($request_url);
		$lowercase = mb_strtolower($request_url[0],'UTF-8');
		if ($lowercase!=$request_url[0]) {
			header('HTTP/1.1 301 Moved Permanently');
			header('location: '.$config['http_domain'].$lowercase);
			die();
		}
	}
}

//callback-функция для буфера вывода gzip.
//ob_start("ob_gzhandler"); - закоментировал так как не понятно было работает или нет
if (@$config['html_minify']) {
	ob_start("html_minify"); //в common добавил функцию для сжатия нтмл
}

//загрузка шаблона
echo html_render('layouts/_template');

//debug queries
if (access('user admin') AND @$_GET['show_queries']) {
	dd($config['queries']);
}
