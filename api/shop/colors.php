<?php

// список цветов
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

$rows = mysql_select("SELECT id,name,code FROM colors ORDER BY sort, id","rows");
$api['success'] = 1;
$api['list'] = array();
if ($rows) foreach ($rows as $r) {
    $api['list'][] = array(
        'id' => intval($r['id']),
        'name' => $r['name'],
        'code' => $r['code']
    );
}


