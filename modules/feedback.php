<?php

/**
 * обратная связь
 * v1.2.21 - добавили поле с ИД языка
 */

if ($u[2]) $error++;

//отделения
$abc['branches'] = mysql_select("SELECT * FROM shop_branches WHERE display=1 ORDER BY `rank` DESC",'rows');

//обработка формы перенесена в /ajax/feedback.php
/*
//обрабока формы
if (count($_POST)>0) {
	//загрузка функций для формы
	require_once(ROOT_DIR.'functions/form_func.php');	//функции для работы со формами

	//определение значений формы
	$fields = array(
		'email'			=>	'required email',
		'name'			=>	'required text',
		'text'			=>	'required text',
		'captcha'		=>	'required captcha2'
	);
	//создание массива $post
	$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);

	//сообщения с ошибкой заполнения
	$message = form_validate($fields,$post);

	//если нет ошибок то отправляем сообщение
	if (count($message)==0) {
		unset($_SESSION['captcha'],$post['captcha']); //убиваем капчу чтобы второй раз не отправлялось
		//прикрепленные файлы
		$files = array();
		$post['files'] = array();
		if (isset($_FILES['attaches']['name']) AND is_array($_FILES['attaches']['name'])) {
			foreach ($_FILES['attaches']['name'] as $k=>$v) if ($v) {
				$name = trunslit($v);
				$files[$name] = $_FILES['attaches']['tmp_name'][$k];
				$post['files'][] = array(
					'name'=>$v,
					'file'=>$name
				);
			}
		}
		//запись сообщения в базу вместе с файлами
		$post['files'] = count($post['files']) ? serialize($post['files']) : '';
		$post['date'] = date('Y-m-d H:i:s');
		$post['id'] = mysql_fn('insert','feedback',$post);
		//ИД языка - v,1.2.21
		$post['language'] = $lang['id'];
		if ($post['files']) {
			$i = 0;
			foreach ($files as $k=>$v) {
				$path = ROOT_DIR.'files/feedback/'.$post['id'].'/files/'.$i.'/';
				mkdir($path,0755,true);
				copy($v,$path.$k);
				$i++;
			}

		}
		//6-й параметр кому ответить
		require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
		mailer('feedback',$lang['id'],$post,false,false,$post['email'],$files);
		$post['success'] = 1;
	}
	if (count($message)>0) $post['message'] = $message;
}
else $post = array();
*/
$post = array();
