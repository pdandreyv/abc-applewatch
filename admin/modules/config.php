<?php

//динамические настройки сайта - хранятся в /config.php
/*
* v1.4.17 - сокращение параметров form
  v1.4.46 - проблема с сохранением
*/

$module['one_form'] = true;

$get['id']='config';
if ($get['u']!='edit') $post = $config;
if ($get['u']=='edit') {
	$content = "<?php\r\n";
	foreach($post as $k=>$v)
		$content.= '$config[\''.$k.'\']=\''.str_replace("'","\'",$v).'\';'."\r\n";
	$fp = fopen(ROOT_DIR.'_config.php', 'w');
	fwrite($fp,$content);
	fclose($fp);
	if(@$post['cache']==0) {
		delete_all(ROOT_DIR.'cache/',true);
	}
	$data['error']	= '';
	//print_r($data);
	//v1.4.46 - проблема с сохранением
	echo '<textarea>'.json_encode($data, JSON_HEX_AMP).'</textarea>';
	die();
}

$tabs = array(
	1=>'Общее',
	//2=>'Платежные агрегаторы',

	//ниже ненужные разделы
	//3=>'Индексация', //по умолчанию заккоментирована
	//4=>'Sitemap'
);

// Дополнительные настройки для генераций
$form[1][] = '<h2>Генерации обоев</h2>';
$form[1][] = array('input td3','user_free_generations', array(
	'name' => 'Бесплатных генераций при регистрации',
	'help' => 'Сколько генераций пользователь получит сразу после создания аккаунта',
	'value' => isset($config['user_free_generations']) ? $config['user_free_generations'] : 0
));
$form[1][] = array('textarea td12','user_generation_prompt', array(
	'name' => 'Дополнительный prompt для генерации обоев от пользователя',
	'help' => 'Этот текст будет добавляться к пользовательскому prompt перед отправкой в ИИ'
));

$form[1][] = '<h2>Оптимизация</h2>';
//$form[1][] = array('checkbox td12 line','cache',true,array('name'=>'включить кеширование сайта'));
$form[1][] = array('checkbox td12 line','html_minify',array('name'=>'включить сжатие html'));
$form[1][] = array('checkbox td12 line','open_graph',array('name'=>'включить протокол <a href="http://ruogp.me/">Open Graph</a>'));

$form[1][] = '<h2>Переадресации</h2>';
$form[1][] = array('checkbox td12 line','redirects',array('name'=>'включить редиректы <a target="_blank" href="/admin.php?m=redirects">настроить пути</a>'));
//v1.2.30
$form[1][] = array('checkbox td12 line','redirect_uppercase',array('name'=>'включить редиректы с верхнего регистра на нижний (NEWS->news)'));
//v1.2.34 - переадресация https
$form[1][] = array('checkbox td12 line','https',array('name'=>'включить переадресацию на протокол <a target="_blank" href="https://'.$_SERVER["SERVER_NAME"].'/">https://'.$_SERVER["SERVER_NAME"].'/</a>'));
//v1.2.45 - основной домен
$form[1][] = array('input td4','domain_main',array('name'=>'основной домен'));
//v1.2.45 - основной домен
$form[1][] = array('checkbox td8','domain_main_redirect',array('name'=>'включить переадресацию на основной домен'));

$form[1][] = '<h2>Другие настройки</h2>';
//$form[1][] = array('checkbox td12 line','editable',true,array('name'=>'включить быстрое редактирование с сайта <a target="_blank" href="/admin.php?m=user_types">настроить права доступа</a>'));
$form[1][] = array('checkbox td12 line','dummy',array('name'=>'включить заглушку для сайта (доступ на сайт будут иметь только администраторы)'));
$form[1][] = array('checkbox td12 line','uploader',array('name'=>'включить загрузку файлов через html5'));


//Платежные агрегаторы
//не показываем вкладку если нет оплаты
if (!isset($config['payments'])) unset($tabs[2]);
$modules['basket'] = mysql_select("SELECT url FROM pages WHERE module='basket'",'string');
$payment_action = $config['http_domain'].'/api/payments/';
$payment_success = $config['http_domain'].get_url('basket', 'success');
$payment_fail = $config['http_domain'].get_url('basket', 'fail');
//100 робокасса
if (isset($config['payments'][100])) {
	$form[2][] = '<div style="clear:both"><br /><b>ROBOKASSA</b> <a href="http://robokassa.ru/" target="_blank">http://robokassa.ru/</a></div>';
	$form[2][] = array('input td4', 'robokassa_login', array('name' => 'логин'));
	$form[2][] = array('input td4', 'robokassa_password1', array('name' => 'пароль1'));
	$form[2][] = array('input td4', 'robokassa_password2', array('name' => 'пароль2'));
	$form[2][] = '<br /><b>Result URL:</b> ' . $payment_action . 'robokassa_result/';
	$form[2][] = '<br /><b>Success URL:</b> ' . $payment_success;
	$form[2][] = '<br /><b>Fail URL:</b> ' . $payment_fail;
}
//200 yandex
if (isset($config['payments'][200])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>YANDEX</b> <a href="https://kassa.yandex.ru/" target="_blank">https://kassa.yandex.ru/</a></div>';
	$form[2][] = array('input td2', 'yandex_shopId', array('name' => 'идентификатор магазина'));
	$form[2][] = array('input td3', 'yandex_scid', array('name' => 'идентификатор витрины магазина'));
	$form[2][] = array('input td3', 'yandex_customerNumber', array('name' => 'идентификатор плательщика'));
	$form[2][] = array('input td2', 'yandex_shopPassword', array('name' => 'пароль'));
	$form[2][] = array('checkbox', 'yandex_demo', array('name' => 'деморежим'));
	$form[2][] = array('input td12', '', array(
		'value'=>$payment_action . 'yandex_paymentAviso',
		'name' => 'paymentAviso URL',
		'help'=>'это нужно скопировать и вставить в настройках на сайте яндекс-кассы'
	));
	$form[2][] = array('input td12', '',  array(
		'value'=>$payment_action . 'yandex_checkOrder',
		'name' => 'checkOrder URL',
		'help'=>'это нужно скопировать и вставить в настройках на сайте яндекс-кассы')
	);
}
//250 yandex2 v.1.2.46
if (isset($config['payments'][250])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>YANDEX</b> <a href="https://money.yandex.ru/myservices/online.xml" target="_blank">https://money.yandex.ru/myservices/online.xml</a></div>';
	$form[2][] = array('input td3', 'yandex2_receiver', array('name' => 'кошелек получателя'));
	$form[2][] = array('input td9', 'yandex2_secret_key', array('name' => 'секретный ключ'));
	$form[2][] = array('input td12', '', array(
		'value'=>$payment_action . 'yandex_quickpay',
		'name' => 'Адрес для уведомлений',
		'help' =>'это нужно скопировать и вставить в настройках на сайте яндекс-денег'
	));
}
//400 alfabank v.1.2.59
if (isset($config['payments'][400])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>ALFABANK</b> <a href="https://pay.alfabank.ru/" target="_blank">https://pay.alfabank.ru/</a></div>';
	$form[2][] = array('input td6', 'alfabank_userName', array('name' => 'логин'));
	$form[2][] = array('input td6', 'alfabank_password', array('name' => 'пароль'));
}
//500 liqpay privat24 v.1.1.2
if (isset($config['payments'][500])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>LIQPAY (Privat24)</b> <a href="https://liqpay.com/" target="_blank">https://liqpay.com/</a></div>';
	$form[2][] = array('input td6', 'liqpay_public_key', array('name' => 'публичный ключ'));
	$form[2][] = array('input td6', 'liqpay_private_key', array('name' => 'приватный ключ'));
}
//600 paypal
if (isset($config['payments'][600])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>PAYPAL</b> <a href="https://paypal.ru/" target="_blank">https://paypal.ru/</a></div>';
	$form[2][] = array('input td3', 'paypal_email', array('name' => 'email'));
}
//todo 700 2checkout
if (isset($config['payments'][700])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>2CHECKOUT</b> <a href="https://2checkout.com/" target="_blank">https://2checkout.com/</a></div>';
	$form[2][] = array('input td12', '2checkout_id', array('name' => '2checkout_id'));
	$form[2][] = array('input td12', '', array(
		'value'=> $payment_action . '2checkout_result',
		'name' => 'Адрес для уведомлений',
		'help'=>'это нужно скопировать и вставить в настройках на сайте 2checkout')
	);
}
//800 tinkoff v.1.2.54
if (isset($config['payments'][800])) {
	$form[2][] = '<div style="clear:both"><br /><br /><b>TINKOFF</b> <a href="https://oplata.tinkoff.ru/" target="_blank">https://oplata.tinkoff.ru/</a></div>';
	$form[2][] = array('input td4', 'tinkoff_terminalkey', array('name' => 'Терминал'));
	$form[2][] = array('input td4', 'tinkoff_password', array('name' => 'Пароль'));
	$form[2][] = array('input td12', array(
		'value'=>$payment_action . 'tinkoff',
		'name' => 'Нотификация по http(s)',
		'help'=>'это нужно скопировать и вставить в настройках на сайте tinkoff'
	));
	$form[2][] = array('input td4', '', array(
		'value'=>$payment_success,
		'name' => 'Страница успешного платежа',
		'help'=>'это нужно скопировать и вставить в настройках на сайте tinkoff'
	));
	$form[2][] = array('input td4', '', array(
		'value'=>$payment_fail,
		'name' => 'Страница ошибки оплаты',
		'help'=>'это нужно скопировать и вставить в настройках на сайте tinkoff'
	));
}

//900 sberbank
if (isset($config['payments'][900])) {
    $form[2][] = '<div style="clear:both"><br /><br /><b>СберБанк</b></div>';
    $form[2][] = array('input td6', 'sberbank_userName', array('name' => 'логин'));
    $form[2][] = array('input td6', 'sberbank_password', array('name' => 'пароль'));
    // эти поля не обязательны, достаточно логин/пароль либо токен мерчанта, т.к. логика подошла от альфабанка, то и тут я решил убрать эти поля
    //$form[2][] = array('input td4', 'sberbank_login', true, array('name' => 'Логин мерчанта'));
    //$form[2][] = array('input td4', 'sberbank_token', true, array('name' => 'API токен'));
}

//Индексация
if (isset($tabs[3])) {
	$form[3][] = '<div style="clear:both"><br /><b>ЯНДЕКС</b> <a href="https://xml.yandex.ru/settings/" target="_blank">https://xml.yandex.ru/settings/</a></div>';
	$form[3][] = array('input td4','yandex_user',array('name'=>'логин яндекс для xmlsearch'));
	$form[3][] = array('input td8','yandex_key',array('name'=>'ключ яндекс для xmlsearch'));
	$form[3][] = '<br />Для автоматической проверки страниц в индексе яндекса, нужно включить задачу cron: <a target="_blank" href="http://'.$_SERVER['HTTP_HOST'].'/cron.php?file=yandex_index">http://'.$_SERVER['HTTP_HOST'].'/cron.php?file=yandex_index</a>';
}


if (isset($tabs[4])) {
	$sitemap_generation = array(
		0 => 'все страницы',
		1 => 'не генерировать'
	);
	if (isset($tabs[3])) $sitemap_generation[2] = 'только не проиндексированные';
	$form[4][] = array('select td4', 'sitemap_generation', array(
		'name' => 'генерация sitemap.xml',
		'value'=> array(true, $sitemap_generation)
	));
	$form[4][] = 'clear';
	$form[4][] = '<b>1. все страницы</b> - файл <a target="_blank" href="/api/sitemap/common.xml">/api/sitemap/common.xml</a> будет генерироваться автоматически с url всех страниц';
	$form[4][] = '<br /><b>2. не генерировать</b> - статический файл <a target="_blank" href="/sitemap.xml">sitemap.xml</a>';
	if (isset($tabs[3])) {
		$form[4][] = '<br /><b>3. только не проиндексированные</b> - файл <a target="_blank" href="/sitemap.xml">sitemap.xml</a> будет генерироваться автоматически с урл не проиндексированных страниц';
		$form[4][] = '(<a target="_blank" href="/admin.php?m=config#3">настроить индексацию</a>)';
	}
}