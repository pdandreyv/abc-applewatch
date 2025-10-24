<?php

//not_visible
// список категорий магазина
/*
 * v1.0.0
 * example: /api/shop/categories?key=secret_key - только видимые категории
 * example: /api/shop/categories?key=secret_key&all=1 - все категории
 * example_response:
 * {
 *   "success": 1,
 *   "list": [
 *     {"id":1,"name":"Abstract","level":1,"parent":0,"description":"","img":"http://example.com/img1.png","total":12,"avail":5}
 *   ]
 * }
 */

// параметры
$only_visible = empty($_REQUEST['all']); // по умолчанию только видимые

$where = $only_visible ? " WHERE display=1 " : " WHERE 1 ";

$rows = mysql_select("\n    SELECT id,name,level,parent,url,description,img\n    FROM shop_categories\n    $where\n    ORDER BY left_key\n","rows");

$api['success'] = 1;
$api['list'] = array();
if ($rows) {
	global $config;
    foreach ($rows as $r) {
		$img = '';
		if (!empty($r['img'])) {
			$img = $config['http_domain'] . get_img('shop_categories', $r, 'img');
		}
        // вычисляемые поля по shop_wallpaper (в этой категории)
        $total = mysql_select("SELECT COUNT(*) FROM shop_wallpaper WHERE category=".intval($r['id'])."", 'string');
        if ($total === false) $total = 0;
        $avail = mysql_select("SELECT COUNT(*) FROM shop_wallpaper WHERE category=".intval($r['id'])." AND price=0", 'string');
        if ($avail === false) $avail = 0;
		$api['list'][] = array(
			'id'          => intval($r['id']),
			'name'        => $r['name'],
			'level'       => intval($r['level']),
			'parent'      => intval($r['parent']),
			'description' => isset($r['description']) ? $r['description'] : '',
			'img'         => $img,
            'total'       => intval($total),
            'avail'       => intval($avail),
		);
	}
}


