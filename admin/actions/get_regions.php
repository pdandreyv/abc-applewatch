<?php

//список регионов
/*
* v1.4.84 - список регионов
*/

$api = [];

if (@$_POST['country']) {
	$where .= " AND country ='" . intval($_POST['country']) . "'";

	$query = "
		SELECT id,name
		FROM geo_regions
		WHERE 1 " . $where . "
		ORDER BY `rank` DESC, name
	";

	$items = mysql_select($query, 'array');
	$api['regions'] = select('',$items);
}
else {
	$api['regions'] = '<option>asdfas</option>';
}

header('Content-type: application/json; charset=UTF-8');
echo json_encode($api);
die();