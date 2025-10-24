<?php

//авторизация через смс
/*
 * v1.3.32 - авторизация через смс
 * v1.4.0 - html_render в админке
 */


$user = user('sms');
if (access('user admin')==true) {
	if ($get['m']=='login_sms') $get['m']='index';
	die(header('location: /admin.php?m='.$get['m']));
}
if ($get['u']=='exit')		$message = 'Вы вышли!';
if (@$_POST['sessioninfo']) $message = 'Вы ввели неверный код из смс или телефона нет в базе!';

require_once(ROOT_DIR . $config['style'].'/includes/layouts/_login_sms.php');
die();
