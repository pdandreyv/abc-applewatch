<?php

// языки для обоев
/*
 * v1.0.0
 */

$table = array(
    'id'           => 'id name localization display sort created_at updated_at',
    'name'         => '',
    'localization' => '',
    'display'      => 'display',
    'sort'         => 'right',
    'created_at'   => 'date',
    'updated_at'   => 'date',
);

$query = "SELECT wallpaper_languages.* FROM wallpaper_languages WHERE 1";

// форма основных полей
$form[] = array('input td6','name');
$form[] = array('input td3','localization', array('help'=>'Напр.: en, ru, de'));
$form[] = array('checkbox td2','display');
$form[] = array('input td1 right','sort');

// блок словаря (кастомный рендер ниже через html_render)
$form[] = array('wallpaper_dictionary td12','dictionary');


