<?php

//способы доставки
/*
 * v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=>	'rank',
	'name'		=> '',
	'rank'		=>	'',
	'cost'		=>	'right',
	'free'		=>	'right',
	'display'	=>	'display'
);

$tabs = array(
	1=>'Общее',
);

$form[1][] = array('input td5','name');
$form[1][] = array('input td1 right','rank');
$form[1][] = array('input td2 right','cost',array('name'=>'стоимость доставки'));
$form[1][] = array('input td2 right','free',array('help'=>'укажите минимальную стоимость заказа для которого будет доставка бесплатной'));
$form[1][] = array('checkbox','display');
$form[1][] = array('textarea td12','text');