<?php

// генерации пользователей
/*
 * v1.0.0
 */

$table = array(
    'id'       => 'id user_id style_id color_id img created_at',
    'user_id'  => 'right',
    'style_id' => 'right',
    'color_id' => 'right',
    'img'      => 'img',
    'created_at' => 'date',
);

$query = "SELECT user_generation.* FROM user_generation WHERE 1";

$form[] = array('input td3','user_id');
$form[] = array('input td3','style_id');
$form[] = array('input td3','color_id');
$form[] = array('textarea td12','prompt');
$form[] = array('file td6','img',array(
    'sizes'=>array(''=>'resize 1000x1000')
));



