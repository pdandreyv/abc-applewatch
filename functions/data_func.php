<?php

//массивы которые используются везде по шаблону

if (empty($abc)) $abc = array();

//язки
if ($config['multilingual']) {
	$abc['languages'] = mysql_select("SELECT id,url,name FROM languages WHERE display=1 ORDER BY `rank` DESC", 'rows');
}

//меню сайта
$menu = mysql_select("
	SELECT id,name,url,module,level,parent
	FROM pages
	WHERE display=1 AND level < 3 AND menu = 1 AND language=".$lang['id']."
	ORDER BY left_key
",'rows');
//строим дерево
$abc['menu'] = array();
foreach ($menu as $k=>$v) {
	$v['_url'] = get_url('page',$v);
	$v['_active'] = $v['_url']==$_SERVER['REQUEST_URI'] ? 1:0;
	$v['_submenu'] = array();
	if ($v['level']==1) {
		$abc['menu'][$v['id']] = $v;
	}
	if ($v['level']==2) {
		$abc['menu'][$v['parent']]['_submenu'][] = $v;
	}
}

//меню в подвале
$abc['menu_footer'] = mysql_select("
	SELECT name,url,module,level
	FROM pages
	WHERE display=1 AND level=1 AND menu2 = 1
	ORDER BY left_key",'rows','');

//меню категорий
$abc['menu_categories'] = mysql_select("
	SELECT *
	FROM shop_categories
	WHERE display = 1
	ORDER BY left_key
",'rows',60*60);

//случайный товар
$abc['product_random'] = mysql_select("SELECT *
	FROM shop_products
	WHERE display = 1 AND img!=''
	ORDER BY RAND()
	LIMIT 1
",'rows');