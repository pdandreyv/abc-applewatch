<?php

/**
 * //функции для отправки уведомлений
 * v1.4.57 - добавлено
 */

/**
 * для telegram нужно создать бота в @BotFather
 */
//токен для бота @abc_cms_bot
$config['telegram_token'] = '1592629468:AAH4WsuUQiBu85EzJREZF5dV-TusCRTLaH4';
//ид чата для уведомления по умолчанию
$config['telegram_chat_id'] = '247341751';
//секретный хеш, используется если телега делает запросы на сайт чтобы обезопасить
$config['telegram_secret'] = 'abc';


/*
 * отправляет любое сообщение
 * @param $method - метод апи телеграма
 * @param array $params - набор параметров
 * @param bool $token - токен бота
 * @return json ответ от сервера
 * v1.4.57 - добавлено
 */
function telegram_push ($method,$params=array(),$token=false) {
	global $config;
	$token = $token ? $token : $config['telegram_token'];
	$url = 'https://api.telegram.org/bot'.$token.'/'.$method;

	if ($params) {
		$url.= '?'.http_build_query($params);
	}
	//echo $url;
	if (@$_GET['debug']) {
		echo $url.'<br>';
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

/**
 * @param $photo - хеш фото, нужно отправить фото боту чтобы получить его хеш
 * @param $chat - массив с данными пользователя
 * @param $keyboard - массив с клавиатурой
 */
function telegram_send_photo ($photo,$chat,$keyboard=false) {
	$params = array(
		'chat_id' => $chat['id'],
		'photo' => $photo,
	);
	if ($keyboard) {
		$params['reply_markup'] = json_encode($keyboard);
	}
	$result = telegram_push('sendPhoto', $params);
}

/**
 * @param $text - сообщение
 * @param $chat - массив с данными пользователя
 * @param $keyboard - массив с клавиатурой
 */
function telegram_send_text ($text,$chat,$keyboard=false) {
	$params = array(
		'chat_id' => $chat['id'],
		'text' => $text,
		'parse_mode'=>'html',
	);
	if ($keyboard) {
		$params['reply_markup'] = json_encode($keyboard);
	}
	$result = telegram_push('sendMessage', $params);
	//telegram_log($result);
}

/*
 * отправляет лог в чат для логов
 * @param $message
 * @param bool $chat_id
 * @param bool $token
 * @return mixed
 * v1.4.57 - добавлено
 */
function telegram_log ($message,$chat_id=false,$token=false) {
	global $config;
	$token = $token ? $token : $config['telegram_log_token'];
	$chat_id = $chat_id ? $chat_id : $config['telegram_chat_id'];
	$params = array(
		'chat_id' => $chat_id,
		'text' => $message,
		'parse_mode'=>'HTML'
	);
	$result = telegram_push ('sendMessage',$params,$token);
	/*
	$url = 'https://api.telegram.org/bot' . $token . '/sendMessage?' . $query;
	$result = file_get_contents($url);
	*/
	return $result;
}

/**
 * //установка вебхука, нужно для взаимодействия бота с сайтом
 * @param $url - урл на сайте, например {domain}/api/telegram/
 * @return boolen
 * v1.4.57 - добавлено
 */
function telegram_setWebhook ($url) {
	global $config;
	$result = telegram_push('setWebhook', array('url' => $url), $config['telegram_token']);
	if ($result) {
		$data = json_decode($result, true);
		if ($data['ok']==true) return true;
	}
	return false;
}
