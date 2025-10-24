<?php

/*
 *  v1.4.17 - сокращение параметров form
 */

$a18n['name2'] = 'name';

$countries = mysql_select("
	SELECT id, name
	FROM geo_countries
	ORDER BY `rank` DESC,name
","array");


$table = array(
	'id'		=>	'rank:desc name id iso',
	'name'		=>	'',
	'name2'		=>	'',
	'iso'       =>	'',
	//'country'   => $countries,
	'rank'      => '',
	'display'	=>	'boolean'
);

$filter[] = array('country',$countries,NULL);
$filter[] = array('search');

if (@$_GET['country']) $where.= " AND geo_regions.country=".intval($_GET['country']);
else $where.= " AND geo_regions.country=".key($countries);
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(geo_regions.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(geo_regions.name2) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(geo_regions.iso) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT *
	FROM geo_regions
	WHERE 1 $where
";

$form[] = array('input td5','name');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');

$form[] = array('input td5','name2');
$form[] = array('input td2','iso');
$form[] = array('input td2','uid');
$form[] = array('select td3','country',array(
	'value'=>array(true,$countries)
));

$form[] = array('google_map','');

$form[] = array('seo','seo url');

html_sources('footer','google_map');