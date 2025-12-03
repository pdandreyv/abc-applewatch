<?php

// Получение словаря по id языка
/*
 * v1.0.0
 * example: /api/wallpaper/dictionary?key=secret_key&language_id={id}
 * example_response:
 * {
 *   "success": 1,
 *   "language_id": 1,
 *   "dictionary": {"hello":"Привет"}
 * }
 */

include_once __DIR__.'/../_guard.php';

$languageId = isset($_REQUEST['language_id']) ? intval($_REQUEST['language_id']) : 0;
if ($languageId <= 0) {
	$api['success'] = 0;
	$api['error'] = 'validation_error';
	$api['message'] = 'language_id is required';
	return;
}

$row = mysql_select("SELECT id FROM languages WHERE id='".intval($languageId)."' LIMIT 1", 'row');
if (!$row) {
	$api['success'] = 0;
	$api['error'] = 'not_found';
	$api['message'] = 'language not found';
	return;
}

$dict = array();
// загружаем файл словаря секции wallpaper из файловой системы (как в админ-модуле languages)
$lang = array();
$file = ROOT_DIR . 'files/languages/' . intval($row['id']) . '/dictionary/wallpaper.php';
if (is_file($file)) {
	include $file; // ожидается $lang['wallpaper'] = array(...)
	if (isset($lang['wallpaper']) && is_array($lang['wallpaper'])) {
		$dict = $lang['wallpaper'];
	}
}

$api['success'] = 1;
$api['language_id'] = intval($row['id']);
$api['dictionary'] = $dict;


