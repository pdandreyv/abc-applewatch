<?php

//подписчики
/*
 * v1.4.17 - сокращение параметров form
 */

$filter[] = array('search');

if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(subscribers.email) like  '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT subscribers.*
	FROM subscribers
	WHERE 1 $where
";

$table = array(
	'id'		=>	'id:desc date email name',
	'email'		=>	'',
	'date'		=>	'date',
	'display'	=>	'display'
);

$form[] = array('input td7','email');
$form[] = array('input td3','date');
$form[] = array('checkbox','display');