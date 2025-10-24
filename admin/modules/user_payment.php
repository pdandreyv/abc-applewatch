<?php

// платежи пользователей
/*
 * v1.0.0
 */

$table = array(
    'id'         => 'id user_id package_id price created_at updated_at',
    'user_id'    => 'right',
    'package_id' => 'right',
    'price'      => 'right',
    'created_at' => 'date',
    'updated_at' => 'date',
);

$query = "SELECT user_payment.* FROM user_payment WHERE 1";

// форма
$form[] = array('input td3','user_id', array('name'=>'Пользователь (id)'));
$form[] = array('input td3','package_id', array('name'=>'Пакет (id)'));
$form[] = array('input td2 right','price', array('name'=>'Цена'));


