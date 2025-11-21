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

$debug = (@$_REQUEST['_debug'] ?? null) ? 1 : 0;
// поддержка параметра debug=1 для удобного чтения ответа
if (!$debug && isset($_REQUEST['debug']) && $_REQUEST['debug']) {
	$debug = 1;
}

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

    // хелпер: извлекает первый комментарий из файла
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

    // хелпер: скрыть файл, если первый комментарий — //not_visible
    $is_not_visible = function($file_path) {
        $src = @file_get_contents($file_path);
        if ($src===false) return false;
        $src = preg_replace('/^\s*<\?php\s*/', '', $src);
        return (bool)preg_match('/^\s*\/\/\s*not_visible\b/m', $src);
    };

    // разбор методов и параметров + извлечение примеров и примера ответа из комментариев
    $extract_meta = function($file_path) use ($config) {
        $src = @file_get_contents($file_path);
        $method = 'GET/POST';
        $params = array();
        $examples = array();
        $exampleResponse = '';
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
            // примеры из комментариев: строки вида "// example: ..."
            if (preg_match_all('/^\s*\/\/\s*example\s*:\s*(.+)$/mi', $src, $mEx1)) {
                foreach ($mEx1[1] as $ex) { $examples[] = trim($ex); }
            }
            // пример ответа одной строкой
            if (preg_match('/^\s*\/\/\s*example_response\s*:\s*(.+)$/mi', $src, $mExR1)) {
                $exampleResponse = trim($mExR1[1]);
            }
            // а также внутри блочных комментариев
            if (preg_match_all('/\/\*([\s\S]*?)\*\//', $src, $blocks)) {
                foreach ($blocks[1] as $block) {
                    if (preg_match_all('/^\s*\*?\s*example\s*:\s*(.+)$/mi', $block, $mEx2)) {
                        foreach ($mEx2[1] as $ex) { $examples[] = trim($ex); }
                    }
                    // пример ответа (многострочный)
                    if ($exampleResponse==='') {
                        if (preg_match('/example_response\s*:(.+)$/is', $block, $mR)) {
                            $resp = trim($mR[1]);
                            // очистим лидирующие символы комментариев
                            $lines = preg_split('/\r?\n/', $resp);
                            $norm = array();
                            foreach ($lines as $ln) {
                                $ln = preg_replace('/^\s*\*\s?/', '', $ln);
                                $norm[] = rtrim($ln);
                            }
                            $exampleResponse = trim(implode("\n", $norm));
                        }
                    }
                }
            }
        }
        $params = array_values(array_unique($params));
        $examples = array_values(array_unique($examples));
        return array($method, $params, $examples, $exampleResponse);
    };

    // хелпер: примеры URL для показа в списке
    $build_examples = function($urlPath, $method, $params, $commentExamples = array()) use ($config) {
        $fullBase = rtrim($config['http_domain'], '/');
        $out = array();
        // Только примеры из комментариев; без автогенерации
        foreach ((array)$commentExamples as $ex) {
            if ($ex==='') continue;
            if (preg_match('#^https?://#i', $ex)) {
                $out[] = $ex;
            } elseif ($ex[0]=='/') {
                $out[] = $fullBase.$ex;
            } else {
                if ($ex[0]=='?') $out[] = $fullBase.$urlPath.$ex; else $out[] = $fullBase.$urlPath.'?'.$ex;
            }
        }
        return $out;
    };

    // файлы верхнего уровня
    foreach (scandir($base) as $f) {
        if ($f=='.' || $f=='..') continue;
        $path = $base.$f;
        if (is_file($path) && substr($f,-4)=='.php' && $f!='index.php') {
            if ($is_not_visible($path)) continue; // скрыть из списка
            $name = basename($f,'.php');
            list($method,$params,$commentExamples,$exampleResponse) = $extract_meta($path);
            $url = '/api/'.$name;
            $list[] = array(
                'url' => $url,
                'file'=> 'api/'.$f,
                'description' => $extract_description($path),
                'method' => $method,
                'params' => $params,
                'examples' => $build_examples($url, $method, $params, $commentExamples),
                'example_response' => $exampleResponse
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
                if (!$is_not_visible($dir.'/_index.php')) {
                    list($method,$params,$commentExamples,$exampleResponse) = $extract_meta($dir.'/_index.php');
                    $url = '/api/'.$d;
                    $list[] = array(
                        'url' => $url,
                        'file'=> 'api/'.$d.'/_index.php',
                        'description' => $extract_description($dir.'/_index.php'),
                        'method' => $method,
                        'params' => $params,
                        'examples' => $build_examples($url, $method, $params, $commentExamples),
                        'example_response' => $exampleResponse
                    );
                }
            }
            // отдельные файлы
            foreach (scandir($dir) as $f2) {
                if ($f2=='.' || $f2=='..' || $f2=='_index.php') continue;
                $path2 = $dir.'/'.$f2;
                if (is_file($path2) && substr($f2,-4)=='.php') {
                    if ($is_not_visible($path2)) continue; // скрыть из списка
                    $name2 = basename($f2,'.php');
                    list($method,$params,$commentExamples,$exampleResponse) = $extract_meta($path2);
                    $url2 = '/api/'.$d.'/'.$name2;
                    $list[] = array(
                        'url' => $url2,
                        'file'=> 'api/'.$d.'/'.$f2,
                        'description' => $extract_description($path2),
                        'method' => $method,
                        'params' => $params,
                        'examples' => $build_examples($url2, $method, $params, $commentExamples),
                        'example_response' => $exampleResponse
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
