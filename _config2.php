<?php

$config['cms_version'] = '1.4.93';

$config['multilingual'] = true; //многоязычный сайт вкл/выкл
$config['multilingual_u0'] = false;//использовать или нет для основного языка в урл u[0]

$config['http'] = @$config['https']==1 ? 'https':'http';
$config['domain'] = @$config['domain_main'] ? $config['domain_main'] : $_SERVER['HTTP_HOST'];
//v1.2.99 - кросдоменная авторизация - $config['.main_domain']
$config['.main_domain'] = '.'.@$config['domain_main'];
$config['.main_domain'] = false;
if ($config['.main_domain']) {
	session_set_cookie_params(60 * 60 * 24 * 30, "/", $config['.main_domain']);
}
$config['http_domain'] = $config['http'].'://'.$config['domain'];

//дефолтные значение для карты
$config['map_lat'] = '55.755826';
$config['map_lng'] = '37.617299';
//ключ для карт гугла и яндекса
$config['google_map_key'] = '';
$config['yandex_map_key'] = '';
//firebase
$config['firebase_key'] = '';
$config['firebase_project'] = '';
$config['firebase_sender'] = '';

//amp-страницы по умолчанию отключены
$config['amp'] = 0;

//локальная версия
$config['local'] = (($_SERVER['REMOTE_ADDR']=='127.0.0.1' AND $_SERVER['SERVER_ADDR']=='127.0.0.1')
    OR ($_SERVER['HTTP_HOST'] == 'localhost')) ? true : false;

$config['smartoptimizer'] = false; //поставить true если nginx отдает статику и не работает смартоптимайзер

//v1.4.26 - проверка WEBP
$config['webp'] = false;
if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false && strpos( $_SERVER['HTTP_USER_AGENT'], ' Chrome/' ) !== false ) {
	$config['webp'] = true;
}
//чтобы включить, нужно установить webp-convert
//https://github.com/rosell-dk/webp-convert
//composer require rosell-dk/webp-convert
$config['webp'] = false;

//размеры картинок для автоматического ресайза
$config['_imgs'] = array(
	'shop_products'=>array('150x150','_200x200','_300x300','400x400'),
	// превью для обоев
	'shop_wallpaper'=>array('502x502'),
	// превью для категорий обоев
	'shop_wallpaper_categores'=>array('100x100'),
);

//database - для избегания случайного затирания тестовой базы поля пустые
$config['mysql_server'] = '';
$config['mysql_username'] = '';
$config['mysql_password'] = '';
$config['mysql_database'] = '';
//исключение для локальной версии
if ($config['local']) {
	$config['mysql_server'] = ($_SERVER['HTTP_HOST'] == 'localhost') ? 'mariadb' : 'localhost';
	$config['mysql_username'] = 'root';
	$config['mysql_password'] = '';
	$config['mysql_database'] = 'abc-applewatch';
}
//исключение тестовой версии
elseif($_SERVER['SERVER_NAME'] == 'abc-cms.com'){
	$config['mysql_server'] = 'localhost';
	$config['mysql_username'] = 'admin_abc-cms';
	$config['mysql_password'] = 'NZ4zG3oxTl';
	$config['mysql_database'] = 'admin_abc-cms';
}
//$config['mysql_charset']	= 'UTF8';
$config['mysql_charset']    = 'utf8mb4'; //для emoji, надо кодировку таблиц менять
$config['mysql_connect']	= false; //по умолчанию база не подключена
$config['mysql_error']		= false; //ошибка подключения к базе
$config['mysql_null'] = false; //для функции mysql_fn

//timezone
$config['timezone']			= 'Europe/Moscow';
date_default_timezone_set($config['timezone']);
$config['date'] = date('Y-m-d');
$config['datetime'] = date('Y-m-d H:i:s');

//папка со стилями
$config['style'] = 'templates';

//charset
$config['charset']			= 'UTF-8';

//debug
$config['debug'] = false; //если поставить true то будут писаться все логи log_add

// API ключ для внешних запросов
$config['api_key'] = 'secret_key';

// Stability.ai API
$config['stability_api_key'] = 'sk-xYCOCmU0tNmdrfdF4pQMxQ9F3pfERGawH4iZ3z0rNAHZSt8d';
$config['stability_engine'] = 'stable-diffusion-xl-1024-v1-0';

//виды оплат (мерчанты) - по умолчанию закомментированы
/**/
$config['payments'] = array(
	//без оплаты
	1=> 'безналичный рассчет',
	//robokassa
	//100 => 'robokassa',
	//101 => 'robokassa [терминал]',
	//102 => 'robokassa [qiwi]',
	//103 => 'robokassa [карта]',
	//104 => 'robokassa [wmr]',
	//105 => 'robokassa [yandex]',
	//yandex
	//200 => 'yandex',
	//201 => 'yandex [yandexmoney]',
	//202 => 'yandex [карта]',
	//203 => 'yandex [webmoney]',
	//204 => 'yandex [qiwi]',
	//yandex2
	//250 => 'yandex2',
	//251 => 'yandex2 [yandexmoney]',
	//252 => 'yandex2 [карта]',
	//253 => 'yandex2 [webmoney]',
	//254 => 'yandex2 [qiwi]',
	//todo qiwi
	//300 => 'qiwi',
	//alfabank
	//400 => 'alfabanki',
	//liqpay privatbank
	//500 => 'liqpay',//v.1.1.2
	//paypal
	//600 => 'paypal',
	//todo 2checkout
	//700 => '2checkout',
	//800=>'tinkoff'
    //900 => 'sberbank'
);
/**/

//соципльные профили
$config['user_socials'] = array(
	'genders'=>array(
		1=>'мужской',
		2=>'женский'
	),
	'types'=>array(
		1=>'vk',
		2=>'facebook',
		3=>'google',
		4=>'yandex',
		5=>'mailru'
	)
);

//массив всех подключаемых css и js файлов
//{localization} - будет заменяться на $lang['localization']
//? будет заменятся на гет параметр времени создания сайта
$config['sources'] = array(
	'bootstrap.css'             => '/plugins/bootstrap/css/bootstrap.min.css',
	'bootstrap.js'              => '/plugins/bootstrap/js/bootstrap.min.js',
	'common.css'				=> '/templates/css/common.css?',
	'bundle.css'				=> array(
		'/templates/src/vendors/bootstrap-4.5.0-dist/css/bootstrap.min.css',
		'/templates/src/scss/font.scss',
		'/templates/src/scss/common.scss',
	),
	'bundle.css'				=> '/templates/assets/css/bundle.min.css?',
	'common.js'				    => '/templates/scripts/common.js?',
	'bundle.js'				    => array(
		'/templates/src/vendors/jquery/jquery-3.5.1.min.js',
		'/templates/src/vendors/bootstrap-4.5.0-dist/js/bootstrap.bundle.min.js',
		'/templates/src/vendors/lazysizes/lazysizes.min.js',
		'/templates/src/js/common.js',
	),
	'bundle.js'				    => '/templates/assets/js/bundle.min.js?',
	'editable.js'				=> '/templates/scripts/editable.js',
	'font.css'				    => '/templates/css/font.css',
	'lazysizes.js'				=> '/templates/scripts/lazysizes.min.js',
	'highslide'					=> array(
		'/plugins/highslide/highslide.packed.js',
		'/plugins/highslide/highslide.css',
	),
	'highslide_gallery' 		=> array(
		'/plugins/highslide/highslide-with-gallery.js',
		'/templates/scripts/highslide.js',
		'/plugins/highslide/highslide.css',
	),
	'jquery.js'					=> '/plugins/jquery/jquery-1.11.3.min.js',
	'jquery_cookie.js'			=> '/plugins/jquery/jquery.cookie.js',
	'jquery_ui.js'				=> '/plugins/jquery/jquery-ui-1.11.4.custom/jquery-ui.min.js',
	'jquery_ui.css'			    => '/plugins/jquery/jquery-ui-1.11.4.custom/jquery-ui.min.css',
	'jquery_localization.js'	=> '/plugins/jquery/i18n/jquery.ui.datepicker-{localization}.js',
	//'jquery_form.js'			=> '/plugins/jquery/jquery.form.min.js',
	'jquery_uploader.js'		=> '/plugins/jquery/jquery.uploader.js',
	'jquery_validate.js'		=> array(
		'/plugins/jquery/jquery-validation-1.8.1/jquery.validate.min.js',
		'/plugins/jquery/jquery-validation-1.8.1/additional-methods.min.js',
		'/plugins/jquery/jquery-validation-1.8.1/localization/messages_{localization}.js',
	),
	//
	'jquery_validate.js'		=> array(
		'/plugins/jquery/jquery-validation-1.19.1/jquery.validate.min.js',
		'/plugins/jquery/jquery-validation-1.19.1/additional-methods.min.js',
		'/plugins/jquery/jquery-validation-1.19.1/localization/messages_{localization}.js',
	),
	'jquery_validate.js'=>'',
	'jquery_multidatespicker.js'=> '/plugins/jquery/jquery-ui.multidatespicker.js',
	'reset.css'					=> '/templates/css/reset.css',
	'tinymce.js'				=> '/plugins/tinymce/tinymce.min.js',//старый тинумайс
	'tinymce.js'				=> '/plugins/tinymce_4.3.11/tinymce.min.js',
	//v1.3.35
	'yandex_map'=>'<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$config['yandex_map_key'].'" type="text/javascript"></script>',
	//v1.2.71
	'google_map'=>'<script src="https://maps.googleapis.com/maps/api/js?language={localization}&key='.$config['google_map_key'].'" type="text/javascript"></script>',
	'google_markerclusterer'=>'/templates/scripts/markerclusterer.js',
);

error_reporting(E_ALL);
//не показываем предупреждения в выводе (они ломают JSON-ответы в админке)
ini_set('display_errors', 0);
//исключение для локальной версии
if ($config['local']) {
	set_error_handler('error_handler');
}
else {
	set_error_handler('error_handler');
}

ini_set('session.cookie_lifetime', 0);
ini_set('magic_quotes_gpc', 0);

header('Content-type: text/html; charset='.$config['charset']);
header('X-UA-Compatible: IE=edge');

session_start();

//обработчик ошибок
function error_handler($errno,$errmsg,$file,$line) {
	// Этот код ошибки не включен в error_reporting
	if (in_array($errno,array(8192,8))) return;
	//if (!(error_reporting() & $errno)) return;
	//не фиксируем простые ошибки
	if ($errno==E_USER_NOTICE) return true;
	//папка с логами
	$dir = ROOT_DIR.'logs';
	//файл лога
	$log_file_name = $dir.'/error_'.date('Y-m').'.txt';
	//запись в файл или перезапись файла
	$write = 'a'; //запись в файл
	//размер файла лога
	$size = 0;
	//максимальный размер файла лога
	$max_size = 100000; //1 мегабайт
	if (file_exists($log_file_name)) {
		$size = filesize($log_file_name);
		if ($size>$max_size) $write = 'w';
	}
	//строка лога
	$err_str = date('d H:i');
	$err_str.= "\t".$errno;
	$err_str.= "\tfile://".$file;
	$err_str.= "\t".$line;
	$err_str.= "\thttp://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$err_str.= "\t".$errmsg;
	$err_str.= "\r\n";
	//создаем папку
	if (!is_dir($dir)) mkdir($dir);
	//запись в файл
	$fp = fopen($log_file_name, $write);
	fwrite($fp,$err_str);
	fclose($fp);
	//фатальная ошибка
	if ($errno==E_USER_ERROR) exit(1);
	//не запускаем внутренний обработчик ошибок PHP
	return true;
}

/**
 * @param $type - тип урл
 * @param bool $q - массив значений
 * @return string - урл
 * @version v1.2.59
 * v1.1.0 - добавлена
 * v1.2.20 - обработка урл модулей
 * v1.2.46 - ошибка урл в заказе
 * v1.2.56 - index, orders, subscribe
 * v1.2.59 - lang
 */
function get_url($type='',$q=false,$param=''){
	global $modules,$config,$lang;
	//v1.2.59
	if (!isset($lang)) {
		$lang = lang();
	}
	if (!isset($modules)) {
		$modules = mysql_select("
			SELECT url name,module id
			FROM pages
			WHERE module!='pages' AND language=".$lang['id']." AND display=1
	  	",'array',60*60);
	}
	$url = '';
	//v1.2.56 - главная (1.2.74 - поправил в пагинаторе косяк с урл главной)
	if ($type=='index') {
		$url = '/';
		//v1.2.125 - главная для языка
		if ($q AND $q['id']!=1) {
			$url = '/' . $q['url'] . '/';
		}
		return $url;
	}
	//страницы
	elseif ($type=='page') {
		$url = $q['module'] == 'index' ? '/' : '/' . $q['url'] . '/';
	}
	//товары
	elseif ($type=='shop_product') {
		if (@$modules['shop']) {
			get_data('shop_categories');
			$category = get_data('shop_categories',$q['category']);
			$url = get_url('shop_category',$category);
			$url.= $q['id'] . '-' . $q['url'.$lang['i']] . '/';
			return $url;
		}
	}
	//категории
	elseif ($type=='shop_category') {
		if (@$modules['shop']) {
			$url = '/' . $modules['shop'] . '/' . $q['id'] . '-' . $q['url' .$lang['i']]. '/';
		}
	}
	//новости
	elseif ($type=='news') {
		if (@$modules['news']) {
			$url = '/' . $modules['news'] . '/';
			//страница новости
			if ($q) {
				$url.= $q['url'] . '/';
			}
		}
	}
	//галлерея
	elseif ($type=='gallery') {
		if (@$modules['gallery']) {
			$url = '/' . $modules['gallery'] . '/' .  $q['id'] . '-'.$q['url']. '/';
		}
	}
	//корзина
	elseif ($type=='basket') {
		if (@$modules['basket']) {
			$url = '/' . $modules['basket'] . '/';
			if ($q) {
				//v1.2.46
				if (is_array($q)) {
					$url.= $q['id'] . '/' . md5($q['id'] . $q['created_at']) . '/';
				}
				else {
					$url.= $q.'/';
				}
			}
		}
		else return false;
	}
	//v1.2.56 - заказы
	elseif ($type=='orders') {
		if (@$modules['profile']) {
			$url = get_url('profile','orders').  $q['id'].'/';
			return $url;
		}
	}
	//v1.2.56 - подписка
	elseif ($type=='subscribe') {
		if (@$modules['subscribe']) {
			$url = '/'.$modules['subscribe'].'/';
			if ($q=='unsubscribe') {
				$url.= 'unsubscribe/'.@$param['receiver'].'/'.md5(@$param['receiver'].md5(@$param['date'])).'/';
			}
		}
	}
	//если $type равен модулю v.1.2.20
	elseif ($type) {
		if (isset($modules[$type])) {
			$url = '/' . $modules[$type] . '/';
			if ($q AND is_string($q)) {
				$url .= $q . '/';
			}
		}
	}
	//возвращаем пусто есть не сформировало урл v.1.2.20
	if ($type AND $url=='') return false;
	//добавляем в коце язык
	if ($config['multilingual']) {
		//u[0] для в урл языков
		if ($lang['id']!=1 OR $config['multilingual_u0']==true) {
			$url = '/' . $lang['url'] . $url;
		}
	}
	//v1.2.131 - amp-страницы
	if ($param=='amp') $url.= '?view=amp';
	return $url;
}
