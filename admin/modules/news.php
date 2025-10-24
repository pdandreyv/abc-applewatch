<?php

/*
 * v1.3.37 - created_at - mysql_fn
 * v1.4.17 - сокращение параметров form
 * v1.4.59 - hypertext
 * v1.4.89 - admin/template2/includes/filter/
 */


//$module['save_as'] = true;

$table = array(
	'id'		=>	'date:desc name url user title id',
	'_view'      => 'news',
	'name'		=>	'',
	'h1'        =>  '',
	'title'		=>	'',
	'url'		=>	'',
	'date'		=>	'date',
	'display'	=>	'boolean'
);

$filter[] = array('search');
$filter[] = array('date_from');
$filter[] = array('date_to');

if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(news.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(news.text) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";
if (@$_GET['date_from']) {
	$where.= " AND date>='".mysql_res($_GET['date_from'])."'";
}
if (@$_GET['date_to']) {
	$where.= " AND date<='".mysql_res($_GET['date_to'])."'";
}

$query = "
	SELECT news.*,
		u.email login
	FROM news
	LEFT JOIN users u ON u.id = news.user
	WHERE 1 $where
";

$form[] = array('input td7','name');
$form[] = array('input td3 datetimepicker','date');
$form[] = array('checkbox','display');
$form[] = array('input td7','h1');
//$form[] = array('tinymce td12','text',array('name'=>a18n('help_text_img&video')));
$form[] = array('hypertext td12','hypertext');
$form[] = array('seo','seo url title description');

$content = html_array('form/hypertext_templates',array('key'=>'hypertext'));