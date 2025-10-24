<?php

//авторизация
/*
 * v1.4.0 - html_render в админке
 */

$user = user('enter');
if (access('user admin')==true) {
	if ($get['m']=='login') $get['m']='index';
	die(header('location: /admin.php?m='.$get['m']));
}
if ($get['u']=='exit')		$message = 'Вы вышли!';
if (count($_POST)>0) $message = 'Вы ввели неверный логин или пароль!';

require_once(ROOT_DIR . $config['style'].'/includes/layouts/_login.php');
die();
