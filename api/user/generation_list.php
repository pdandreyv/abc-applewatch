<?php

// Список изображений пользователя по стилю
/*
 * v1.0.0
 * example: /api/user/generation_list?key=secret_key&apple_id={apple_id}&style_id={style_id}
 * example_response:
 * {
 *   "success": 1,
 *   "list": [
 *     {"id":10, "img": "http://example.com/files/user_generation/abc/10.png"}
 *   ]
 * }
 */

include_once __DIR__.'/../_guard.php';

$appleId = isset($_REQUEST['apple_id']) ? trim((string)$_REQUEST['apple_id']) : '';
$styleId = isset($_REQUEST['style_id']) ? intval($_REQUEST['style_id']) : 0;

if ($appleId === '' || $styleId<=0) {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'apple_id and style_id are required';
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

// получаем список img
$rows = mysql_select("\n    SELECT id, img\n    FROM user_generation\n    WHERE user_id='".intval($userId)."' AND style_id='".intval($styleId)."' AND display=1\n    ORDER BY id DESC\n","rows");

$api['success'] = 1;
$api['list'] = array();
if ($rows) {
    global $config;
    foreach ($rows as $r) {
        $img = '';
        if (!empty($r['img'])) {
            $img = $config['http_domain'] . get_img('user_generation', $r, 'img');
        }
        $api['list'][] = array(
            'id'  => intval($r['id']),
            'img' => $img,
        );
    }
}



