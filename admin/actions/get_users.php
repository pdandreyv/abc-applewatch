<?php

//поиск по пользователям
/*
* v1.4.31 - создание новой записи в автозаплнении
*/

$api = array(
	'items'=>array()
);

if (@$_GET['q']) $where.= "
	AND (
		LOWER(users.email) like '%".mysql_res(mb_strtolower($_GET['q'],'UTF-8'))."%'
		OR LOWER(users.phone) like '%".mysql_res(mb_strtolower($_GET['q'],'UTF-8'))."%'
	)
";
//v1.2.28 другие пользователи не видят суперадмина
if ($user['id']!=1) $where.= ' AND users.id!=1';

$query = "
	SELECT *
	FROM users
	WHERE 1 ".$where."
	ORDER BY id
	LIMIT 20
";

$users = mysql_select($query,'rows');
if ($users) {
	foreach ($users as $q) {
		$name = $q['email'];
		if ($q['phone']) {
			$name.= ' ['.$q['phone'].']';
		}
		$api['items'][] = array(
			'id'=>$q['id'],
			'text'=>$name,
		);
	}
}
//добавление нового
if (access('admin module','users')) {
	if ($api['items'] == false) {
		$api['items'][] = array(
			'id' => 'add',
			'text' => 'добавить пользователя',
			'value' => $_GET['q'],
		);
	}
}

header('Content-type: application/json; charset=UTF-8');
echo json_encode($api);
die();