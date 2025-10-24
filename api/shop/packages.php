<?php

// список пакетов
/*
 * v1.0.0
 * example: /api/shop/packages?key=secret_key
 * example_response:
 * {
 *   "success": 1,
 *   "list": [
 *     {"id":1,"count":5,"price":3.99}
 *   ]
 * }
 */

include_once __DIR__.'/../_guard.php';

$rows = mysql_select("SELECT id,`count`,price FROM packages ORDER BY sort, id","rows");
$api['success'] = 1;
$api['list'] = array();
if ($rows) foreach ($rows as $r) {
    $api['list'][] = array(
        'id'    => intval($r['id']),
        'count' => intval($r['count']),
        'price' => (float)$r['price']
    );
}


