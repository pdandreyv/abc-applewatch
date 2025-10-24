<?php

// список пакетов
/*
 * v1.0.0
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


