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

$tabs = array(
	1=>'Общее',
);

$form[1][] = array('input td6','name');
$form[1][] = array('input td2 right','sort');
$form[1][] = array('textarea td12','prompt');
$form[1][] = array('checkbox','display');


