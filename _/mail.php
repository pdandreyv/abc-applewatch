<?php

/*
 * скрипт тестирование пояты на сервере
 */
error_reporting(E_ALL);

$receiver.= 'ottofonf@gmail.com';
$sender = 'ottofonf@gmail.com';

if (count(@$_POST)>0) {
	$receiver = $_POST['receiver'];
	$sender = $_POST['sender'];
	$subject = 'test';
	$text = 'test';
	if (email2 ($sender,$receiver,$subject,$text)) echo 'success';
	else echo 'error';

}
$email = 'test@metropolitan.kz';
?>
<form action="" method="post">
	<br>sender <input name="sender" value="<?=$sender?>">
	<br>receiver<input name="receiver" value="<?=$receiver?>">
	<br><input type="submit" value="Send">
</form>
<?php



function email2 ($sender,$receiver,$subject,$text,$reply=false,$files = array()) {
	global $config;
	$subject = '=?UTF-8?B?'.base64_encode(filter_var($subject)).'?=';
	$sitename = $config['domain'];
	$sitename = '=?UTF-8?B?'.base64_encode(filter_var($sitename, FILTER_SANITIZE_STRING )).'?=';
	//без файлов
	$headers = "MIME-Version: 1.0".PHP_EOL;
	//если письма не доходят то отправителем надо ставить емейл который добавлен на сервере
	$headers.= "From: ".$sitename." <".$sender.">".PHP_EOL;
	$headers.= "Return-path: ".$sender.PHP_EOL;
	if ($reply) $headers.= "Reply-To: ".$reply.PHP_EOL;
	$headers.= "X-Mailer: PHP/".phpversion().PHP_EOL;
	if (!is_array($files) OR count($files)==0) {
		$headers .= "Content-Type: text/html; charset=UTF-8".PHP_EOL;
		$multipart = $text;
	}
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