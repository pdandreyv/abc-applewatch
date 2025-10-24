<?php

/*
 * v1.4.14 - event_func
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 * v1.4.20 - значение в поле
 * v1.4.45 - карты и многоуровневый словарь
 */

$locales = array(
	'en'	=>	'Английский',
	'ar'	=>	'Арабский',
	'bg'	=>	'Болгарский',
	'ca'	=>	'Каталанский',
	'cn'	=>	'Китайский',
	'cs'	=>	'Чешский',
	'da'	=>	'Датский',
	'de'	=>	'Немецкий',
	'el'	=>	'Греческий',
	'es'	=>	'Испанский',
	'eu'	=>	'Баскский',
	'fa'	=>	'Фарси',
	'fi'	=>	'Финский',
	'fr'	=>	'Французский',
	'he'	=>	'Иврит',
	'hu'	=>	'Венгерский',
	'it'	=>	'Итальянский',
	'ja'	=>	'Японский',
	'kk'	=>	'Казахский',
	'lt'	=>	'Литовский',
	'lv'	=>	'Латышский',
	'nl'	=>	'Голландский',
	'no'	=>	'Норвежский',
	'pl'	=>	'Польский',
	'ptbr'	=>	'Португальский (Бразилия)',
	'ptpt'	=>	'Португальский',
	'ro'	=>	'Румынский',
	'ru'	=>	'Русский',
	'si'	=>	'Словенский',
	'sk'	=>	'Словацкий',
	'sl'	=>	'Словенский',
	'sr'	=>	'Сербский',
	'th'	=>	'Таиландский',
	'tr'	=>	'Турецкий',
	'tw'	=>	'Тайванский',
	'ua'	=>	'Украинский',
	'vi'	=>	'Вьетнамский',
);

//удаление языка
function event_delete_languages ($q) {
	global $config;
	if ($q) {
		foreach ($config['lang_tables'] as $key => $val) {
			foreach ($val as $k => $v) {
				mysql_fn('query', "ALTER TABLE `" . $key . "` DROP `" . $k . $q['id'] . "`");
			}
		}
	}
}

function event_change_languages ($q) {
	global $config;
	if (is_dir(ROOT_DIR . 'files/languages/' . $q['id'] . '/dictionary') || mkdir(ROOT_DIR . 'files/languages/' . $q['id'] . '/dictionary', 0755, true)) {
		$post = stripslashes_smart($_POST);
		if (@$post['dictionary']) foreach ($post['dictionary'] as $key => $val) {
			$str = '<?php' . PHP_EOL;
			$str .= '$lang[\'' . $key . '\'] = array(' . PHP_EOL;
			foreach ($val as $k => $v) {
				//v1.4.45 - для многоуровневого словаря
				if (is_array($v)) $v = serialize($v);
				$str .= "	'" . $k . "'=>'" . str_replace("'", "\'", $v) . "'," . PHP_EOL;
			}
			$str .= ');';
			$str .= '?>';
			$fp = fopen(ROOT_DIR . 'files/languages/' . $q['id'] . '/dictionary/' . $key . '.php', 'w');
			fwrite($fp, $str);
			fclose($fp);
		}
	}
	//если мультиязычный то нужно добавлять колонки в мультиязычные таблицы
	if ($config['multilingual']) {
		if ($_GET['id'] == 'new') {
			foreach ($config['lang_tables'] as $key=>$val) {
				foreach ($val as $k=>$v) {
					mysql_fn('query',"ALTER TABLE `".$key."` ADD `".$k.$q['id']."` ".$v." AFTER `".$k."`");
				}
			}
		}
	}
}

//многоязычный
if ($config['multilingual']) {
	$module['save_as'] = true;
	$table = array(
		'id'			=>	'rank:desc name id',
		'name'			=>	'',
		'rank'			=>	'',
		'url'			=>	'',
		'localization'	=>	$locales,
		'display'		=>	'display'
	);
	$form[0][] = array('input td4','name');
	$form[0][] = array('input td2','rank');
	$form[0][] = array('input td2','url');
	$form[0][] = array('select td2','localization',array('value'=>array(true,$locales)));
	$form[0][] = array('checkbox td2','display');
}
//одноязычный
else {
	$module['one_form'] = true;
	$get['id'] = 1;
	if ($get['u']!='edit') {
		$post = mysql_select("
			SELECT *
			FROM languages
			WHERE id = 1
			LIMIT 1
		",'row');
	}
}

$a18n['localization'] = 'localization';

//исключения
if ($get['u']=='edit') {
	unset($post['dictionary']);
}
else {
	if ($get['id']>0) {
		$root = ROOT_DIR . 'files/languages/' . $get['id'] . '/dictionary';
		if (is_dir($root) && $handle = opendir($root)) {
			while (false !== ($file = readdir($handle))) {
				if (strlen($file) > 2)
					include(ROOT_DIR . 'files/languages/' . $get['id'] . '/dictionary/' . $file);
			}
		}
	}
}

//v1.4.16 - $delete удалил confirm
$delete = array('pages'=>'language');

//вкладки
$tabs = array(
	0 => 'Общее',
	1 => 'Формы',
	2 => 'Профайл',
	3 => 'Каталог',
	4 => 'Корзина',
	5 => 'Яндекс-маркет',
	6 => 'Рассылка',
	7 => 'Календарь',
	8 => 'Карта'
);

$form[0][] = lang_form('input td12','common|site_name','название сайта');
$form[0][] = lang_form('textarea td12','common|script_head','metatag (внутри тега head)');
$form[0][] = lang_form('textarea td12','common|script_body_start','после открывающегося тега body');
$form[0][] = lang_form('textarea td12','common|script_body_end','перед закрывающимся тегом body');
$form[0][] = lang_form('textarea td12','common|txt_head','текст в шапке');
$form[0][] = lang_form('textarea td12','common|txt_index','текст на главной');
$form[0][] = lang_form('input td12','common|info','информация');
$form[0][] = lang_form('textarea td12','common|social','социальные кнопки');
$form[0][] = lang_form('textarea td12','common|txt_footer','текст в подвале');
$form[0][] = lang_form('input td12','common|str_no_page_name','название страницы 404');
$form[0][] = lang_form('textarea td12','common|txt_no_page_text','текст страницы 404');
$form[0][] = lang_form('input td12','common|wrd_more','подробнее');
$form[0][] = lang_form('input td12','common|msg_no_results','нет результатов');
$form[0][] = lang_form('input td12','common|wrd_no_photo','нет картинки');
$form[0][] = lang_form('input td4','common|breadcrumb_index','хлебные крошки: на главную');
$form[0][] = lang_form('input td4','common|breadcrumb_separator','хлебные крошки: разделитель');
$form[0][] = lang_form('input td4','common|make_selection','сделайте выбор');
$form[0][] = lang_form('input td4','common|pagination_prev','&#171;');
$form[0][] = lang_form('input td4','common|pagination_next','&#187;');
$form[0][] = lang_form('input td4','common|pagination_count_all','все');

$form[1][] = '<h2>Форма обратной связи</h2>';
$form[1][] = lang_form('input td12','feedback|name','имя');
$form[1][] = lang_form('input td12','feedback|email','еmail');
$form[1][] = lang_form('input td12','feedback|text','сообщение');
$form[1][] = lang_form('input td12','feedback|send','отправить');
$form[1][] = lang_form('input td12','feedback|attach','прикрепить файл');
$form[1][] = lang_form('input td12','feedback|message_is_sent','сообщение отправлено');
$form[1][] = '<h2>Сообщения в формах</h2>';
$form[1][] = lang_form('input td12','validate|no_required_fields','не заполнены обязательные поля');
$form[1][] = lang_form('input td12','validate|short_login','короткий логин');
$form[1][] = lang_form('input td12','validate|not_valid_login','некорректный логин');
$form[1][] = lang_form('input td12','validate|not_valid_email','некорректный email');
$form[1][] = lang_form('input td12','validate|not_valid_password','некорректный пароль');
$form[1][] = lang_form('input td12','validate|not_valid_captcha','некорректный защитный код');
$form[1][] = lang_form('input td12','validate|not_valid_captcha2','отключены скрипты');
$form[1][] = lang_form('input td12','validate|error_email','ошибка при отправке письма');
$form[1][] = lang_form('input td12','validate|no_email','в базе нету такого email');
$form[1][] = lang_form('input td12','validate|duplicate_login','дублирование логина');
$form[1][] = lang_form('input td12','validate|duplicate_email','дублирование email');
$form[1][] = lang_form('input td12','validate|duplicate_phone','дублирование телефона');
$form[1][] = lang_form('input td12','validate|not_match_passwords','пароли не совпадают');

$form[2][] = lang_form('input td12','profile|hello','здравствуйте');
$form[2][] = lang_form('input td12','profile|link','личный кабинет');
$form[2][] = lang_form('input td12','profile|exit','выйти');
$form[2][] = '<h2>Меню личного кабинета</h2>';
$form[2][] = lang_form('input td12','profile|user_edit','личные данные');
$form[2][] = lang_form('input td12','profile|password_change','изменить пароль');
$form[2][] = lang_form('input td12','profile|socials','социальные профили');
$form[2][] = '<h2>Форма авторизации/регистрации/редактирования</h2>';
$form[2][] = lang_form('input td3','profile|email','еmail');
$form[2][] = lang_form('input td3','profile|password','пароль');
$form[2][] = lang_form('input td3','profile|password2','подтв. пароль');
$form[2][] = lang_form('input td3','profile|old_password','старый пароль');
$form[2][] = lang_form('input td3','profile|new_password','новый пароль');
$form[2][] = lang_form('input td3','profile|save','сохранить');
$form[2][] = lang_form('input td3','profile|registration','регистрация');
$form[2][] = lang_form('input td3','profile|enter','войти');
$form[2][] = lang_form('input td3','profile|remember_me','запомнить меня');
$form[2][] = lang_form('input td3','profile|auth','авторизация');
$form[2][] = lang_form('input td3','profile|remind','забыли пароль');
$form[2][] = lang_form('input td12','profile|successful_registration','успешная регистрация');
$form[2][] = lang_form('input td12','profile|successful_auth','успешная авторизация');
$form[2][] = lang_form('input td12','profile|error_auth','ошибка авторизации');
$form[2][] = lang_form('input td12','profile|error_auth_social','ошибка авторизации через соцсеть');
$form[2][] = lang_form('input td12','profile|error_password','неправильный пароль');
$form[2][] = lang_form('input td12','profile|msg_exit','Вы вышли!');
$form[2][] = lang_form('input td12','profile|go_to_profile','перейти в профиль');
$form[2][] = lang_form('input td12','profile|saved_success','Измененения успешно сохранены');
$form[2][] = '<h2>Восстановление пароля</h2>';
$form[2][] = lang_form('input td12','profile|remind_button','отправить письмо по восстановлению пароля');
$form[2][] = lang_form('input td12','profile|successful_remind','отправлено письмо по восстановлению пароля');
$form[2][] = '<h2>Социальные профили</h2>';
$form[2][] = lang_form('input td3','socials|1','Вконтакте');
$form[2][] = lang_form('input td3','socials|2','Facebook');
$form[2][] = lang_form('input td3','socials|3','Google');
$form[2][] = lang_form('input td3','socials|4','Yandex');
$form[2][] = lang_form('input td3','socials|5','Mail.ru');
$form[2][] = lang_form('input td3','socials|on','Подключить');
$form[2][] = lang_form('input td3','socials|off','Отключить');
$form[2][] = lang_form('input td3','socials|confirm_delete','Подтвердить удаление');
$form[2][] = lang_form('input td6','socials|uid_error','Данный социальный профиль уже привязан к другому пользователю');

$form[3][] = lang_form('input td3','shop|catalog','каталог');
$form[3][] = lang_form('input td3','shop|new','новинки');
$form[3][] = lang_form('input td3','shop|brand','производитель');
$form[3][] = lang_form('input td3','shop|article','артикул');
$form[3][] = lang_form('input td3','shop|parameters','параметры');
$form[3][] = lang_form('input td3','shop|price','цена');
$form[3][] = lang_form('input td3','shop|currency','валюта');
$form[3][] = lang_form('input td3','shop|product_random','случайный товар');
$form[3][] = lang_form('input td3','shop|filter_button','искать');
$form[3][] = '<h2>Отзывы</h2>';
$form[3][] = lang_form('input td3','shop|reviews','Отзывы');
$form[3][] = lang_form('input td3','shop|review_add','Оставить отзыв');
$form[3][] = lang_form('input td3','shop|review_name','имя');
$form[3][] = lang_form('input td3','shop|review_email','еmail');
$form[3][] = lang_form('input td3','shop|review_text','сообщение');
$form[3][] = lang_form('input td3','shop|review_send','отправить');
$form[3][] = lang_form('input td12','shop|review_is_sent','отзыв добавлен');

$form[4][] = lang_form('input td3','basket|buy','купить');
$form[4][] = lang_form('input td3','basket|basket','корзина');
$form[4][] = lang_form('input td12','basket|empty','пустая корзина');
$form[4][] = lang_form('input td12','basket|go_basket','перейти в корзину');
$form[4][] = lang_form('input td12','basket|go_next','продолжить покупки');
$form[4][] = lang_form('input td12','basket|product_added','товар добавлен');
$form[4][] = '<h2>Оплата</h2>';
$form[4][] = lang_form('input td12','order|payments','оплата');
$form[4][] = lang_form('input td12','order|pay','оплатить');
$form[4][] = lang_form('input td12','order|paid','оплачен');
$form[4][] = lang_form('input td12','order|not_paid','не плачен');
$form[4][] = lang_form('textarea td12','order|success','успешная оплата');
$form[4][] = lang_form('textarea td12','order|fail','отказ оплаты');

$form[4][] = '<h2>Таблица товаров</h2>';
$form[4][] = lang_form('input td3','basket|product_id','id товара');
$form[4][] = lang_form('input td3','basket|product_name','название товара');
$form[4][] = lang_form('input td3','basket|product_price','цена');
$form[4][] = lang_form('input td3','basket|product_count','количество');
$form[4][] = lang_form('input td3','basket|product_summ','сумма');
$form[4][] = lang_form('input td3','basket|product_cost','стоимость');
$form[4][] = lang_form('input td3','basket|product_delete','удалить');
$form[4][] = lang_form('input td3','basket|total','итого');
$form[4][] = '<h2>Параметры заказа</h2>';
$form[4][] = lang_form('input td3','basket|profile','личные данные');
$form[4][] = lang_form('input td3','basket|delivery','доставка');
$form[4][] = lang_form('input td3','basket|delivery_cost','стоимость доставки');
$form[4][] = lang_form('input td3','basket|comment','коммен к заказу');
$form[4][] = lang_form('input td3','basket|order','оформить заказ');
$form[4][] = '<h2>Статистика заказов</h2>';
$form[4][] = lang_form('input td3','basket|orders','статистика заказов');
$form[4][] = lang_form('input td3','basket|order_name','заказ');
$form[4][] = lang_form('input td3','basket|order_from','от');
$form[4][] = lang_form('input td3','basket|order_status','статус');
$form[4][] = lang_form('input td3','basket|order_date','дата');
$form[4][] = lang_form('input td3','basket|view_order','просмотр заказа');

$form[5][] = 'Полное описание можно найти на странице <a target="_balnk" href="http://help.yandex.ru/partnermarket/shop.xml">http://help.yandex.ru/partnermarket/shop.xml</a><br /><br />';
$form[5][] = lang_form('input td12','market|name','Короткое название магазина');
$form[5][] = lang_form('input td12','market|company','Полное наименование компании');
$form[5][] = lang_form('input td12','market|currency','Валюта магазина');

$form[6][] = '<h2>Основной шаблон автоматического письма</h2>';
$form[6][] = lang_form('textarea td12','common|letter_top','Текст в шапке письма');
$form[6][] = lang_form('textarea td12','common|letter_footer','Текст в подвале письма');
$form[6][] = '<h2>Основной шаблон письма рассылки</h2>';
$form[6][] = lang_form('textarea td12','subscribe|top','Текст в шапке рассылки');
$form[6][] = lang_form('textarea td12','subscribe|bottom','Текст в подвале рассылки');
$form[6][] = lang_form('input td8','subscribe|letter_failure_str','Если вы хотите отписаться от рассылки нажмите на');
$form[6][] = lang_form('input td4','subscribe|letter_failure_link','ссылку');
$form[6][] = '<h2>Подписка</h2>';
$form[6][] = lang_form('input td12','subscribe|on_button','Подписаться');
$form[6][] = lang_form('input td12','subscribe|on_success','Вы успешно подписаны');
$form[6][] = lang_form('input td12','subscribe|failure_text','Подтвердите, что хотите отписаться');
$form[6][] = lang_form('input td12','subscribe|failure_button','Отписаться');
$form[6][] = lang_form('input td12','subscribe|failure_success','Вы отписаны');

$form[7][] = lang_form('input td3','calendar|year','год');
$form[7][] = lang_form('input td3','calendar|y','г.');
$form[7][] = lang_form('input td3','calendar|month','месяц');
$form[7][] = lang_form('input td3','calendar|m','m.');
$form[7][] = lang_form('input td3','calendar|day','день');
$form[7][] = lang_form('input td3','calendar|d','д.');
$form[7][] = '<h2>Полные названия месяцев</h2>';
$form[7][] = lang_form('input td3','calendar|month_01','январь');
$form[7][] = lang_form('input td3','calendar|month_02','февраль');
$form[7][] = lang_form('input td3','calendar|month_03','март');
$form[7][] = lang_form('input td3','calendar|month_04','апрель');
$form[7][] = lang_form('input td3','calendar|month_05','май');
$form[7][] = lang_form('input td3','calendar|month_06','июнь');
$form[7][] = lang_form('input td3','calendar|month_07','июль');
$form[7][] = lang_form('input td3','calendar|month_08','август');
$form[7][] = lang_form('input td3','calendar|month_09','сентябрь');
$form[7][] = lang_form('input td3','calendar|month_10','октябрь');
$form[7][] = lang_form('input td3','calendar|month_11','ноябрь');
$form[7][] = lang_form('input td3','calendar|month_12','декабрь');
$form[7][] = '<h2>Полные названия месяцев в родительном падеже</h2>';
$form[7][] = lang_form('input td3','calendar|month2_01','января');
$form[7][] = lang_form('input td3','calendar|month2_02','февраля');
$form[7][] = lang_form('input td3','calendar|month2_03','марта');
$form[7][] = lang_form('input td3','calendar|month2_04','апреля');
$form[7][] = lang_form('input td3','calendar|month2_05','мая');
$form[7][] = lang_form('input td3','calendar|month2_06','июня');
$form[7][] = lang_form('input td3','calendar|month2_07','июля');
$form[7][] = lang_form('input td3','calendar|month2_08','августа');
$form[7][] = lang_form('input td3','calendar|month2_09','сентября');
$form[7][] = lang_form('input td3','calendar|month2_10','октября');
$form[7][] = lang_form('input td3','calendar|month2_11','ноября');
$form[7][] = lang_form('input td3','calendar|month2_12','декабря');
$form[7][] = '<h2>Короткие названия месяцев</h2>';
$form[7][] = lang_form('input td3','calendar|mth_01','янв');
$form[7][] = lang_form('input td3','calendar|mth_02','фев');
$form[7][] = lang_form('input td3','calendar|mth_03','мар');
$form[7][] = lang_form('input td3','calendar|mth_04','апр');
$form[7][] = lang_form('input td3','calendar|mth_05','май');
$form[7][] = lang_form('input td3','calendar|mth_06','июн');
$form[7][] = lang_form('input td3','calendar|mth_07','июл');
$form[7][] = lang_form('input td3','calendar|mth_08','авг');
$form[7][] = lang_form('input td3','calendar|mth_09','сен');
$form[7][] = lang_form('input td3','calendar|mth_10','окт');
$form[7][] = lang_form('input td3','calendar|mth_11','ноя');
$form[7][] = lang_form('input td3','calendar|mth_12','дек');
$form[7][] = '<h2>Полные дней недели</h2>';
$form[7][] = lang_form('input td3','calendar|day_1','понедельник');
$form[7][] = lang_form('input td3','calendar|day_2','вторник');
$form[7][] = lang_form('input td3','calendar|day_3','среда');
$form[7][] = lang_form('input td3','calendar|day_4','четверг');
$form[7][] = lang_form('input td3','calendar|day_5','пятница');
$form[7][] = lang_form('input td3','calendar|day_6','субота');
$form[7][] = lang_form('input td3','calendar|day_7','воскресенье');
$form[7][] = '<h2>Короткие дней недели</h2>';
$form[7][] = lang_form('input td3','calendar|d_1','пн');
$form[7][] = lang_form('input td3','calendar|d_2','вт');
$form[7][] = lang_form('input td3','calendar|d_3','ср');
$form[7][] = lang_form('input td3','calendar|d_4','чт');
$form[7][] = lang_form('input td3','calendar|d_5','пт');
$form[7][] = lang_form('input td3','calendar|d_6','сб');
$form[7][] = lang_form('input td3','calendar|d_7','вс');

//v1.4.45 - правки в карты
/* *
$form[8][] = array('yandex_map','dictionary[map]',array(
	'value'=>@$lang['map'],
));
html_sources('footer','yandex_map');
/* */
$form[8][] = array('google_map','dictionary[map]',array(
	'value'=>@$lang['map'],
	//api/common/google_autocomplete - с автозаполнением или без
	'autocomplete'=>0
));
html_sources('footer','google_map');
/* */


function lang_form($type,$key,$name) {
	global $lang;
	$key = explode('|',$key);
	//автозаполнение пустых полей
	if (/*@$_GET['fuel'] AND */!isset($lang[$key[0]][$key[1]])) $lang[$key[0]][$key[1]] = $name;
	return array ($type,'dictionary['.$key[0].']['.$key[1].']',array(
		'name'=>$name.' {'.$key[0].'|'.$key[1].'}',
		'title'=>$key[0].'|'.$key[1],
		//v1.4.20 - значение в поле
		'value'=>$lang[$key[0]][$key[1]]
	));
}