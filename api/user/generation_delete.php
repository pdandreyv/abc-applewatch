<?php

// Удаление генерации пользователя по apple_id и id
/*
 * v1.0.0
 * example: /api/user/generation_delete?key=secret_key&apple_id={apple_id}&generation_id={id}
 * example_response:
 * {
 *   "success": 1,
 *   "message": "deleted",
 *   "generation_id": 123
 * }
 */

include_once __DIR__.'/../_guard.php';

// входные параметры
$appleId = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
$genId   = isset($_REQUEST['generation_id']) ? intval($_REQUEST['generation_id']) : 0;

if ($appleId === '' || $genId<=0) {
	$api['success'] = 0;
	$api['error'] = 'validation_error';
    $api['message'] = 'apple_id and generation_id are required';
	return;
}

// ищем пользователя
$user = mysql_select("SELECT id, apple_id FROM users WHERE apple_id='".mysql_res($appleId)."' LIMIT 1", 'row');
if (!$user) {
	$api['success'] = 0;
	$api['error'] = 'not_found';
	$api['message'] = 'user not found';
	return;
}

// ищем генерацию и проверяем владельца
$row = mysql_select(
	"SELECT id, user_id, img FROM user_generation WHERE id='".intval($genId)."' LIMIT 1",
	'row'
);
if (!$row || intval($row['user_id']) !== intval($user['id'])) {
	$api['success'] = 0;
	$api['error'] = 'not_found';
	$api['message'] = 'generation not found';
	return;
}

// удаляем файл(ы)
if (!empty($row['img'])) {
	include_once(ROOT_DIR.'functions/file_func.php');
	$path = ROOT_DIR.'files/user_generation/'.str_replace('..','',$row['img']);
	// если хранится путь вида appleId/123.png — удаляем файл и, если пусто, папку
	if (is_file($path)) {
		@unlink($path);
		// попытка удалить пустую папку apple_id
		$dir = dirname($path);
		@rmdir($dir);
	}
}

// удаляем запись из БД
mysql_fn('delete','user_generation', array('id'=>$row['id']));

$api['success'] = 1;
$api['message'] = 'deleted';
$api['generation_id'] = intval($genId);

