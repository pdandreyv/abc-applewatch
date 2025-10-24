<?php

/*
 *  v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=>	'date:desc name url user title id',
	'name'		=>	'',
	'title'		=>	'',
	'url'		=>	'',
	'date'		=>	'date',
	'display'	=>	'boolean'
);

$query = "
	SELECT news.*,
		u.email login
	FROM news
	LEFT JOIN users u ON u.id = news.user
	WHERE 1
";

$form[] = array('input td7','name');
$form[] = array('input td3','date');
$form[] = array('checkbox','display');
$form[] = array('tinymce td12','text');
$form[] = array('seo','seo url title description');