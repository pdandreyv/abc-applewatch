<?php

/*
 * v1.4.48 - удалил дату
 * v1.4.54 - чтобы соль не дублировалась
 */

$post=array('');
$message = array();

//обрабока формы
if (count($_POST)>0) {
	//загрузка функций для формы
	require_once(ROOT_DIR.'functions/form_func.php');
	//определение значений формы
	$fields = array(
		'password'	=> 'required password',
		'password2'	=> 'required password2',
		'email'		=> 'required email',
		'phone'		=> 'phone',
		'captcha'	=> 'required captcha2'
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);
	$post['fields'] = isset($_POST['fields']) ? serialize(stripslashes_smart($_POST['fields'])) : '';//дополнительные поля
	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);
	//проверка существования мыла
	$num_rows = mysql_select("SELECT id FROM users WHERE email = '".mysql_res(strtolower($post['email']))."'  LIMIT 1",'num_rows');
	if ($num_rows==1)
		$message[] =  i18n('validate|duplicate_email',true);
	//проверка существования телефона
	if ($post['phone']) {
		$num_rows = mysql_select("SELECT id FROM users WHERE phone = '" . mysql_res(strtolower($post['phone'])) . "'  LIMIT 1", 'num_rows');
		if ($num_rows == 1)
			$message[] = i18n('validate|duplicate_phone', true);
	}
	//проверка пароля
	if ($post['password']!==$post['password2'])
		$message[] = i18n('validate|not_match_passwords',true);
	//регистарация
	if (count($message)==0) {
		//v1.3.3 - разрешить null
		$config['mysql_null'] = true;
		//так как поле уникальное то null если нет значения
		if ($post['phone']=='') $post['phone'] = null;
		$post['last_visit'] = $config['datetime'];
		// стартовое количество генераций
		$post['count_generation'] = intval(config('user_free_generations', 0));
		//v1.4.54 - чтобы соль не дублировалась
		$post['salt']	= md5($post['password'].time());
		$post['hash']	= user_hash_db($post['salt'],$post['password']);
		$post['type']	= 0;
		//по умолчанию ставим запомнить меня
		$post['remember_me'] = 1;
		$password		= $post['password']; //пароль будет удален потому что такого поля нет в БД, но значение будет нужно при отправке сообщения пользователю
		unset($post['password'],$post['password2'],$post['captcha']);
		if ($post['id'] = mysql_fn('insert','users',$post)) {
			//$post['avatar'] = file_upload ('users',$post['id'],'avatar',array('size'=>'100*100'));
			$_SESSION['user'] = $user = $post;
			$post['password'] = $password;
			require_once(ROOT_DIR.'functions/mail_func.php');
			mailer('registration',$lang['id'],$post,$post['email']);
			/*email(
				$config['email'],
				$post['email'],
				'Регистрация на сайте '.$_SERVER['SERVER_NAME'],
				html_array('profile/registration_letter',$post)
			);*/
		}
		else $message[] = i18n('validate|error',true);
	}
	if (count($message)>0) $post['message'] = $message;
}

$abc['post'] = $post;
