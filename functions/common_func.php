<?php

//общие функции
/*
 * v1.4.4 - html_array для таблицы
 * v1.4.26 - добавил генерацию img_webp
 * v1.4.41 - нет картинки для админки
 * v1.4.43 - get_imgs если вообще нет display
 * v1.4.55 - PHP 7.4 deprecated get_magic_quotes_gpc
 * v1.4.59 - hypertext
 * v1.4.66 - hypertext путь картинки
 * v1.4.82 - serialize->json
 */

//обрезание обратных слешев в $_REQUEST данных
//v1.4.55 - PHP 7.4 deprecated get_magic_quotes_gpc
function stripslashes_smart($post) {
    // PHP 7.4+ — magic_quotes отключены, просто возвращаем данные как есть
    return $post;
}

//создание урл из $_GET
function build_query($key = '') {
	$get = $_GET;
	if ($key) {
		$array = explode(',',$key);
		foreach ($array as $k=>$v) unset($get[$v]);
	}
	return http_build_query($get);
}

//создание файла лога в папке logs
/**
 * @param $file - название файла в папке /logs/
 * @param $string - строка или массив данных который будут записаны в лог
 * @param bool $debug - в значении true логи будут писываться только если $config['debug'] = true
 */
function log_add($file,$string,$debug=false) {
	global $config;
	//логи с пометкой дебаг не создаются при выключеном $config['debug']
	if ($debug==false OR $config['debug'] == true) {
		if (!is_dir(ROOT_DIR . 'logs')) mkdir(ROOT_DIR . 'logs');
		$fp = fopen(ROOT_DIR . 'logs/' . $file, 'a');
		//если в лог передан массив то делаем из него строку
		if (is_array($string)) {
			/*
			$content = '';
			foreach ($string as $k=>$v) {
				if (is_array($v)) $content.= $k.':'.serialize($v)."\t";
				else $content.= $k.':'.$v."\t";
			}
			$string = $content;
			*/
			$string = json_encode($string);
		}
		fwrite($fp, $string . PHP_EOL);
		fclose($fp);
	}
}

//получить ИП
function get_ip(){
	$ip = '';
	if(!empty($_SERVER['HTTP_X_REAL_IP'])) {//check ip from share internet
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	}
	elseif(!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

//определение города по ип
/**
 * @param $ip - ип пользователя
 * @param int $level 1 - страна, 2 - регион, 3 - город
 * @return array geo - массив базы гео, country - страна, region - регион, city - город
 * @version v1.2.128
 * добавлана v.1.2.128
 */
function geo_data ($ip,$level=3) {
	if (!isset($_SESSION)) session_start();
	if (!isset($_SESSION['geo']) OR $_SESSION['geo']==false) {
		require_once(ROOT_DIR . 'functions/mysql_func.php');    //функции для работы с БД
		require_once(ROOT_DIR . 'functions/string_func.php');    //функции для работы со строками
		require_once(ROOT_DIR . 'plugins/SypexGeo_2.2/SxGeo.php');
		//в цмс по умолчанию база городов не хранится, нужно качать отсюда
		//https://sypexgeo.net/ru/download/
		//Sypex Geo City UTF-8
		//https://sypexgeo.net/files/SxGeoCity_utf8.zip
		$SxGeo = new SxGeo(ROOT_DIR . 'plugins/SypexGeo_2.2/SxGeoCity.dat');
		//$SxGeo = new SxGeo(ROOT_DIR.'plugins/SypexGeo_2.2/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY); // Самый производительный режим, если нужно обработать много IP за раз

		//сразу ли включать страны, регионы и города
		$display = 1;

		//возвращаемый массив
		$data = array(
			'geo' => false,
			'country' => false,
		);
		if ($level > 1) $data['region'] = false;
		if ($level > 2) $data['city'] = false;

		if ($supex_geo = $SxGeo->getCityFull($ip)) {
			$data['geo'] = $supex_geo;
			//1. выбираем страну
			$data['country'] = mysql_select("
				SELECT * FROM geo_countries 
				WHERE iso='" . $supex_geo['country']['iso'] . "'
				ORDER BY `rank` DESC,name LIMIT 1
			", 'row');

			//2. добавляем страну
			if ($data['country']==false) {
				$data['country'] = array(
					'uid'       => $supex_geo['country']['id'],
					'iso'       => $supex_geo['country']['iso'],
					'lat'       => $supex_geo['country']['lat'],
					'lng'       => $supex_geo['country']['lon'],
					'name'      => $supex_geo['country']['name_ru'],
					'name2'     => $supex_geo['country']['name_en'],
					'url'       => trunslit($supex_geo['country']['name_ru']),
					'display'   => $display //по умолчанию отключен
				);
				$data['country']['id'] = mysql_fn('insert', 'geo_countries', $data['country']);
			}

			//3. обновляем страну
			$country = array();
			if ($data['country']['lat'] == '') {
				$country['lat'] = $supex_geo['country']['lat'];
				$country['lng'] = $supex_geo['country']['lon'];
			}
			if ($data['country']['uid'] == 0) {
				$country['uid'] = $supex_geo['country']['id'];
			}
			if ($country) {
				$country['id'] = $data['country']['id'];
				mysql_fn('update', 'geo_countries', $country);
				$data['country'] = array_merge($data['country'], $country);
			}

			//4. если страна не отключена то определяем район
			if ($supex_geo['region'] AND $data['country']['display'] == 1 AND $level > 1) {
				//1. выбираем регион
				$data['region'] = mysql_select("
					SELECT * FROM geo_regions 
					WHERE iso='" . $supex_geo['region']['iso'] . "'
						OR uid = '" . $supex_geo['region']['id'] . "'
						OR LOWER(name) = '" . mysql_res(mb_strtolower($supex_geo['region']['name_ru'], 'UTF-8')) . "'
					ORDER BY `rank` DESC,name LIMIT 1
				", 'row');

				//2. добавляем регион
				if ($data['region']==false) {
					$data['region'] = array(
						'uid'       => $supex_geo['region']['id'],
						'country'   =>$data['country']['id'],
						'iso'       => $supex_geo['region']['iso'],
						'name'      => $supex_geo['region']['name_ru'],
						'name2'     => $supex_geo['region']['name_en'],
						'url'       => trunslit($supex_geo['region']['name_ru']),
						'display'   => $display //по умолчанию отключен
					);
					$data['region']['id'] = mysql_fn('insert', 'geo_regions', $data['region']);
				}

				//3. обновляем регион
				$region = array();
				if ($data['region']['iso'] == '') {
					$region['iso'] = $supex_geo['region']['iso'];
				}
				if ($data['region']['uid'] == 0) {
					$region['uid'] = $supex_geo['region']['id'];
				}
				if ($region) {
					$region['id'] = $data['region']['id'];
					mysql_fn('update', 'geo_regions', $region);
					$data['region'] = array_merge($data['region'], $region);
				}

				//4. если регион не отключен то определяем город
				if ($supex_geo['city'] AND $data['region']['display'] == 1 AND $level > 2) {
					//1. выбираем город
					$data['city'] = mysql_select("
						SELECT * FROM geo_cities 
						WHERE uid='" . $supex_geo['city']['id'] . "'
							OR LOWER(name) = '" . mysql_res(mb_strtolower($supex_geo['city']['name_ru'], 'UTF-8')) . "'
						ORDER BY `rank` DESC,name LIMIT 1
					", 'row');

					//2. добавляем город
					if ($data['city']==false) {
						$data['city'] = array(
							'uid'       => $supex_geo['city']['id'],
							'region'    =>$data['region']['id'],
							'country'   =>$data['country']['id'],
							'lat'       => $supex_geo['city']['lat'],
							'lng'       => $supex_geo['city']['lon'],
							'name'      => $supex_geo['city']['name_ru'],
							'name2'     => $supex_geo['city']['name_en'],
							'url'       => trunslit($supex_geo['city']['name_ru']),
							'display'   => $display //по умолчанию отключен
						);
						$data['city']['id'] = mysql_fn('insert', 'geo_cities', $data['city']);
					}

					//3. обновляем города
					$city = array();
					if ($data['city']['lat'] == '') {
						$city['lat'] = $supex_geo['city']['lat'];
						$city['lng'] = $supex_geo['city']['lon'];
					}
					if ($data['city']['uid'] == 0) {
						$city['uid'] = $supex_geo['city']['id'];
					}
					if ($city) {
						$city['id'] = $data['city']['id'];
						mysql_fn('update', 'geo_cities', $city);
						$data['city'] = array_merge($data['city'], $city);
					}

					//4. если город отключен
					if ($data['city']['display'] == 0) $data['city'] = false;
				}

				//5. если регион отключен
				if ($data['region']['display'] == 0) $data['region'] = false;
			}

			//5. если страна отключена
			if ($data['country']['display'] == 0) $data['country'] = false;
		}
		//если не определило, берем по дефолту
		if ($data['country'] == false) {
			$data['country'] = mysql_select("
				SELECT * FROM geo_countries 
				WHERE display=1
				ORDER BY `rank` DESC,name LIMIT 1
			", 'row');
		}
		if ($level > 1 AND $data['region'] == false AND $data['country']) {
			$data['region'] = mysql_select("
				SELECT * FROM geo_regions 
				WHERE country=" . $data['country']['id'] . " AND display=1
				ORDER BY `rank` DESC,name LIMIT 1
			", 'row');
		}
		if ($level > 2 AND $data['city'] == false AND $data['region']) {
			$data['city'] = mysql_select("
				SELECT * FROM geo_cities 
				WHERE region=" . $data['region']['id'] . " AND display=1
				ORDER BY `rank` DESC,name LIMIT 1
			", 'row');
		}
		$_SESSION['geo'] = $data;
	}
	else $data = $_SESSION['geo'];
	return $data;
}

/**
 * Get value from config and return default value if empty.
 * Ex. config('mysql_server', 'localhost');
 * Or config('mysql.server', 'localhost') for get multidimensional array value
 *
 * @global array $config
 * @param string $key
 * @param mixed $default
 * @return mixed
 * добавлана v.1.1.21
 */
function config($key, $default = NULL) {
	global $config;

	if(strpos($key, '.'))
	{
	    $array = $config;
	    foreach (explode('.', $key) as $segment) {
		if (isset($array[$segment])) {
		    $array = $array[$segment];
		} else {
		    return $default;
		}
	    }

	    return $array;
	}
	else
	{
	    return (isset($config[$key])) ? $config[$key] : $default;
	}
}

/*
 * Функция для сжатия нтмл кода
 * @param $body - простой нтмл код
 * @return mixed - сжатый нтмл код
 * @version v1.2.11
 * v.1.1.8 - добавлена
 * v.1.2.11 - полностью обновлена
*/
function html_minify ($body) {
	//remove redundant (white-space) characters
	$replace = array(
		//remove tabs before and after HTML tags
		'/\>[^\S ]+/s'   => '>',
		'/[^\S ]+\</s'   => '<',
		//shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
		'/([\t ])+/s'  => ' ',
		//remove leading and trailing spaces
		'/^([\t ])+/m' => '',
		'/([\t ])+$/m' => '',
		// remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
		'~//[a-zA-Z0-9 ]+$~m' => '',
		//remove empty lines (sequence of line-end and white-space characters)
		'/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
		//remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
		'/\>[\r\n\t]+\</s'    => '><',
		//все пробелы между тегами нельзя удалять
		'/\>[ ]+\</s'    => '> <',
		//remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
		'/}[\r\n\t ]+/s'  => '}',
		'/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
		//remove new-line after JS's function or condition start; join with next line
		'/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
		'/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
		//remove new-line after JS's line end (only most obvious and safe cases)
		'/\),[\r\n\t ]+/s'  => '),',
		//remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
		'~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
	);
	$body = preg_replace(array_keys($replace), array_values($replace), $body);

	//remove optional ending tags (see http://www.w3.org/TR/html5/syntax.html#syntax-tag-omission )
	$remove = array(
		'</option>', '</li>', '</dt>', '</dd>', '</tr>', '</th>', '</td>'
	);
	$body = str_ireplace($remove, '', $body);
	return $body;
}

/**
 * функция для тестирования скриптов, выводить в удобочитаемом виде информацию
 * @param $data - массив значений для вывода на экран
 * @param bool $die - опция умирать или нет
 * @version v1.1.30
 * v.1.1.30 - добавлена
 */
function dd($data,$die=false) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	if ($die) die();
}

/*
 * Функция для формировки урл картинки
 * @param string $table - название таблицы
 * @param int $id - ид записи
 * @param string $key - ключ картинки
 * @param string $img - название картинки
 * @param string $p - тип превью
 * @return string
 * @version v1.4.82
 * v.1.1.23 - добавлена
 * v1.2.101 - много картинок
 * v1.2.115 - доработка по много картинок
 * v1.4.26 - добавил генерацию img_webp
 * v1.4.41 - поправил косяк с показом картинки, нет картинки для админки
 * v1.4.66 - hypertext путь картинки
 * v1.4.82 - serialize->json
 */
function get_img($table,$q,$key='img',$p='') {
	global $config;
	//дебаг для _imgs
	if (@$_GET['_imgs']) {
		return '/debug/'.$q[$key];
	}
	$img = ''; //путь к картинке
	//тут можно написать полный путь с доменом
	$site = '';
    //одна картинка
    if (@$q[$key]) {
        // если в значении уже есть вложенные папки (например, apple_id/123.png)
        if (strpos($q[$key], '/') !== false) {
            $img = '/files/' . $table . '/' . $p . $q[$key];
        }
        else {
            $img = '/files/'.$table.'/'.$q['id'].'/'.$key.'/'.$p.$q[$key];
        }
    }
	//v1.4.59 - hypertext
	elseif (strpos($key, '_')) {
		// /news/18/hypertext/2_3/img.jpg
		// 2 номер по порядку блока, 3 номер по порядку изображения внутри блока
		$exp = explode('/',$key);
		if (isset($exp[1])) {
			$exp2 = explode('_',$exp[1]);
			if (isset($q[$exp[0]])) {
				$hypertext = $q[$exp[0]];
				//v1.4.66 - hypertext путь картинки
				if (isset($hypertext[$exp2[0]])) {
					$imgs = $hypertext[$exp2[0]]['images'];
					$img = '/files/' . $table . '/' . $q['id'] . '/' . $key . '/' . $p . $imgs[$exp2[1]]['file'];
				}
			}
		}
	}
	//v1.2.101 - много картинок
	elseif (strpos($key, '/')) {
		$exp = explode('/',$key);
		if (isset($exp[1]) AND isset($q[$exp[0]])) {
			if (is_array($q[$exp[0]])) $imgs = $q[$exp[0]];
			//v1.4.82 - serialize->json
			else $imgs = json_decode($q[$exp[0]],true);
			if (isset($imgs[$exp[1]])) {
				$img = '/files/' . $table . '/' . $q['id'] . '/' . $key . '/' . $p . $imgs[$exp[1]]['file'];
			}
		}
		//v1.4.41 - поправил косяк с показом картинки
		//return '';
	}
	//если есть картинка
	if ($img) {
		if ($config['webp']) {
			require_once(ROOT_DIR . 'functions/webp_func.php');
			$img = img_webp($img);
		}
		return $site.$img;
	}
	//заглушка
	else {
		//v1.2.115 по умолчанию для админки
		if ($p=='a-') return $site.'/admin/templates/imgs/no_img.png';
		//v1.4.41 - нет картинки для админки
		elseif ($config['style']=='admin/templates2') {
			return $site.'/'.$config['style'].'/imgs/no_img.png';
		}
		//v1.2.115 по умолчанию для сайта
		else return $site.'/templates/images/no_img.svg';
	}
}

//получаем массив картинок с полными путями
/**
 * @param string $table - таблица с материалом
 * @param array $q - массив данных
 * @param string $key - поле с картинками
 * @param string $p - если нужно превью
 * @return array - массив картинок с полным путем _ и превью
 * @version v1.4.82
 * v.1.2.101 - добавлена
 * v1.4.43 - если вообще нет display
 * v1.4.82 - serialize->json
 */
function get_imgs ($table, $q, $key = 'imgs',$p=''){
	$images = array();
	//v1.4.82 - serialize->json
	$data = $q[$key] ? json_decode($q[$key],true) : array();
	$path = '/files/'.$table.'/'.$q['id'].'/'.$key.'/';
	if(is_array($data)) {
		foreach ($data as $k=>$v) {
			//v1.4.43 - если вообще нет display
			if(!isset($v['display']) OR $v['display']==1) {
				$images[$k] = $v;
				//полный путь
				$images[$k]['_'] = $path . $k . '/' . $v['file'];
				//путь к превью
				if ($p) {
					$images[$k]['_' . $p] = $path . $k . '/' . $p . $v['file'];
				}
			}
		}
	}

	return $images;
}

/**
 * вставка инлайнового svg
 * полезно для вставки интерфейсных картинок
 * @param $name - путь к файлу svg
 * @param $path - путь папке с картинкой (по дефолту это templates/images/)
 * @return svg контент
 * @version v1.1.26
 * v.1.1.26 - добавлена
 */
function get_svg ($img,$path='templates/images/'){
	return file_get_contents(ROOT_DIR.$path.$img);
}

/**
 * функция создает массивы данных чтобы не повторять запросы к базе
 * @param $table - название табклицы
 * @param $id - ИД записи
 * @param string $label - поля, которые нужны в нтмл
 * @return string|array
 * @version v1.2.23
 * v.1.2.23 - добавлена
 */
function get_data($table,$id=true,$label='') {
	global $config;
	//возвращает всю таблицу в виде массива
	if ($id===true) {
		if (!isset($config['_'.$table])) {
			$config['_' . $table] = mysql_select("SELECT * FROM `" . $table . "` WHERE 1", 'rows_id');
		}
		return $config['_' . $table];
	}
	//возвращает конкретную запись по ИД
	if (!isset($config['_'.$table][$id])) {
		if ($config['_'.$table][$id] = mysql_select("SELECT * FROM `".$table."` WHERE id=".intval($id),'row')) {
			//значение по умолчанию
			$config['_'.$table][0] = array(
				'name'=>'',
			);
		}
		else return false;
	}
	if ($label) {
		$array = explode(' ',$label);
		$content = '';
		foreach ($array as $k=>$v) {
			$content.= $config['_'.$table][$id][$v].' ';
		}
		return trim($content);
	}
	else return $config['_'.$table][$id];
}
