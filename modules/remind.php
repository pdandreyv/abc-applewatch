<?php

$post = array();
//обрабока формы
if (count($_POST)>0) {
	//загрузка функций для формы
	require_once(ROOT_DIR.'functions/form_func.php');
	require_once(ROOT_DIR.'functions/mail_func.php');
	//определение значений формы
	$fields = array(
		'email'		=>	'required email',
		'captcha'	=>	'required captcha2'
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);

	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	if (count($message)==0) {
		if ($q = mysql_select("
			SELECT *
			FROM users
			WHERE email = '".mysql_res(strtolower($post['email']))."'
			LIMIT 1
		",'row')) {
			mailer('remind',$lang['id'],$q,$post['email']);
			$post['success'] = 1;
			/*if (email(
				$config['email'],
				$post['email'],
				'Востановление пароля на сайте '.$_SERVER['SERVER_NAME'],
				html_array('mailer/remind',$q)
			)) $post['success'] = 1;
			else $message[] = $lang['msg_error_email'];//'Произошла ошибка с отправлением письма, если это повторится, сообщите администартору '.$config['email'].'!';
			*/
		}
		else $message[] =i18n('validate|no_email',true);//'Данный E-mail не закреплён ни за одним пользователем!';
	}
	if (count($message)>0) $post['message'] = $message;
}
$abc['post'] = $post;