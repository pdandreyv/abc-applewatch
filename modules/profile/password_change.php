<?php

/*
 * v1.4.54 - убрал проверку старого пароля
 */

require_once(ROOT_DIR.'functions/form_func.php');

//обрабока формы
if (count($_POST)>0) {
	//создание массива $post
	$fields = array(
		//v1.4.54 - убрал проверку старого пароля
		//'password'		=> 'required text',
		'password_new'  => 'required text',
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);
	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	//проверяем старый пароль
	//v1.4.54 - убрал проверку старого пароля
	/*
	if (user_hash_db ($user['salt'],$post['password'])!=$user['hash']) {
		$message[] = i18n('profile|error_password', true);
	}
	*/

	$post['id']		= $user['id'];
	//соль
	$post['salt'] = md5(time());
	//новый хеш
	$post['hash']   = user_hash_db($post['salt'],$post['password_new']);

	//v1.4.54 - убрал проверку старого пароля
	//unset($post['password'],$post['password_new']);
	unset($post['password_new']);

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
$abc['content'] = html_array('profile/password',$post);
