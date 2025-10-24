<?php

/**
 * функции для работы со строками
 * v1.2.63
 * v1.4.47 - добавлена get_socials
 * v1.4.51 - get_video_id
 * v1.4.66 - hypertext
 */

/**
 * генерация ключевых слов
 * @param string $str - html код с текстом
 * @return string - ключевые слова через запятую
 * @version v1.2.4
 * v1.2.4 - добавил чтобы всегда возвращало string (раньше могло null вернуть)
 */
function keywords($str) {
	$keywords = '';
	if (strlen($str)>0) {
		$str = preg_replace("/&[\w]+;/", ' ',$str);	//замена символов типа &nbsp; на пробел
		$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
		$str = preg_replace('~[^-їієа-яa-z0-9 ]+~u', ' ', $str);
		$token = strtok($str, ' ');
		$array = array();
		while ($token) {
			$token = trim($token);
			if (strlen($token)>=4) {
				if (!isset($array[$token])) $array[$token]=0;
				$array[$token]++;
			}
			$token = strtok(' ');
		}
		if (count($array)>0) {
			arsort ($array);
			foreach ($array as $key=>$value) {
				if (strlen($keywords.', '.$key)>255) break;
				$keywords.= ', '.$key;
			}
			return substr($keywords, 2);
		}
	}
	return '';
}

/**
 * генерирует описание из текста
 * @param string $str - html код с текстом
 * @return string текст около 255 символов длиной
 */
function description($str) {
	$description = '';
	$str = preg_replace("/&[\w]+;/", ' ',$str);	//замена символов типа &nbsp; на пробел
	$str = trim(strip_tags($str));
	$token = strtok($str, ' ');
	while ($token) {
		$token = trim($token);
		if ($token!='') {
			if (strlen($description.' '.$token)>255) break;
			$description.= trim($token).' ';
		}
		$token = strtok(' ');
	}
	return trim($description);
}

/**
 * преобразование кирилицы в транслит
 * @param string $str - строка текста, обычно название
 * @return string - транлит
 */
function trunslit($str){
	$str = mb_strtolower(trim(strip_tags($str)),'UTF-8');
	$str = str_replace(
		array('a','o','u','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я','і','ї','є'),
		array('a','o','u','a','b','v','g','d','e','e','zh','z','i','i','k','l','m','n','o','p','r','s','t','u','f','h','ts','ch','sh','shch','','y','','e','yu','ya','i','yi','e'),
		$str
	);
	$str = preg_replace('~[^-a-z0-9_.]+~u', '-', $str);	//удаление лишних символов
	$str = preg_replace('~[-]+~u','-',$str);			//удаление лишних -
	$str = trim($str,'-');								//обрезка по краям -
	$str = trim($str,'.');
	return $str;
}

//зaмена функции strtolower
if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str,$enc = 'UTF-8') {
		$large = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','A','A','A','A','A','A','?','C','E','E','E','E','I','I','I','I','?','N','O','O','O','O','O','O','U','U','U','U','Y','А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','Є');
		$small = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','a','a','a','a','a','a','?','c','e','e','e','e','i','i','i','i','?','n','o','o','o','o','o','o','u','u','u','u','y','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','є');
		return str_replace($large,$small,$str);
	}
}
//оставлена для совместимости
function strtolower_utf8($str){
	return mb_strtolower($str,'UTF-8');
}

/**
 * корректировка часового пояса
 * @param $date - дата
 * @param int $time_zone - временной пояс
 * @return string - дата с учетом временного пояса
 */
function time_zone($date,$time_zone = false) {
	if ($time_zone==false) {
		if (access('user auth')==false) return $date;
		global $user;
		$time_zone = $user['time_zone'];
	}
	if ($time_zone==4) return $date;
	return strftime('%Y-%m-%d %H:%M:%S',(strtotime($date)+($time_zone-4)*60*60));
}

/**
 * доставляет нули к числу
 * @param int $number - число
 * @param int $n - количество цифр в числе
 * @return string - число с нулями - 00067
 */
function zerofill($number,$n = 7) {
	return str_pad($number,$n,'0',STR_PAD_LEFT);
}

/**
 * конвертация даты
 * @param datetime $date - время
 * @param string $type - формат даты
 * @return string - отформатировання дана
 */
function date2($date,$type='%d.%m.%y') {
	//названия месяцев $m = strftime('%m',$date);
	//i18n('calendar|month_'.$m) полное название месяца
	//i18n('calendar|month2_'.$m) родительный падеж
	//i18n('calendar|m_'.$m) сокращенное название месяца
	//названия дней $d = strftime('%d',strtotime($date));
	//i18n('calendar|day_'.$d) полное название дня
	//i18n('calendar|d_'.$d) сокращенное название дня
	//полная дата, месяц в родительном падеже
	if ($type=='d month y') {
		if (is_string($date)) $date = strtotime($date);
		$d = strftime2('%d',$date);
		$m = strftime2('%m',$date);
		$y = strftime2('%Y',$date);
		return $d.' '.i18n('calendar|month2_'.$m).' '.$y;
	}
	//в зависимости от прошедшего времени показывает три варианта
	//12:52 | 5 июля | 2011 г.
	elseif ($type=='smart') {
		//если в прошлом году 2011 г.
		if ($date<date('Y')) return date2($date,'%Y ').i18n('calendar|y');
		//если в этом году 5 июля
		elseif 	($date<date('Y-m-d')) {
			$d = strftime2('%d',strtotime($date));
			$m = strftime2('%m',strtotime($date));
			return $d.' '.i18n('calendar|month2_'.$m);
		}
		// если сегодня 12:52
		else return date2($date,'%H:%M');
	}
	else return strftime2($type,strtotime($date));
}

function strftime2($format, $timestamp = null) {
	// Якщо $timestamp не заданий, використовуємо поточний час
	if ($timestamp === null) {
		$timestamp = time();
	}

	// Створюємо об'єкт DateTime з вказаним timestamp
	$date = new DateTime('@' . $timestamp);
	$date->setTimezone(new DateTimeZone(date_default_timezone_get())); // Встановлюємо часовий пояс за замовчуванням

	// Мапа для заміни формату strftime на DateTime::format
	$conversionMap = [
		'%a' => 'D',    // Скорочена назва дня тижня
		'%A' => 'l',    // Повна назва дня тижня
		'%d' => 'd',    // День місяця (з провідним нулем)
		'%e' => 'j',    // День місяця (без провідного нуля)
		'%j' => 'z',    // День року
		'%m' => 'm',    // Місяць (з провідним нулем)
		'%b' => 'M',    // Скорочена назва місяця
		'%B' => 'F',    // Повна назва місяця
		'%y' => 'y',    // Дворіччя року
		'%Y' => 'Y',    // Чотирирічний рік
		'%H' => 'H',    // Години (24-годинний формат)
		'%I' => 'h',    // Години (12-годинний формат)
		'%p' => 'A',    // AM або PM
		'%M' => 'i',    // Хвилини
		'%S' => 's',    // Секунди
		'%U' => 'W',    // Номер тижня в році (неділя як перший день тижня)
		'%w' => 'w',    // День тижня (0 для неділі)
		'%x' => 'Y-m-d',// Стандартне представлення дати
		'%X' => 'H:i:s' // Стандартне представлення часу
	];

	// Заміна форматів strftime на DateTime
	$phpFormat = strtr($format, $conversionMap);

	// Повертаємо відформатовану дату
	return $date->format($phpFormat);
}

/**
 * обрезание текста
 * @param $text нтмл - код с текстом
 * @param int $lenght - длина результирующего текста
 * @param string $strip_tags - какие теги оставляем
 * @param string $end - постфикс если обрезали строку
 * @return string - обрезанная строка
 * @version v1.1.32
 * v1.1.32 - замена iconv на mb
 */
function about($text,$lenght = 1000,$strip_tags = '<br><img>',$end = '..') {
	$text  = strip_tags($text,$strip_tags);
	if (strlen($text)>$lenght) {
		$text = mb_substr($text,0,$lenght,"UTF-8");
		$text.= $end;
	}
	return $text;
}

/**
 * обрезание текста с нтмл тегами
 * @param $text
 * @param $length
 * @param string $suffix
 * @param bool $isHTML
 * @return bool|mixed|string
 * @version v1.3.9
 * v1.2.63 - добавлена
 * v1.2.126 - обрезаем шаблоны для картинок и видео
 * v1.3.9 - косяк с тем что могло пустоту возвращать
 */
function truncate($text, $length, $suffix = '&hellip;', $isHTML = true) {
	//v1.2.126 - обрезаем шаблоны для картинок и видео
	$text = str_replace(array('{img}','{video}'),array('',''),$text);
	$i = 0;
	$simpleTags=array('br'=>true,'hr'=>true,'input'=>true,'image'=>true,'link'=>true,'meta'=>true);
	$tags = array();
	if($isHTML){
		preg_match_all('/<[^>]+>([^<]*)/', $text, $m, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		foreach($m as $o){
			if($o[0][1] - $i >= $length)
				break;
			$t = substr(strtok($o[0][0], " \t\n\r\0\x0B>"), 1);
			// test if the tag is unpaired, then we mustn't save them
			if($t[0] != '/' && (!isset($simpleTags[$t])))
				$tags[] = $t;
			elseif(end($tags) == substr($t, 1))
				array_pop($tags);
			$i += $o[1][1] - $o[0][1];
		}
	}

	// output without closing tags
	$output = mb_substr($text, 0, $length = min(mb_strlen($text, "UTF-8"),  $length + $i),"UTF-8");
	// closing tags
	$output2 = (count($tags = array_reverse($tags)) ? '</' . implode('></', $tags) . '>' : '');

	// Find last space or HTML tag (solving problem with last space in HTML tag eg. <span class="new">)
	$end = preg_split('/<.*>| /', $output, -1, PREG_SPLIT_OFFSET_CAPTURE);
	$end = end($end);
	$pos = (int)end($end);
	//v1.3.9 если возвращает 0 то переопределеям
	if ($pos==0) $pos = $length;
	// Append closing tags to output
	$output.=$output2;

	// Get everything until last space
	$one = mb_substr($output, 0, $pos,"UTF-8");
	// Get the rest
	$two = mb_substr($output, $pos, (mb_strlen($output, "UTF-8") - $pos),"UTF-8");
	// Extract all tags from the last bit
	preg_match_all('/<(.*?)>/s', $two, $tags);
	// Add suffix if needed
	if (mb_strlen($text, "UTF-8") > $length) { $one .= $suffix; }
	// Re-attach tags
	$output = $one . implode($tags[0]);

	//added to remove  unnecessary closure
	$output = str_replace('</!-->','',$output);

	return $output;
}

/**
 * функция читабельности текста
 * @param string $text - текст
 * @return string - оформленный текст
 * @version v1.2.123
 * v1.2.123 - добавлена обработка пробелов если рядом много коротких слов и добавлены некоторые слова из 3 букв
 */
function readability($text) {
	//замена - на &mdash
	$text = str_replace(" - ", "&nbsp;&mdash; ", $text);
	//замена пробелов на &nbsp возле кортких слов (1 и 2 буквы)
	//$text = preg_replace('/(^|\s+)([0-9A-Za-zA-Zа-яЇї]{1,2})\s+/ui', '$1$2&nbsp;', $text);
	$text = preg_replace('/(^|\s+)(\w{1,2})\s+/ui', '$1$2&nbsp;', $text);
	$text = preg_replace('/(&nbsp;)(\w{1,2})\s+/ui', '$1$2&nbsp;', $text);
	//$text = str_replace('&nbsp; ','&nbsp;',$text);
	$text = str_replace(
		array('для ','над ','под ', 'пред ', 'при ', 'про '),
		array('для&nbsp;','над&nbsp;','под&nbsp;', 'пред&nbsp;', 'при&nbsp;', 'про&nbsp;'),
		$text);
	/*if (@$_GET['nbsp']) {
		$text = str_replace('&nbsp;','<b style="display:inline-block; background:red">_</b>',$text);
	}*/
	return $text;
}

/**
 * аналог explode только возвращает не массив а нужный елемент
 * @param string $delimiter - разделитель
 * @param string $str - строка для разделения
 * @param int $number -
 * @param int $count
 * @return mixed
 */
function explode2($delimiter,$str,$number = 1,$count = 2) {
	$array = explode($delimiter,$str,$count);
	$n = $number-1;
	if (isset($array[$n])) return $array[$n];
}

/**
 * множественное число слова
 * @param int $number - число
 * @param string $str1 - строка один ...
 * @param string $str2 - строка два ...
 * @param string $str5 - строка пять ...
 * @return string
 */
function plural($number, $str1, $str2, $str5){
	return $number % 10 == 1 && $number % 100 != 11 ? $str1 : ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20) ? $str2 : $str5);
}

function setUrlParams($url, $params = array())  {
	$urlp = parse_url($url);
	if(!count($params) && (!is_array($urlp))) return $url;
	else {
		parse_str(@$urlp['query'],$urlparams);
		foreach ($params as $k=>$v) {
			if ($v!==false) $urlparams[$k] = $v;
			else unset($urlparams[$k]);
		}
		$query = http_build_query($urlparams);
		return "{$urlp['path']}?".$query;
	}
}

/**
 * функция для конвертации урл видео для вставки в iframe
 * @param $str - урл на видео youtube или vimeo в браузере
 * @return bool|string - урл на видео для iframe
 * @version v1.2.114
 * v1.2.18 - добавлена
 * v1.2.114 уточнил условие для youtube
 * v1.4.51 - get_video_id
 */
function video_iframe($str) {
	$video = false;
	if ($str) {
		//1.2.114 уточнил условие для youtube
		if (strpos($str,'youtube') OR strpos($str,'youtu.be')) {
			$video = get_video_id ($str);//substr($str,-11);
			$video = 'https://www.youtube.com/embed/'.$video;
		}
		elseif (strpos($str,'vimeo')) {
			$video = get_video_id ($str);//substr($str,18);
			$video = 'http://player.vimeo.com/video/'.$video.'?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff';
		}
	}
	return $video;
}

/**
 * @param $str - ссылка на видео
 * @return string - ид видео
 * v1.4.51 - добавлена
 */
function get_video_id ($str) {
	$video = '';
	//return $str;
	if ($str) {
		//https://jwPS_6UTbyA/livestreaming
		if (strripos($str, "studio.youtube.com")) {
			$str = trim($str,'https://');
			$str = trim($str,'studio.youtube.com/video/');
			$arr = explode('/',$str);
			$video = $arr[0];
		}
		//https://www.youtube.com/watch?v=jwPS_6UTbyA
		elseif(strripos($str, "youtube.com")) {
			parse_str(parse_url($str, PHP_URL_QUERY), $get);
			$video = $get["v"];
		}
		//1.2.114 уточнил условие для youtube
		//https://youtu.be/jwPS_6UTbyA
		elseif (strpos($str,'youtu.be')) {
			$video = substr($str,-11);
		}
		elseif (strpos($str,'vimeo')) {
			$video = substr($str,18);
		}
	}
	return $video;
}

/**
 * функция установки формата цены на товар
 * @param $price цена из БД
 * @return string форматированная цена
 * @version v1.2.100
 * v1.2.100 - добавлена
 */
function price_format ($price) {
	//без копеек
	$price = number_format($price, 0, '', ' ');
	//с копейками и
	//$price =  number_format($price, 2, '.', ' ');
	return $price;
}

/**
 * определить соцсеть по  урлу
 * @param $url - урл профиля в соцсети
 * @return string - название созсети
 * v1.4.47 - добавлена
 */
function get_social($url) {
	if(preg_match('/^https?:\/\/(?:www\.)?([a-z0-9\-\.]+)(?:\/|$)/i', $url, $result)) {
		$array = explode('.',$result[1]);
		return $array[0];
		//dd($result);
	}
	return  'unknown';
}

/**
 * @param $q - строка из базы
 * @param $module - таблица базы
 * @return string
 * v1.4.66 - добавлена
 */
function hyppertext ($q,$module) {
	$data = $q['hypertext'] ? json_decode($q['hypertext'],true) : array();
	$content = '';
	$q['hypertext'] = $data;
	//dd($data);
	foreach ($data as $k=>$v) {
		if ($v['type']=='html') {
			$content.= $v['content'];
		}
		elseif ($v['type']=='video') {
			$content.= html_render('hypertext/video',$v);
		}
		elseif ($v['type']=='img') {
			$q['hypertext/' . $k] = $v['img'];
			$v['_img'] = get_img($module,$q,'hypertext/'.$k);
			$content.= html_render('hypertext/img',$v);
		}
		elseif ($v['type']=='images') {
			$images = array();
			foreach ($v['images'] as $key=>$im) {
				$im['_img'] = get_img($module, $q, 'hypertext' . '/' . $k.'_'.$key);
				$images[] = $im;
			}
			//одна картинка
			if (count($images)==1) {
				$images[0]['_type'] = 'horizontal';
				if (@$images[0]['size']) {
					$size = explode('x', $images[0]['size']);
					//dd($size);
					//если высота больше
					if ($size[0]<$size[1]) {
						$images[0]['_type'] = 'vertical';
						if ($size[1]/$size[0]<1.2) $images[0]['_type'] = 'square';
					}
					//если ширина больше
					elseif ($size[0]>$size[1]) {
						if ($size[0]/$size[1]<1.2) $images[0]['_type'] = 'square';
					}
					else {
						$images[0]['_type'] = 'square';
					}
				}
				$content.= html_render('hypertext/image',$images[0]);
			}
			//слайдер
			else {
				$content.= html_render('hypertext/images',$images);
			}
		}
	}
	return $content;
}
