<?php

// Список доступных языков для обоев
/*
 * v1.0.0
 * example: /api/wallpaper/languages?key=secret_key
 * example_response:
 * {
 *   "success": 1,
 *   "list": [
 *     {"id":1,"name":"English","localization":"en"}
 *   ]
 * }
 */

include_once __DIR__.'/../_guard.php';

// получаем видимые языки из основной таблицы languages
$rows = mysql_select("
	SELECT id, name, localization
	FROM languages
	WHERE display = 1
	ORDER BY `rank` DESC, id ASC
", 'rows');

$api['success'] = 1;
$api['list'] = array();
if ($rows) {
	foreach ($rows as $r) {
		$api['list'][] = array(
			'id' => intval($r['id']),
			'name' => $r['name'],
			'localization' => $r['localization']
		);
	}
}


