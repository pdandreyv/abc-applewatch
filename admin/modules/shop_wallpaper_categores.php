<?php

// категории обоев для магазина
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'sort id name',
    'img'  => 'img',
    'name' => '',
    'sort' => 'right',
    'display' => 'boolean',
);

$query = "SELECT shop_wallpaper_categores.* FROM shop_wallpaper_categores WHERE 1";

$tabs = array(
	1=>'Общее',
);

$form[1][] = array('input td6','name');
$form[1][] = array('input td2 right','sort');
$form[1][] = array('file td6','img',array(
    'sizes'=>array(''=>'resize 1000x1000')
));
$form[1][] = array('checkbox','display');



