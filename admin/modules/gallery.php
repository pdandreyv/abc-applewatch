<?php

/*
 * v1.3.37 - created_at - mysql_fn
 * v1.4.17 - сокращение параметров form
 */

$template = array(
	1=>'список картинок',
	2=>'листалка',
);

$table = array(
	'id'		=>	'rank name id',
	'_view'      => 'gallery',
	'img'		=>	'img',
	'name'		=>	'',
	'h1'        =>  '',
	'url'		=>	'',
	'template'	=>	$template,
	'rank'		=>	'',
	'display'	=>	'display',
);

$form[] = array('input td7','name');
$form[]	= array('select td2','template',array(
	'value'=>array(true,$template)
));
$form[] = array('input td1','rank');
$form[] = array('checkbox','display');
$form[] = array('input td7','h1');
$form[] = array('seo','seo url title description');
$form[] = array('file','img',array(
	'name'=>'основное фото',
	'sizes'=>array(''=>'','p-'=>'cut 250x175'
)));
$form[] = array('file_multi','images',array(
	'name'=>'картинки',
	'sizes'=>array(''=>'resize 700x700')
));
