<?php

//бренды
/*
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=>	'name:asc id',
	'name'		=>	'',
	'h1'        =>  '',
	'url'		=>	'',
	'rank'		=>	'',
	'display'	=>	'display'
);

//v1.4.16 - $delete удалил confirm
$delete = array('shop_products'=>'brand');

$form[] = array('input td8','name');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');
$form[] = array('input td8','h1');
$form[] = array('tinymce td12','text');

$form[] = array('seo','seo url title description');