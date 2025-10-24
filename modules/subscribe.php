<?php

/*
 * модуль подписки и отписки пользователей
 */

require_once(ROOT_DIR.'functions/mail_func.php');	//функции почты
require_once(ROOT_DIR.'functions/form_func.php'); //функции форм

//Отписка
if ($u[2]=='unsubscribe' AND $u[3] AND $u[4]) {
	$query = "SELECT * FROM subscribers WHERE LOWER(email)='".mysql_res(strtolower($u[3]))."' LIMIT 1";
	if ($subscriber = mysql_select($query,'row')) {
		//print_r($subscriber);
		//echo md5($subscriber['email'].md5($subscriber['date']));
		//echo $u[4];
		if (md5($subscriber['email'].md5($subscriber['date']))==$u[4]) {
			if (@$_POST['action']=='failure') {
				$subscriber['display'] = 0;
				mysql_fn('update','subscribers',array(
					'id'=>$subscriber['id'],
					'display'=>0
				));
				mailer('subscribe_failure',$lang['id'],$subscriber);
				//email($config['email'],$config['email'],i18n('subscribe|failure_letter_name'),html_array('subscribe/failure_letter',$subscriber));
			}
		}
		else $subscriber = '';
	}
	else $subscriber = '';
	//print_r($subscriber);
	$abc['content'] = html_array('subscribe/failure_form',$subscriber);

}
//Просмотр письма на сайте
elseif ($u[2]>0 AND $u[3] AND $u[4]) {
	$query = "SELECT * FROM subscribers WHERE LOWER(email)='".mysql_res(strtolower($u[3]))."' LIMIT 1";
	//echo $query;
	if ($subscriber = mysql_select($query,'row')
		AND $subscribe_letter = mysql_select("SELECT * FROM subscribe_letters WHERE id=".intval($u[2]),'row')
	) {
		$subscribe_letter['receiver'] = $subscriber['email'];
		$subscribe_letter['date'] = $subscriber['date'];
		echo html_array('subscribe/letter',$subscribe_letter);
		die();
	}
	else $error++;
}
//Подписка
else {
	//обрабока формы
	if (count($_POST)>0) {
		//создание массива $post
		$fields = array(
			//'name'=>'required text',
			//'surname'=>'required text',
			'email'=>'required email',
			'captcha'=> 'required captcha2'
		);
		//dd($fields);
		//создание массива $post
		$post = form_smart($fields,stripslashes_smart($_POST)); //print_r($post);
		//сообщения с ошибкой заполнения
		$message = form_validate($fields,$post);
		if (count($message)==0) {
			$post['date']		= date("Y-m-d H:i:s");
			$query = "SELECT * FROM subscribers WHERE LOWER(email)='".mysql_res(strtolower($post['email']))."' LIMIT 1";
			if ($subscriber = mysql_select($query,'row')) {
				$post['success']=1;
				if ($subscriber['display']==0) {
					mysql_fn('update','subscribers',array('id'=>$subscriber['id'],'display'=>1));
				}
			}
			else {
				unset($post['captcha']);
				$post['display'] = 1;
				if ($id=mysql_fn('insert','subscribers',$post)) {
					mailer('subscribe_on',$lang['id'],$post);
					//email($post['email'],$config['email'],i18n('subscribe|on_letter_name'),html_array('subscribe/on_letter',$post));
					$post['success']=1;
				}
				else $message[] = i18n('validate|error_email',true);
			}
		}
		$post['message'] = $message;
	}
	//вывод шаблона
	$abc['content'] = isset($post['success']) ? '':$abc['page']['text'];
	$abc['content'].= html_array('subscribe/on_form',@$post);
}