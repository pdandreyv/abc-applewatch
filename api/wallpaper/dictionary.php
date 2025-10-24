<?php

// Получение словаря по id языка
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

$languageId = isset($_REQUEST['language_id']) ? intval($_REQUEST['language_id']) : 0;
if ($languageId <= 0) {
	$api['success'] = 0;
	$api['error'] = 'validation_error';
	$api['message'] = 'language_id is required';
	return;
}

$row = mysql_select("SELECT id, dictionary FROM wallpaper_languages WHERE id='".intval($languageId)."' LIMIT 1", 'row');
if (!$row) {
	$api['success'] = 0;
	$api['error'] = 'not_found';
	$api['message'] = 'language not found';
	return;
}

$dict = array();
if (!empty($row['dictionary'])) {
	$decoded = json_decode($row['dictionary'], true);
	if (is_array($decoded)) $dict = $decoded;
}

$api['success'] = 1;
$api['language_id'] = intval($row['id']);
$api['dictionary'] = $dict;


