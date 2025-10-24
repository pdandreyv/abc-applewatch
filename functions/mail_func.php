<?php

//функции для работы с почтой
//TODO добавить в настройки отправку через smtp
/*
 * v1.3.39 - проверка валидности почты
 * v1.4.0 - html_render в админке
 * v1.4.22 - проверка на валидность
 */


/**
 * отправляет письма по шаблону в таблице letter_templates
 * @param string $template - letter_templates.name
 * @param int $language - ID языка
 * @param array $q - массив данных
 * @param string $receiver - получатель
 * @param string $sender - отправитель
 * @param string $reply - ответить
 * @param array $files - массив файлов
 * @return boolean - отправлено или нет
 * @see email
 * @version v1.3.39
 * v1.3.13 - имя отправителя
 * v1.3.30 - логирование письма
 * v1.3.39 - проверка валидности почты
 */
function mailer($template,$language,$q,$receiver=false,$sender=false,$reply=false,$files=false) {
	//echo "SELECT * FROM letter_templates WHERE name='".$template."'";
	if ($letter = mysql_select("SELECT * FROM letter_templates WHERE name='".$template."'",'row')) {
		global $lang,$config,$modules;
		if ($receiver==false) $receiver = $letter['receiver'] ? $letter['receiver'] : $config['receiver'];
		$sender_name = '';
		$sender_email = '';
		if ($sender==false) {
			$sender_email = $letter['sender'] ? $letter['sender'] : $config['sender'];
			$sender_name = $letter['sender_name'] ? $letter['sender_name'] : @$config['sender_name'];
			if ($sender_name=='') $sender_name = $config['domain'];
			$sender = array($sender_email=>$sender_name);
		}
		else {
			if (is_array($sender)) {
				foreach ($sender as $k=>$v) {
					$sender_name = $v;
					$sender_email = $k;
					break;
				}
			}
			else {
				$sender_email = $sender;
			}
		}
		//print_r($letter);

		//чтобы с админки отправлялись письма
		//v1.4.0 - html_render в админке
		$style = $config['style'];
		$config['style'] = 'templates';

		ob_start();
		include (ROOT_DIR.'files/letter_templates/'.$letter['name'].'/'.$language.'/subject.php');
		$subject = ob_get_clean();
		ob_start(); // echo to buffer, not screen
		include (ROOT_DIR.'files/letter_templates/'.$letter['name'].'/'.$language.'/text.php');
		$text = ob_get_clean(); // get buffer contentshtml_array('letter_templates/'.$q);
		//echo '<b>'.$subject.'</b><br />'.$text.'<br /><br />';
		if ($letter['template']) {
			if (!function_exists('html_array')) require_once(ROOT_DIR.'functions/html_func.php');
			$text = html_array('letter_templates/'.$letter['template'],$text);
		}
		//возвращаем назад стиль
		$config['style'] = $style;
		//echo ($text).'<br />';
		//закоментированна старая функция
		//return email2($sender,$receiver,$subject,$text,$reply,$files);
		//v1.3.30 - логирование письма
		$letter = array(
			'date'=>$config['datetime'],
			'date_sent'=>$config['datetime'],
			'sender_name'=>$sender_name,
			'sender'=>$sender_email,
			'receiver'=>$receiver,
			'subject'=>$subject,
			'text'=>$text,
			//'reply'=>$reply,
		);
		mysql_fn('insert','letters',$letter);
		$email = array(
			'from' => $sender,
			'to' => $receiver,
			'subject' => $subject,
			'text' => $text,
			'reply' => $reply,
			'files' => $files
		);


			//log_add('email.txt',$email);
			//отправка письма
			return email($email);
			//старая отправки
			//return email2 ($sender,$receiver,$subject,$text,$reply,$files)



	}
}


/**
 * функция для отправки письма
 * @param $data array - массив данных
 * smtp - массив данных подключения к smtp
 * subject - тема письма
 * from - отправитель
 * to - получатель
 * cc - копия
 * bc - скрытая копия
 * replay - ответить
 * cc - копия
 * bcc - скрытая копия
 * return - если письмо не пришло то куда отправить
 * type- text/html илил text/plain
 * charset - кодировка (UTF-8)
 * text - текст
 * files - прикрелпенные файлы
 * @return bool
 * http://swiftmailer.org/
 * v1.4.22 проверка на валидность
 */
function email ($data = array()) {//print_r($data);
	require_once(ROOT_DIR.'plugins/swiftmailer/lib/swift_required.php');
	// Create the Transport
	if (!empty($data['smtp'])) {//smtp данные
		$transport = Swift_SmtpTransport::newInstance();
		$transport->setHost($data['smtp']['host'])
		->setPort($data['smtp']['port'])
		->setEncryption($data['smtp']['encryption'])
		->setUsername($data['smtp']['username'])
		->setPassword($data['smtp']['password']);
	} else {
		$transport = Swift_MailTransport::newInstance();
		//$transport = Swift_SendmailTransport::newInstance();
	}
	// Create the Mailer using your created Transport
	$mailer = Swift_Mailer::newInstance($transport);
	// Create a message
	$message = Swift_Message::newInstance($data['subject']); //тема письма
	$message -> setFrom($data['from']); //отправитель
	//получатели
	$to = explode(',',$data['to']); //через запятую разделяем получателей
	$receivers = array();
	foreach ($to as $k=>$v) {
		$receiver = explode(' ',trim($v),2); //через пробел разделяем емейл и имя
		//v1.4.22 проверка на валидность
		if (filter_var($receiver[0], FILTER_VALIDATE_EMAIL)) {
			if (empty($receiver[1])) $receiver[1] = $receiver[0];
			$receivers[$receiver[1]] = $receiver[0];
		}
		else {
			log_add('email.txt',$v);
		}
	}
	if ($receivers) {
		$message->setTo($receivers); //print_r($receivers);
		if (!empty($data['replay'])) $message->setReplyTo($data['replay']); //ответить
		if (!empty($data['cc'])) $message->setCc($data['cc']); //копия
		if (!empty($data['bcc'])) $message->setBcc($data['bcc']); //скрытая копия
		if (!empty($data['return'])) $message->setReturnPath($data['return']); //если письмо не пришло то куда отправить
		if (empty($data['type'])) $data['type'] = 'text/html'; //тип текста, может быть еще 'text/plain'
		if (empty($data['charset'])) $data['charset'] = 'UTF-8';
		$message->setBody($data['text'], $data['type'], $data['charset']);
		if (!empty($data['files'])) {
			foreach ($data['files'] as $k => $v) if (is_file($v)) {
				$message->attach(
					Swift_Attachment::fromPath($v)->setFilename($k)
				);
			}
		}
		// Send the message
		return $mailer->send($message);
	}
}

/**
 * отправка email - старая функция
 * @param string|array $sender - отправитель
 * @param string $receiver - получатель
 * @param string $subject - тема письма
 * @param string $text - текст письма
 * @param string $reply - кому ответить
 * @param array $files - массив файлов array('название файла'=>'путь к файлу','название файла'=>'путь к файлу')
 * @return bool - отправлено или нет
 *
 */
function email2 ($sender,$receiver,$subject,$text,$reply=false,$files = array()) {
	global $config;
	$subject = '=?UTF-8?B?'.base64_encode(filter_var($subject)).'?=';
	$headers = "MIME-Version: 1.0".PHP_EOL;
	//если письма не доходят то отправителем надо ставить емейл который добавлен на сервере
	if (is_array($sender)) {
		$sender_email = key($sender);
		$sender_name = $sender[$sender_email];
	}
	else {
		$sender_email = $sender;
		$sender_name = $config['domain'];
	}
	$sender_name = '=?UTF-8?B?'.base64_encode(filter_var($sender_name, FILTER_SANITIZE_STRING )).'?=';
	$headers.= "From: ".$sender_name." <".$sender_email.">".PHP_EOL;
	$headers.= "Return-path: ".$sender_email.PHP_EOL;
	if ($reply) $headers.= "Reply-To: ".$reply.PHP_EOL;
	$headers.= "X-Mailer: PHP/".phpversion().PHP_EOL;
	//без файлов
	if (!is_array($files) OR count($files)==0) {
		$headers .= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
		$multipart = $text;
	}
	//с файлами
	else {
		$boundary = "--".md5(uniqid(time()));
		$headers.="Content-Type: multipart/mixed; boundary=\"".$boundary."\"".PHP_EOL;
		$multipart = "--".$boundary.PHP_EOL;
		$multipart.= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
		$multipart.= "Content-Transfer-Encoding: base64".PHP_EOL.PHP_EOL;
		$text = chunk_split(base64_encode($text)).PHP_EOL.PHP_EOL;
		$multipart.= stripslashes($text);
		//$count = count($files);
		foreach($files as $k=>$v) if (is_file($v)){
			$fp = fopen($v, "r");
			if ($fp) {
				$content = fread($fp, filesize($v));
				$multipart.= "--".$boundary.PHP_EOL;
				$multipart.= 'Content-Type: application/octet-stream'.PHP_EOL;
				$multipart.= 'Content-Transfer-Encoding: base64'.PHP_EOL;
				$multipart.= 'Content-Disposition: attachment; filename="=?UTF-8?B?'.base64_encode(filter_var($k,FILTER_SANITIZE_STRING )).'?="'.PHP_EOL.PHP_EOL;
				$multipart.= chunk_split(base64_encode($content)).PHP_EOL;
			}
			fclose($fp);
		}
		$multipart.= "--".$boundary."--".PHP_EOL;
	}
	$receivers = explode(',',$receiver);
	$return = true;
	foreach ($receivers as $k=>$v) {
		if ($k>0) sleep(1); //делаем паузу перед отправлением второго письма
		$return = mail(trim($v),$subject,$multipart,$headers) ? $return : false;
	}
	//возвращаем false если хотя бы одно письмо не отправлено
	return $return;
}