<?php

// набор цветов
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'id name code sort',
    'name' => '',
    'code' => '',
    'sort' => 'right',
);

$query = "SELECT colors.* FROM colors WHERE 1";

$form[] = array('input td6','name');
$form[] = array('input td3','code');
$form[] = array('input td2 right','sort');
$form[] = array('textarea td12','prompt');


