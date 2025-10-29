<?php

// генерации пользователей
/*
 * v1.0.0
 */

$styles = mysql_select("SELECT id,name FROM styles ORDER BY sort, id", 'array');
$colors = mysql_select("SELECT id,name FROM colors ORDER BY sort, id", 'array');
// подписи полей без суффикса _id
$a18n['style_id'] = 'Стиль';
$a18n['color_id'] = 'Цвет';

$table = array(
    'id'       => 'id user_id style_id color_id img created_at display',
    'user_id'  => 'right',
    'style_id' => $styles,
    'color_id' => $colors,
    'img'      => 'img',
    'created_at' => 'date',
    'display'  => 'boolean',
);

$query = "SELECT user_generation.* FROM user_generation WHERE 1";

$form[] = array('input td3','user_id');
$form[] = array('select td3','style_id', array('value'=>array(true, $styles), 'name'=>'Стиль'));
$form[] = array('select td3','color_id', array('value'=>array(true, $colors), 'name'=>'Цвет'));
$form[] = array('textarea td12','prompt');
$form[] = array('file td6','img',array(
    'sizes'=>array(''=>'resize 1000x1000')
));
$form[] = array('checkbox','display');



