<?php

// Получение/создание пользователя по apple_id
/*
 * v1.0.0
 * example: /api/user/apple?key=secret_key&apple_id={apple_id} - получить/создать пользователя
 * example_response:
 * {
 *   "success": 1,
 *   "user": {"id":1,"apple_id":"abc123","count_generation":2, "has_unlocked_all": 0}
 * }
 */

include_once __DIR__.'/../_guard.php';

// входные параметры (жёсткая валидация, чтобы исключить пустое значение)
$appleId = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
if ($appleId === '') {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'apple_id is required';
    return;
}

// ищем пользователя
$user = mysql_select("SELECT * FROM users WHERE apple_id='".mysql_res($appleId)."' LIMIT 1", 'row');
if (!$user) {
    // создаём
    global $config;
    $usr = array(
        'last_visit' => $config['datetime'],
        'type'       => 0,
        'salt'       => md5(time()),
        'hash'       => NULL,
        'remember_me'=> 1,
        'count_generation' => intval(config('user_free_generations', 0)),
        'apple_id'   => $appleId,
    );
    $usr['hash'] = user_hash_db($usr['salt'], '');
    $usr['id'] = mysql_fn('insert', 'users', $usr);
    $api['success'] = 1;
    $api['message'] = 'user was created';
	$api['user'] = array(
		'id'       => intval($usr['id']),
		'apple_id' => $usr['apple_id'],
		'count_generation' => intval($usr['count_generation']),
	);
    return;
}

$api['success'] = 1;
$api['user'] = array(
    'id'       => intval($user['id']),
    'apple_id' => $user['apple_id'],
	'count_generation' => intval($user['count_generation']),
    'has_unlocked_all' => intval($user['has_unlocked_all']),
);



