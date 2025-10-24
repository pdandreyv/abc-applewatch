<?php

/*
 *  v1.4.17 - сокращение параметров form
 */

$a18n['name2'] = 'name';

$table = array(
	'id'		=>	'rank:desc name id iso',
	'name'		=>	'',
	'name2'		=>	'',
	'iso'       =>	'',
	'rank'      => '',
	'display'	=>	'boolean'
);

$filter[] = array('search');

if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(geo_countries.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(geo_countries.name2) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(geo_countries.iso) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT *
	FROM geo_countries
	WHERE 1 $where
";

$form[] = array('input td5','name');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');

$form[] = array('input td5','name2');
$form[] = array('input td2','iso');
$form[] = array('input td2','uid');

$form[] = array('google_map','');

$form[] = array('seo','seo url');

html_sources('footer','google_map');