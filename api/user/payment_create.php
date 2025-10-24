<?php

// Создание записи оплаты пользователя
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

$userId    = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
$packageId = isset($_REQUEST['package_id']) ? intval($_REQUEST['package_id']) : 0;
$price     = isset($_REQUEST['price']) ? (float)str_replace(',', '.', $_REQUEST['price']) : 0.0;

if ($userId<=0 || $packageId<=0 || $price<=0) {
    $api['success'] = 0;
    $api['error'] = 'validation_error';
    $api['message'] = 'user_id, package_id and price are required';
    return;
}

// проверим пользователя
$existsUser = mysql_select("SELECT id FROM users WHERE id='".$userId."' LIMIT 1", 'string');
if (!$existsUser) {
    $api['success'] = 0;
    $api['error'] = 'not_found';
    $api['message'] = 'user not found';
    return;
}

$row = array(
    'user_id'    => $userId,
    'package_id' => $packageId,
    'price'      => number_format($price, 2, '.', ''),
);

$id = mysql_fn('insert', 'user_payment', $row);

if ($id) {
    // инкрементируем счетчик генераций пользователя на количество из пакета
    $pkgCount = mysql_select("SELECT `count` FROM packages WHERE id='".$packageId."' LIMIT 1", 'string');
    if ($pkgCount) {
        mysql_fn('query', "UPDATE users SET count_generation = count_generation + " . intval($pkgCount) . ", updated_at = NOW() WHERE id = '".$userId."'");
    }
    $api['success'] = 1;
    $api['id'] = intval($id);
    $api['message'] = 'created';
} else {
    $api['success'] = 0;
    $api['error'] = 'db_error';
    $api['message'] = 'failed to create payment';
}


