<?php

// пакеты (количество и цена)
/*
 * v1.0.0
 */

$table = array(
    'id'    => 'id count price sort',
    'count' => 'right',
    'price' => 'right',
    'sort'  => 'right',
);

$query = "SELECT packages.* FROM packages WHERE 1";

$form[] = array('input td3 right','count');
$form[] = array('input td3 right','price');
$form[] = array('input td2 right','sort');


