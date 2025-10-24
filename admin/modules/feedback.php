<?php

//обратная связь
/**
 * v1.2.21 - добавил language;
 * v1.4.17 - сокращение параметров form
 * v1.4.48 - удалил дату
 */

$a18n['display'] = 'просмотрено';
$a18n['name'] = 'имя';
$a18n['page'] = 'страница';
$a18n['page_name'] = 'название страницы';
$a18n['page_url'] = 'урл страницы';

$table = array(
	'_edit'=>'edit',//только редактирование
	'id'		=>	'id:desc name email created_at',
	'name'		=>	'',
	'email'		=>	'',
	'page'      =>  '<a target="_blank" href="{page_url}">{page_name}</a>',
	'created_at'	=>	'date_smart',
	'language'  =>  '',
	'display'   =>  'display'
);
//v1.2.21
if ($config['multilingual']) $table['language'] = $languages;
else unset($table['language']);

$filter[] = array('search');

$where = '';
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(feedback.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(feedback.email) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(feedback.text) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "SELECT * FROM feedback WHERE 1 $where";

$form[] = array('input td4','name');
$form[] = array('input td4','email');
$form[] = array('text td2','created_at');
$form[] = array('checkbox','display');
$form[] = array('input td6','page_name');
$form[] = array('input td6','page_url');
$form[] = array('textarea td12','text');
$form[] = array('textarea td12','comment');

$form[] = array('file_multi','files',array(
	'name'=>'файлы',
	'fields'=>array('name'=>'input')
));