<?php

// Подтверждение генерации: выставляет display=1
/*
 * v1.0.0
 * example: /api/user/generation_confirm?key=secret_key&apple_id={apple_id}&generation_id={id}
 * example_response:
 * {
 *   "success": 1,
 *   "message": "confirmed",
 *   "generation_id": 123
 * }
 */

include_once __DIR__.'/../_guard.php';

$appleId  = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
$genId    = isset($_REQUEST['generation_id']) ? intval($_REQUEST['generation_id']) : 0;

if ($appleId === '' || $genId<=0) {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'apple_id and generation_id are required';
    return;
}

// ищем пользователя
$userId = mysql_select("SELECT id FROM users WHERE apple_id='".mysql_res($appleId)."' LIMIT 1", 'string');
if (!$userId) {
    $api['success'] = 0;
    $api['error'] = 'not_found';
    $api['message'] = 'user not found';
    return;
}

// проверим, что генерация принадлежит пользователю
$exists = mysql_select(
    "SELECT id FROM user_generation WHERE id='".$genId."' AND user_id='".intval($userId)."' LIMIT 1",
    'string'
);
if (!$exists) {
    $api['success'] = 0;
    $api['error'] = 'not_found';
    $api['message'] = 'generation not found';
    return;
}

// подтверждение
mysql_fn('query', "UPDATE user_generation SET display=1 WHERE id='".$genId."'");

$api['success'] = 1;
$api['message'] = 'confirmed';
$api['generation_id'] = $genId;


