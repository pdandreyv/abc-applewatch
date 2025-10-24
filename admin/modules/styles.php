<?php

// стили для генерации картинок
/*
 * v1.0.0
 */

$table = array(
    'id'   => 'id name sort',
    'name' => '',
    'sort' => 'right',
    'prompt' => ''
);

$query = "SELECT styles.* FROM styles WHERE 1";

$form[] = array('input td6','name');
$form[] = array('input td2 right','sort');
$form[] = array('textarea td12','prompt');


