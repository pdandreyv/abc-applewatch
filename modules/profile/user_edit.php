<?php

/*
 * v1.4.54 - косяк при изменении данных
 */
require_once(ROOT_DIR.'functions/form_func.php');

//обрабока формы
if (count($_POST)>0) {
	//создание массива $post
	$fields = array(
		'email'		=> 'required email',
		'phone'     => 'phone',
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);
	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	//проверка существования мыла
	if ($post['email']) {
		$num_rows = mysql_select("SELECT id FROM users WHERE id!=" . $user['id'] . " AND email = '" . mysql_res(strtolower($post['email'])) . "'  LIMIT 1", 'num_rows');
		if ($num_rows == 1) {
			$message[] = i18n('validate|duplicate_email', true);
		}
	}
	else $post['email'] = null;

	//проверка существования телефона
	if ($post['phone']) {
		$num_rows = mysql_select("SELECT id FROM users WHERE id!=" . $user['id'] . " AND phone = '" . mysql_res(strtolower($post['phone'])) . "'  LIMIT 1", 'num_rows');
		if ($num_rows == 1) {
			$message[] = i18n('validate|duplicate_phone', true);
		}
	}
	else $post['phone'] = null;

	$post['id']		= $user['id'];
	$post['fields'] = serialize(stripslashes_smart(@$_POST['fields']));

	unset($post['password']);

	if (count($message)==0) {
		if (mysql_fn('update','users',$post)) {
			$user = user('re-auth');
		}
		$message[]= i18n('profile|saved_success');
	}
	$post['message'] = $message;
}
else $post = $user;

//вывод нтмл
$abc['content'] = html_render('profile/edit',$post);
