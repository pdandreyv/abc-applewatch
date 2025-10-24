<?php

/*
 * v1.4.54 - редирект на изменение пароля
 */

//закрываем лк от индексации
$abc['page']['noindex'] = 1;

//востановление пароля
if (isset($_GET['email'])) {
	$user = user('remind');
	//v1.4.54 - редирект на изменение пароля
	die(header('location: '.get_url('profile','password_change')));
}

if (access('user auth')==false) {
	die(header('location: '.get_url('login')));
}
$config['profile'] = array(
	'user_edit'	=>	i18n('profile|user_edit'),
	'password_change'=>	i18n('profile|password_change'),
	'socials'=>	i18n('profile|socials'),
);
if (isset($modules['basket']))
	$config['profile']['orders'] = i18n('basket|orders');

if (array_key_exists($u[2],$config['profile']) && file_exists('modules/profile/'.$u[2].'.php')) {
	$abc['page']['name'] = $config['profile'][$u[2]];
	$abc['layout'] = $u[2];
	require_once('modules/profile/'.$u[2].'.php');
	$abc['breadcrumb'][] = array(
		'name'=>$config['profile'][$u[2]],
		'url'=>get_url('profile',$u[2])
	);
}

$abc['profile_menu'] = $config['profile'];
