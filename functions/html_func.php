<?php

//функции для работы с HTML кодом

/**
 * v1.2.63
 * v1.4.1 - пагинатор в админке
 * v1.4.62 - мелкая правка
 */

/**
 * нтмл код селекта
 * @param $key - ключ либо массив с ключами для селекта
 * @param $query - 1)массив option; 2)модуль или запрос
 * @param $default	- значени по умолчанию
 * @param $template - шаблон
 * @return html код селекта
 */
function select($key,$query,$default = NULL,$template = '{name}') {
	if (isset($default)) $content = $default ? '<option value="">'.$default.'</option>' : '<option value="">'.i18n('common|make_selection').'</option>';
	else $content = '';
	//1)список из массива - $query - массив со значениями для селектов
	if (is_array($query)) foreach ($query as $k=>$v) {
		if (is_array($v) && !is_int($k)) {
			$content.= '<optgroup label="'.$k.'">';
			$content.= select($key,$v,$default,$template);
			$content.= '</optgroup>';
		}
		else {
			if (is_array($key)) $selected = in_array($k, $key) ? 'selected="selected"' : '';	//для multiple
			else $selected = ($k==$key AND (string)$key!='') ? 'selected="selected"' : '';		//для select
			$nbsp = '';
			if (is_array($v)) {
				if (isset($v['level'])) {
					for ($i = 1; $i<$v['level']; $i++) $nbsp.= '&nbsp; ';
					$nbsp.= ':.. ';
				}
			}
			$content.= '<option value="'.htmlspecialchars($k).'" '.$selected.'>'.$nbsp.(is_array($v) ? $v['name'] : $v).'</option>';
		}
	}
	//2)список из sql-запроса - $query - sql-запрос или таблица
	else {
		//если нет пробела сформировать запрос
		if (!strpos($query, ' ')) $query = "SELECT id,name,level FROM `".$query."` ORDER BY left_key";
		if ($options = mysql_select($query,'rows')) {
			foreach ($options as $q) {
				$nbsp = '';
				if (isset($q['level'])) {
					for ($i = 1; $i < $q['level']; $i++) $nbsp .= '&nbsp; ';
					$nbsp .= ':.. ';
				}
				if (is_array($key)) $selected = in_array($q['id'], $key) ? 'selected="selected"' : '';    //для multiple
				else $selected = $q['id'] == $key ? 'selected="selected"' : '';                            //для select
				if (isset($q['parent'])) $selected .= ' data-parent="' . $q['parent'] . '"';
				$str = $template;
				foreach ($q as $k => $v) $str = str_replace("{" . $k . "}", $q[$k], $str);
				$content .= '<option value="' . $q['id'] . '" ' . $selected . '>' . $nbsp . $str . '</option>';
			}
		}
	}
	return $content;
}


/**
 * шаблонизатор - заменяет {i} на $data['i'] а так же {brand|name} на $data['brand']['name']
 * @param string $template - строка с {i}
 * @param array $data - массив со значениями для замены
 * @return - строка $template с заменой
 * todo
 * закинуть html_template сюдаже
 */
function template($template,$data) {
	preg_match_all('/{(.*?)}/',$template,$matches,PREG_PATTERN_ORDER);
	foreach($matches[1] as $k=>$v) {
		//получаем массив ключей
		$keys = explode('|',$v);
		//создаем вспомогательную переменную, потому как массив $data трогать нельзя. В принципе, можно использовать $matches[1][$k], но лучше перестраховаться
		$replacement = $data;
		//последовательно движемся по всей цепочке ключей.
		//если ключ один (например, {name}), то и foreach выполнит только один цикл, что дает фактически ту же функцию без многомерности
		//если ключей много, то каждый раз проверяем, существует ли такой ключ - если нет, сохраняем пустую строку и выходим из цикла, если есть, цикл продолжаем, сохраняя значение из массива, соотв. ключу
		foreach ($keys as $i=>$key) {
			if (isset($replacement[$key])) { //можно, в принципе, написать array_key_exists($key,$replacement), это уже нюансы, думаю, что isset правильнее
				$replacement = $replacement[$key];
			} else {
				$replacement = '';
				break;
			}
		}
		$matches[1][$k] = is_array($replacement) ? '' : $replacement;
	}
	return str_replace($matches[0],$matches[1],$template);
}

/**
 * замена {img} на картинки
 * @param array $q - массив из БД
 * @param string $table - название таблицы
 * @param string $key - название поля с картинками
 * @param string $text - название поля с текстом
 * @return array - обработанных массив
 * @version v1.2.131
 * v1.2.101 - добавлена
 * v1.2.131 - amp страницы
 */
function template_img ($table,$q,$key_imgs='imgs',$key_text='text') {
	global $config;
	//формируем массив из нтмл кода картинки
	$data = [];
	$imgs = get_imgs($table,$q,$key_imgs);
	foreach ($imgs as $k=>$v) {
		$item = [
			'name' => $v['name'],
			'title' => htmlspecialchars(@$v['title']?:$v['name']),
			'alt' => htmlspecialchars(@$v['alt']?:$v['name']),
			'_'=>$v['_']
		];
		//v1.2.131 - amp страницы
		if ($config['amp']) {
			$size = getimagesize(ROOT_DIR.$item['_']);
			$data[] = '<amp-img style="width:100%" src="'.$item['_'].'" width="'.$size[0].'" height="'.$size[1].'" layout="responsive"></amp-img>';
		}
		else $data[] = '<img style="width:100%" src="'.$item['_'].'" alt="'.$item['alt'].'" title="'.$item['title'].'">';
	}
	if ($data) {
		//заменяем {img} на картинки
		preg_match_all('/{img}/', $q[$key_text], $matches);
		$next = 0;
		if (!empty($matches[0]) && is_array($matches[0]) && ($cnt = count($matches[0])) && count($data)) {
			for ($i = 0; $i < $cnt; $i++) {
				if ($next > (count($data) - 1)) {
					$next = 0;
				}
				$q[$key_text] = preg_replace('/{img}/', $data[$next++], $q[$key_text], 1);
			}
		}
	}
	else $q[$key_text] = preg_replace('/{img}/', '', $q[$key_text]);
	return $q[$key_text];
}

/**
 * замена {video} на видео
 * @param string $text - нтмл код с кодом {video}
 * @param $videos - массив видео или строка с несколькими видео
 * @return string html код с фреймом видое
 * @version v1.2.131
 * v1.2.101 - добавлена
 * v1.2.131 - amp страницы
 */
function template_video ($text,$videos) {
	global $config;
	//формируем массив из нтмл кода фреймов
	$data = [];
	$videos = is_array($videos) ? $videos : explode("\r\n",$videos);
	foreach ($videos as $k=>$v) if ($v) {
		//v1.2.131 - amp страницы
		if ($config['amp']) {
			$data[] = '<amp-iframe width=300 height=300
			   layout="responsive"
			   sandbox="allow-scripts allow-same-origin"
			   frameborder="0"
			   src="'.video_iframe($v).'">
			</amp-iframe>';
		}
		else $data[] = '<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="'.video_iframe($v).'" allowfullscreen></iframe></div>';
	}
	if ($data) {
		//заменяем {video} на фреймы
		preg_match_all('/{video}/', $text, $matches);
		$next = 0;
		if (!empty($matches[0]) && is_array($matches[0]) && ($cnt = count($matches[0])) && count($data)) {
			for ($i = 0; $i < $cnt; $i++) {
				if ($next > (count($videos) - 1)) {
					$next = 0;
				}
				$text = preg_replace('/{video}/', $data[$next++], $text, 1);
			}
		}
	}
	else $text = preg_replace('/{video}/', '', $text);
	return $text;
}


/**
 * замена {page|text} на шаблон
 * @param string $text - html код с тектсом
 * @return string - html код с тектсом c подключенным шаблоном
 */
function html_template($text) {
	preg_match_all('/{(.*?)}/',$text,$matches,PREG_PATTERN_ORDER);
	foreach($matches[1] as $k=>$v) {
		$matches[1][$k] = is_file(ROOT_DIR.'templates/includes/'.$v.'.php') ? html_array($v) : '';
	}
	return str_replace($matches[0],$matches[1],$text);
}

/**
 * Функция из массива синонимов [зеленый|синий] случайно выбирает одно
 * @param $text - входящий текст
 * @return string - результирующий текст
 * @version v1.2.24
 * v1.2.24 - добавлена
 * v1.2.47 - исправлена ошибка с {
 */
function synonymizer ($text) {
	$reg = "/\\[[^\[]*\\]/";
	preg_match_all ($reg,$text, $matches);
	$result_rand=array();
	foreach($matches[0] as $k=>$v){
		$v = trim(str_replace(array('[',']'), "", $v));
		$v = explode('|',$v);
		$v = $v[array_rand($v)];
		$result_rand[$k]=trim($v);
	};
	$reg_arr = array();
	foreach($result_rand as $k => $v){
		$reg_arr[$k]=$reg;
	};
	$text = preg_replace($reg_arr,$result_rand,$text,1);
	return $text;
}

/**
 * //подключение шаблона - наполнение шаблона значенями массива
 * @param string $path - путь к шаблону
 * @param string|array $q - массив данных или строка
 * @return string
 * @version v1.3.2
 * v1.2.63 - $html глобальная
 * v1.2.131 - amp страницы
 * v1.2.133 - callback_func
 * v1.3.2 - $abc
 */
function html_array($path,$q = array()) {
	global $config,$modules,$u,$user,$lang,$page,$html,$abc;
	//v1.2.133 - callback_func
	require_once(ROOT_DIR.'functions/callback_func.php');
	//v1.2.131 - amp страницы
	if (@$config['amp']) $path = 'amp/'.$path;
	$i = $num_rows = 0;
	$function = '_'.str_replace('/','_',$path);
	//v1.2.133 - callback_func
	if (function_exists($function)) {
		$q = $function($q);
	}
	ob_start(); // echo to buffer, not screen
	include (ROOT_DIR.$config['style'].'/includes/'.$path.'.php');
	return ob_get_clean(); // get buffer contents
}

/**
 * наполнение шаблона выборкой с БД
 * @param string $path - путь к файлу шаблона, через пробел путь к файлу пагинатора
 * @param $query - sql запрос
 * @param bool|string $no_results - строка в случае если нет результатов запроса, если false то фраза по умолчанию  i18n('common|msg_no_results')
 * @param bool $cache - время обновления кеша в секундах, если пусто то кеш не создается
 * @param string $cache_type - html - генерируется нтмл файл, json - json массив в файле
 * @return bool|string
 * @version v1.3.2
 * v1.2.2 - писать лог ошибок когда нет пагинатора или шаблона
 * v1.2.63 - $html глобальная
 * v1.2.131 - amp страницы
 * v1.2.133 - callback_func
 * v1.3.2 - $abc
 */
function html_query($path, $query, $no_results = false, $cache = 0, $cache_type = 'html') {
	global $config,$lang,$modules,$user,$u,$page,$html,$abc;
	//v1.2.133 - callback_func
	require_once(ROOT_DIR.'functions/callback_func.php');
	//v1.2.131 - amp страницы
	if ($config['amp']) $path = 'amp/'.$path;
	$content	= false;
	$data		= array();
	$m			= explode(' ',$path);
	//v1.2.133 - callback_func
	$function = '_'.str_replace('/','_',$m[0]);
	$time		= time() - $cache;
	//путь к шаблону
	$file_template = ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php';
	//если есть пагинатор подключить его, в нем к $query прибавляется LIMIT n,c
	if (isset($m[1])) {
		//путь к пагинатору
		$file_pagination = ROOT_DIR.$config['style'].'/includes/pagination/'.$m[1].'.php';
		if (file_exists($file_pagination)) include ($file_pagination);
		else trigger_error('file not exists '.$file_pagination, E_USER_DEPRECATED);
	}
	//если есть кеширование и входной параметр $query - строка
	if (@$config['cache'] && $cache && is_string($query)) {
		if ($cache_type=='json') $file	= md5($query).'.php';
		else $file	= md5($query).'.html';
		$config['queries'][$file] = $query;
		$file = ROOT_DIR.'cache/'.$file;
		if (file_exists($file) && $time<filemtime($file)) {
			if ($cache_type=='json') {
				$content = '';
				$data = json_decode(file_get_contents($file),true);
				if (is_array($data)) {
					$num_rows = count($data);
					$i = 1;
					foreach ($data as $q) {
						ob_start(); // echo to buffer, not screen
						include (ROOT_DIR.$config['style'].'/includes/'.$m[0].'.php');
						$content.= ob_get_clean(); // get buffer contents
						$i++;
					}
				}
			}
			else $content.= file_get_contents($file);
		}
	}
	//если нет результата кеширования то подключаем шаблон и делаем запрос в БД
	if ($content===false) {
		if (file_exists($file_template)) {
			//если в качестве второго параметра задан массив
			if (is_array($query)) {
				if ($num_rows = count($query)) {
					$i = 1;
					foreach ($query as $k => $q) {
						$data[] = $q;
						//v1.2.133 - callback_func
						if (function_exists($function)) {
							$q = $function($q);
						}
						ob_start(); // echo to buffer, not screen
						include($file_template);
						$content .= ob_get_clean(); // get buffer contents
						$i++;
					}
				}
			}
			//если в качестве второго параметра задан запрос к БД
			else {
				if (mysql_connect_db()) {
					if ($data = mysql_select($query, 'rows')) {
						$num_rows = count($data);
						$i = 1;
						foreach ($data as $q) {
							//v1.2.133 - callback_func
							if (function_exists($function)) {
								$q = $function($q);
							}
							ob_start(); // echo to buffer, not screen
							include($file_template);
							$content .= ob_get_clean(); // get buffer contents
							$i++;
						}
					}
					//создаем файл кеша
					if (@$config['cache'] && $cache && (is_dir(ROOT_DIR . 'cache') || mkdir(ROOT_DIR . 'cache', 0755, true))) {
						$f = fopen($file, 'w');
						if ($cache_type == 'json') fwrite($f, json_encode($data));
						else fwrite($f, $content);
						fclose($f);
					}
				}
			}
		}
		else trigger_error('file not exists '.$file_template, E_USER_DEPRECATED);
	}
	//если нет результатов
	if ($content=='') {
		if ($no_results===false) {
			$no_results = i18n('common|msg_no_results');
		}
		$content = $no_results ? '<div class="no_results">'.$no_results.'</div>' : '';
	}
	//подлючаем пагинатор
	if (isset($pagination)) $content = str_replace('{content}',$content,$pagination);
	return $content;
}

/**
 * рендерит нтмл из массива, замена функций html_array и html_query
 * @param $path - путь к шаблону
 * @param $data - массив данных
 * @version v1.3.19
 * v1.3.2 - $abc
 * v1.3.19 - правки в логику обработки
 */
function html_render($path,$data=true) {
	global $config,$abc,$lang,$user;
	//v1.2.133 - callback_func
	require_once(ROOT_DIR.'functions/callback_func.php');
	//v1.2.131 - amp страницы
	if ($config['amp']) {
		if (file_exists(ROOT_DIR.$config['style'].'/includes/amp/'.$path.'.php')) {
			$path = 'amp/' . $path;
		}
	}
	//v1.2.133 - callback_func
	$function = '_'.str_replace('/','_',$path);
	$content	= '';
	//путь к шаблону
	$file_template = ROOT_DIR.$config['style'].'/includes/'.$path.'.php';
	//если вызов без параметра или там простой массив
	if ($data===true OR is_string(key($data))) {
		$q = array();
		$num_rows = 1;
		$i = 1;
		//если первый параметр стринговый то это просто массив
		if ($data!=false) {
			$q = $data;
			if (function_exists($function)) {
				$q = $function($q);
			}
		}
		ob_start(); // echo to buffer, not screen
		include ($file_template);
		$content.= ob_get_clean(); // get buffer contents

	}
	//выводим только если есть массив данных
	elseif(is_array($data)) {
		$num_rows = count($data);
		$i = 1;
		foreach ($data as $q) {
			if (function_exists($function)) {
				$q = $function($q);
			}
			ob_start(); // echo to buffer, not screen
			include ($file_template);
			$content.= ob_get_clean(); // get buffer contents
			$i++;
		}
	}

	return $content;
}

/**
 * наполнение шаблона выборкой с json
 * @param $include - путь к шаблону
 * @param $data - путь к файлу с данными
 * @param int $limit - количество записей
 * @return string - html код
 * добавлана v.1.1.38
 */
function html_json ($include,$data,$limit=0) {
	global $config;
	$path = ROOT_DIR.$config['style'].'/data/'.$data.'.txt';
	$content = '';
	if (file_exists($path)) {
		$data = file_get_contents($path);
		$data = json_decode($data,true);
		//if ($include=='shop/product_random') {
			//dd($data);
		//}
		if (is_array($data)) {
			//если есть лимит то максимальное количество записей такое же
			$num_rows = $limit ? $limit : count($data);
			$i = 1;
			foreach ($data as $q) {
				ob_start(); // echo to buffer, not screen
				include (ROOT_DIR.$config['style'].'/includes/'.$include.'.php');
				$content.= ob_get_clean(); // get buffer contents
				if ($i==$limit) return $content;
				$i++;
			}
		}
	}
	else {
		return 'error';
	}
	return $content;

}

/**
 * функция для замены выборок $config['optimize']
 * @param $text - текст для замены
 * @return mixed - замененный текст
 * @version v1.2.48
 * v1.2.48 - добавлена
 */
function html_optimize ($text) {
	global $config;
	if (isset($config['optimize'])) {
		$data = array();
		foreach ($config['optimize'] as $k=>$v) {
			$query = "SELECT * FROM `".$k."` WHERE id IN (" . $v . ")";
			$data[$k] = mysql_select($query, 'rows_id');
		}
		return template($text,$data);
	}
	return $text;
}

/**
 * собирает ИД одной таблицы и формирует массив $config['optimize'][$table]
 * пример, в товаре цвета храним ид через запятую
 * в шаблоне делаем так
 * html_optimize_data ('shop_colors','1,2,3');
 * echo '{shop_colors|1|name},{shop_colors|2|name},{shop_colors|3|name}';
 * и в основном шаблоне делаем html_optimize ($html['content'])
 * в результате к таблице цветов будет сделан только один запрос не зависимо от количества записей на странице
 * @param $table - таблица
 * @param $ids - ид записей через запятую
 * @version v1.2.48
 * v1.2.48 - добавлена
 */
function html_optimize_data ($table,$ids) {
	global $config;
	if ($ids) {
		if (isset($config['optimize'][$table]) AND $config['optimize'][$table]) {
			$data1 = explode(',', $config['optimize'][$table]);
			$data2 = explode(',', $ids);
			$data = array_merge($data1,$data2);
			$config['optimize'][$table] = implode(',',$data);
		}
		else {
			$config['optimize'][$table] = $ids;
		}
	}
}

/**
 * функция формирует бредкрамб
 * @param string $query - SQL запрос
 * @param string $template - шаблон урл, например, /shop/{id}-{url}/
 * @param int $cache - время кеширования в секундах
 * @return array
 * @version 1.3.2
 * v1.3.2 - добавил ключи name и url и сортировку в обратном порядке
 */
function breadcrumb($query,$template = '/{url}/',$cache = false) {
	$data = mysql_select($query,'rows',$cache);
	if (is_array($data)) {
		foreach ($data as $key=>$value) {
			$str = $template;
			foreach ($value as $k=>$v) $str = str_replace ("{".$k."}", $value[$k], $str);
			$breadcrumb[] = array(
				'name'=>$value['name'],
				'url'=>$str);
		}
		return $breadcrumb;
	}
}


/**
 * возвращает атрибуты для редактируемого блока
 * @param string $edit -
 * @param string $editable - тип редактора [str,text]
 * @return string - data атрибуты для быстрого редактирования
 */
function editable($edit,$editable='str') {
	global $lang;
	$array = explode('|',$edit);
	//если есть право на редактирование и не передан в get параметр i18n
	if (access('editable '.$array[0]) && !isset($_GET['i18n'])) {
		return ' data-editable_type="'.$editable.'" data-editable_module="'.$lang['id'].'|'.$edit.'"';
	}
}

/**
 * подключение скриптов
 * @param string $label - метка - можем сразу выводить скрипт (return), а можем собирать в метку для последующего вывода (head|footer)
 * @param string $source - названия скриптов через пробел, описаны в $config['sources'] в _config2.php
 * @return string
 * @version 1.2.70
 * v1.2.70 - подключение внешних скриптов
 * v1.2.126 - обработка лишнего пробела
 * v1.4.62 - мелкая правка
 * $config['sources'] - глобальная перепенная в которой хранятся все скрипты, определяется в _config2.php
 * $config['html_sources'] - глобальная перепенная для записи лейблов
 * пример 1 - в шаблонах подключаем скрипы а выводим их только в подвале
 * //в разных шаблонах можно много раз подключать
 * html_sources('footer','jquery_form');//запись скрипта jquery_form в лейбел footer
 * html_sources('footer','jquery_cookie');//запись скрипта cookie в лейбел footer
 * //один раз указываем в подвале
 * echo html_sources('footer'); //вывод все скриптов jquery_form и jquery_cookie записаных в лейбел footer
 * пример 2 - сразу выводим скрип
 * echo html_sources('return','jquery_form');//вывод на экран библиотеки jquery_form
 */
function html_sources($label='',$source='') {
	global $config, $lang;
	$content = array();
	if ($source) {
		$sources = explode(' ', $source);
		//v1.2.126 - добавили проверку на пустоту если два пробела
		foreach ($sources as $k=>$v) if ($v!='') {
			//если есть такой ресурс
			if (isset($config['sources'][$v])) {
				$config['html_sources'][$label][$v] = $config['sources'][$v];
				if ($label == 'return') $content[] = $config['sources'][$v];
			}
			else {
				trigger_error('не подключен скрипт в $config[\'sources\'] '.$v,E_USER_DEPRECATED);
			}
		}
	}
	//если $sourсe не указано то выводим метку
	else {
		$content = isset($config['html_sources'][$label]) ? $config['html_sources'][$label] : array();
	}
	//если возвращаем результат то компилируем код
	if (count($content)>0) {
		$text = '';
		foreach ($content as $key=>$val) {
			if (is_array($val)) {
				foreach ($val as $k=>$v) {
					//заменяем {localization} на метку языка
					$str = template($v, $lang);
					if (file_exists(ROOT_DIR . trim($str, '?'))) {
						//если есть знак вопроса то добавляем временную метку
						$str .= substr($v, -1) == '?' ? filemtime(ROOT_DIR.trim($v,'?')) : '';
						if ($config['smartoptimizer'] == true) $str = '/smartoptimizer/?'.$str;
						//js или css
						$text .= strpos($v, '.js') ? '<script type="text/javascript" src="' . $str . '"></script>' : '<link href="' . $str . '" rel="stylesheet" type="text/css" />';
						$text .= PHP_EOL;
					}
					else {
						trigger_error('нет файла '.$v,E_USER_DEPRECATED);
					}
				}
			}
			elseif($val) {
				//заменяем {localization} на метку языка
				$str = template($val, $lang);
				//v1.2.70 - если подлючен внешний файл
				if ($str[0]=='<') {
					$text .=  $str;
				}
				//если файл существует
				elseif (file_exists(ROOT_DIR.trim($str,'?'))) {
					//если есть знак вопроса то добавляем временную метку
					$str .= substr($val, -1) == '?' ? filemtime(ROOT_DIR.trim($val,'?')) : '';
					if ($config['smartoptimizer'] == true) $str = '/smartoptimizer/?'.$str;
					//js или css
					$text .= strpos($val, '.js') ? '<script type="text/javascript" src="' . $str . '"></script>' : '<link href="' . $str . '" rel="stylesheet" type="text/css" />';
					$text .= PHP_EOL;
				}
				else {
					trigger_error('нет файла '.$val,E_USER_DEPRECATED);
				}
			}
		}
		return $text;
	}
}

/**
 * генерация ссылки для пагинатора
 * @param string $key - ключ параметра $_GET
 * @param string $value - необходимое значение параметра $_GET[$key]
 * @param int $default - значение по умолчанию $_GET[$key]
 * @return string - url
 * v1.2.38 - добавлена
 * v1.4.1 - пагинатор в админке
 */
function pagination_link ($key,$value,$default=1) {
	global $u;
	$get = $_GET;
	$current = isset($_GET[$key]) ? $_GET[$key] : false;
	//todo можно возвращать пустой урл если совпадает с текущим значением
	//if ($current==$value) return false;
	unset($get['u'],$get[$key]);
	$link =  '/';
	//если сайт
	if ($u) {
		foreach ($u as $k=>$v) if ($v) $link.= $v.'/';
	}
	//v1.4.1 если админка
	else {
		$link = '/admin.php';
	}
	if ($value!=$default) {
		$get[$key] = $value;
	}
	$url = http_build_query($get);
	$link.= $url ? '?' . $url : '';
	return $link;
}
