<?php

//сессия стартуется в _config2.php
//session_start();

define('ROOT_DIR', dirname(__FILE__).'/../');

require_once(ROOT_DIR.'_config.php');	//динамические настройки
require_once(ROOT_DIR.'_config2.php');	//установка настроек

// загрузка функций **********************************************************
//require_once(ROOT_DIR.'functions/admin_func.php');	//функции админки
require_once(ROOT_DIR.'functions/auth_func.php');	//функции авторизации
require_once(ROOT_DIR.'functions/common_func.php');	//общие функции
//require_once(ROOT_DIR.'functions/file_func.php');	//функции для работы с файлами
require_once(ROOT_DIR.'functions/html_func.php');	//функции для работы нтмл кодом
require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами
//require_once(ROOT_DIR.'functions/image_func.php');	//функции для работы с картинками
require_once(ROOT_DIR.'functions/lang_func.php');	//функции словаря
//require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/mysql_func.php');	//функции для работы с БД
require_once(ROOT_DIR.'functions/string_func.php');	//функции для работы со строками

$request_url = explode('?',$_SERVER['REQUEST_URI'],2); //dd($request_url);
//создание массива $u
$u = explode('/',$request_url[0]);

$lang = @$_REQUEST['language'] ? lang($_REQUEST['language']) : false;

$api = array();

$debug = @$_REQUEST['_debug'];

if (@$u[2]) {
	//второй уровень вложенности
	if (@$u[3]) {
		//если в папке есть индексный файл то грузим его
		//нижнее подчеркивание только для того чтобы он был первым в списке
		$file = $u[2].'/_index.php';
		//если нет то указанный
		if (!file_exists($file)) {
			$file = $u[2].'/'.$u[3].'.php';
		}
	}
	//первый уровень вложенности
	else {
		//либо отдельный файл либо в папке common
		$file = $u[2].'.php';
		//если нет то в папке common
		if (!file_exists($file)) {
			$file = 'common/'.$u[2].'.php';
		}
	}

	if ($debug) $api['_file'] = $file;
	$file = ROOT_DIR.'api/'.$file;
	if (file_exists($file)) {
		include_once($file);
	}
	else {
		$api['_error'] = 'error #1';
	}
}
// если /api/ без указания ресурса — отдаем список доступных эндпоинтов
else {
    $base = ROOT_DIR.'api/';
    $list = array();

    // хелпер для извлечения первого комментария из файла
    $extract_description = function($file_path) {
        $src = @file_get_contents($file_path);
        if ($src===false) return '';
        // убрать открывающий тег и ведущие пробелы
        $src = preg_replace('/^\s*<\?php\s*/', '', $src);
        // пробуем строковый комментарий // ...
        if (preg_match('/^\s*\/\/\s*(.+)$/m', $src, $m)) {
            return trim($m[1]);
        }
        // пробуем блочный комментарий /* ... */ и берём первую непустую строку
        if (preg_match('/\/\*([\s\S]*?)\*\//', $src, $m)) {
            $block = trim($m[1]);
            $lines = preg_split('/\r?\n/', $block);
            foreach ($lines as $line) {
                $line = trim(ltrim($line, "*\t "));
                if ($line!=='') return $line;
            }
        }
        return '';
    };

    // разбор методов и параметров
    $extract_meta = function($file_path) {
        $src = @file_get_contents($file_path);
        $method = 'GET/POST';
        $params = array();
        if ($src!==false) {
            // метод
            $usesPost = (bool)preg_match('/\$_POST\[/i', $src) || (bool)preg_match('/form_smart\s*\(\s*\$fields\s*,\s*stripslashes_smart\s*\(\$_POST/i', $src);
            $usesGet  = (bool)preg_match('/\$_GET\[/i', $src);
            $usesReq  = (bool)preg_match('/\$_REQUEST\[/i', $src) || (bool)preg_match('/form_smart\s*\(\s*\$fields\s*,\s*stripslashes_smart\s*\(\$_REQUEST/i', $src);
            if ($usesPost && !$usesGet && !$usesReq) $method = 'POST';
            elseif ($usesGet && !$usesPost && !$usesReq) $method = 'GET';
            elseif ($usesPost && $usesGet) $method = 'GET/POST';
            elseif ($usesReq) $method = 'GET/POST';

            // параметры из $fields
            if (preg_match('/\$fields\s*=\s*array\((.*?)\)\s*;\s*/is', $src, $m)) {
                if (preg_match_all('/\'([^\']+)\'\s*=>/i', $m[1], $m2)) {
                    foreach ($m2[1] as $p) $params[] = $p;
                }
            }
            // параметры, встречающиеся напрямую
            if (preg_match_all('/\$_(?:GET|POST|REQUEST)\[[\'\"]([a-zA-Z0-9_]+)[\'\"]\]/', $src, $m3)) {
                foreach ($m3[1] as $p) $params[] = $p;
            }
        }
        $params = array_values(array_unique($params));
        return array($method, $params);
    };

    // файлы верхнего уровня
    foreach (scandir($base) as $f) {
        if ($f=='.' || $f=='..') continue;
        $path = $base.$f;
        if (is_file($path) && substr($f,-4)=='.php' && $f!='index.php') {
            $name = basename($f,'.php');
            list($method,$params) = $extract_meta($path);
            $list[] = array(
                'url' => '/api/'.$name,
                'file'=> 'api/'.$f,
                'description' => $extract_description($path),
                'method' => $method,
                'params' => $params
            );
        }
    }
    // подкаталоги первого уровня
    foreach (scandir($base) as $d) {
        if ($d=='.' || $d=='..') continue;
        $dir = $base.$d;
        if (is_dir($dir)) {
            // _index.php как /api/{dir}
            if (is_file($dir.'/_index.php')) {
                list($method,$params) = $extract_meta($dir.'/_index.php');
                $list[] = array(
                    'url' => '/api/'.$d,
                    'file'=> 'api/'.$d.'/_index.php',
                    'description' => $extract_description($dir.'/_index.php'),
                    'method' => $method,
                    'params' => $params
                );
            }
            // отдельные файлы
            foreach (scandir($dir) as $f2) {
                if ($f2=='.' || $f2=='..' || $f2=='_index.php') continue;
                $path2 = $dir.'/'.$f2;
                if (is_file($path2) && substr($f2,-4)=='.php') {
                    $name2 = basename($f2,'.php');
                    list($method,$params) = $extract_meta($path2);
                    $list[] = array(
                        'url' => '/api/'.$d.'/'.$name2,
                        'file'=> 'api/'.$d.'/'.$f2,
                        'description' => $extract_description($path2),
                        'method' => $method,
                        'params' => $params
                    );
                }
            }
        }
    }
    // рендерим красивую HTML-страницу со списком API
    header('Content-type: text/html; charset='.$config['charset']);
    $api_list = $list; // передаём в шаблон
    $template = ROOT_DIR.'api/_templates/index.php';
    if (file_exists($template)) {
        include $template;
    } else {
        echo '<!doctype html><html><head><meta charset="UTF-8"><title>API index</title></head><body>'; 
        echo '<h1>API endpoints</h1><ul>';
        foreach ($api_list as $e) {
            echo '<li><a href="'.htmlspecialchars($e['url']).'">'.htmlspecialchars($e['url']).'</a> — '.htmlspecialchars($e['description']).'</li>';
        }
        echo '</ul></body></html>';
    }
    exit;
}

if ($debug) {
	dd($api);
}
else {
	header('Content-type: application/json; charset=UTF-8');
	echo json_encode($api);
}
