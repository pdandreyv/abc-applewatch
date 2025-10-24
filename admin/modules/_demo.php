<?php

$module['table'] = 'news';

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

$query = "
	SELECT news.*,
		u.email login
	FROM news
	LEFT JOIN users u ON u.id = news.user
	WHERE 1
";

$tabs = array(
	1=>a18n('common'),
	2=>a18n('media'),
);


$form[1][] = array('input td7','name',true);
$form[1][] = array('input td3','date',true,array('attr'=>'class="datepicker"'));
$form[1][] = array('checkbox','display',true);
$form[1][] = array('multiple td12','categories',array(true,'SELECT id,name,level FROM shop_categories ORDER BY left_key'));
$form[1][] = array('input td7','h1',true);
$form[1][] = array('tinymce td12','text',true,array('name'=>a18n('help_text_img&video')));
//$form[1][] = array('hypertext td12','hypertext',true);
$form[1][] = array('seo','seo url title description',true);

$form[] = array('file','img',array(
	'name'=>'основное фото',
	'sizes'=>array(''=>'','p-'=>'cut 250x175'
	)));
$form[] = array('file_multi','images',array(
	'name'=>'картинки',
	'sizes'=>array('resize 700x700','p-'=>'cut 250x175')
));

$form[2][] = array('textarea td12','video',true,array('name'=>a18n('help_video')));