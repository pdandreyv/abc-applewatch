<?php

// список категорий обоев
/*
 * v1.0.0
 */

include_once __DIR__.'/../_guard.php';

$rows = mysql_select("\n    SELECT id, name, img\n    FROM shop_wallpaper_categores\n    ORDER BY sort, id\n","rows");

$api['success'] = 1;
$api['list'] = array();
if ($rows) {
    global $config;
    foreach ($rows as $r) {
        $img = '';
        if (!empty($r['img'])) {
            $img = $config['http_domain'] . get_img('shop_wallpaper_categores', $r, 'img');
        }
        $api['list'][] = array(
            'id'   => intval($r['id']),
            'name' => $r['name'],
            'img'  => $img,
        );
    }
}



