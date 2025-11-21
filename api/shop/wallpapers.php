<?php

// список обоев в категории
/*
 * v1.0.0
 * example: /api/shop/wallpapers?key=secret_key&category={id}
 * example: /api/shop/wallpapers?key=secret_key&category={id}&all=1
 * example_response:
 * {
 *   "success": 1,
 *   "list": [
 *     {"id":10,"name":"Galaxy","img":"http://example.com/img.png","paid":0,"price":0}
 *   ]
 * }
 */

include_once __DIR__.'/../_guard.php';

// входные параметры
$fields = array(
    'category' => 'required int',
    'all'      => 'int', // опционально: показывать скрытые (display=0)
);
$post = form_smart($fields, stripslashes_smart($_REQUEST));
$message = form_validate($fields, $post);

if (count($message)) {
    $api['success'] = 0;
    $api['error_text'] = 'category: обязательный параметр';
    return;
}

$only_visible = empty($post['all']);
$where = " WHERE category=".intval($post['category']);
if ($only_visible) $where .= " AND display=1";

$rows = mysql_select("\n    SELECT id,name,img,price\n    FROM shop_wallpaper\n    $where\n    ORDER BY id DESC\n","rows");

$api['success'] = 1;
$api['list'] = array();
if ($rows) {
    global $config;
    foreach ($rows as $r) {
        $img = '';
        if (!empty($r['img'])) {
            $path = get_img('shop_wallpaper', $r, 'img');
            if ($path) {
                $img = $config['http_domain'] . '/_imgs/502x502' . $path;
            }
        }
        $price = (float)$r['price'];
        $api['list'][] = array(
            'id'    => intval($r['id']),
            'name'  => $r['name'],
            'img'   => $img,
            'paid'  => $price > 0 ? 1 : 0,
            'price' => $price,
        );
    }
}


