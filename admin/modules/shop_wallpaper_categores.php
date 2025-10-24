<?php

// категории обоев для магазина
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'id name sort',
    'img'  => 'img',
    'name' => '',
    'sort' => 'right',
);

$query = "SELECT shop_wallpaper_categores.* FROM shop_wallpaper_categores WHERE 1";

$form[] = array('input td6','name');
$form[] = array('input td2 right','sort');
$form[] = array('file td6','img',array(
    'sizes'=>array(''=>'resize 1000x1000')
));



