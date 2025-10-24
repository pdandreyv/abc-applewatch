<?php

// Список доступных языков для обоев
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

// получаем видимые языки
$rows = mysql_select(
	"SELECT id, name, localization\n\t FROM wallpaper_languages\n\t WHERE display = 1\n\t ORDER BY sort ASC, id ASC",
	'rows'
);

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


