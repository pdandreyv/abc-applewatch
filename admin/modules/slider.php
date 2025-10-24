<?php

//слайдер
/*
 * v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=> 'rank:desc id',
	'img'		=> 'img',
	'name'		=> '',
	'url'		=> '',
	'rank'		=> '',
	'display'	=> 'display'
);

$form[] = array('input td8','name');
$form[] = array('input td2','rank');
$form[] = array('checkbox','display');
$form[] = array('input td12','url');
$form[] = array('tinymce td12','text');

$form[] = array('file td6','img',array(
	'sizes'=>array(''=>'resize 1000x1000')
));