<?php

// платежи пользователей
/*
 * v1.0.0
 */

// получаем данные для выпадающих списков
$users = mysql_select("SELECT id, apple_id as name FROM users WHERE apple_id != '' ORDER BY id", 'array');
$packages = mysql_select("SELECT id, CONCAT(COALESCE(title, CONCAT(count, ' - ', price, '$'))) as name FROM packages WHERE display=1 ORDER BY sort, id", 'array');

$table = array(
    'id'         => 'id apple_id package_title price created_at updated_at',
    'apple_id'   => 'right',
    'package_title' => 'right',
    'price'      => 'right',
    'created_at' => 'date',
    'updated_at' => 'date',
);

$query = "SELECT
    user_payment.*,
    users.apple_id,
    packages.title as package_title,
    packages.count as package_count,
    packages.price as package_price
FROM user_payment
LEFT JOIN users ON user_payment.user_id = users.id
LEFT JOIN packages ON user_payment.package_id = packages.id
WHERE 1";

// форма
$form[] = array('select td3','user_id', array('value'=>array(true, $users), 'name'=>'Пользователь'));
$form[] = array('select td3','package_id', array('value'=>array(true, $packages), 'name'=>'Пакет'));
$form[] = array('input td2 right','price', array('name'=>'Цена', 'required'=>false));


