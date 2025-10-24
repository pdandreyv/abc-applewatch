<?php

// Глобальная проверка API-ключа для всех эндпоинтов
/*
 * include_once в начале каждого API файла после загрузки конфигов
 */

global $config, $api;
if (!isset($api)) $api = array();

$key = isset($_REQUEST['key']) ? (string)$_REQUEST['key'] : '';
if (!isset($config['api_key']) || $config['api_key']==='') {
    // если ключ не настроен — блокируем доступ
    $api['success'] = 0;
    $api['error'] = 'access_denied';
    $api['message'] = 'no access';
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($api);
    exit;
}
if ($key !== $config['api_key']) {
    $api['success'] = 0;
    $api['error'] = 'access_denied';
    $api['message'] = 'no access';
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($api);
    exit;
}


