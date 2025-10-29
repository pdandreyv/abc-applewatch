<?php

// стили для генерации картинок
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'sort id name',
    'name' => '',
    'sort' => 'right',
    'display' => 'boolean',
);

$query = "SELECT styles.* FROM styles WHERE 1";

$form[] = array('input td6','name');
$form[] = array('input td2 right','sort');
$form[] = array('textarea td12','prompt');
$form[] = array('checkbox','display');


