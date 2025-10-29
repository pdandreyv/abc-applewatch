<?php

// набор цветов
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'sort id name code',
    'name' => '',
    'code' => '',
    'sort' => 'right',
    'display' => 'boolean',
);

$query = "SELECT colors.* FROM colors WHERE 1";

$form[] = array('input td6','name');
$form[] = array('input td3','code');
$form[] = array('input td2 right','sort');
$form[] = array('textarea td12','prompt');
$form[] = array('checkbox','display');


