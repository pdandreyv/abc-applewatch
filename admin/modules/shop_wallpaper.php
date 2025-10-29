<?php

// обои (каталог) — аналог shop_items, но с привязкой к категории
/*
 * v1.0.0 - начальная версия
 */

$a18n['sc_name'] = 'категории';
$a18n['price'] = 'Платный';

// фильтры
$categories = mysql_select("SELECT id,name,0 as level FROM `shop_wallpaper_categores` ORDER BY sort, id",'rows_id');
$filter[] = array('search');
$filter[] = array('category',$categories,'категории',true);

// таблица списка

$table = array(
	'id'		=>	'sort id',
	'img'		=>	'img',
	'category'	=>	'<a href="/admin.php?m=shop_wallpaper_categores&id={category}">{sc_name}</a>',
	'created_at'=>	'date',
	'sort'		=>	'right',
	'price'		=>	'boolean',
	'display'	=>	'boolean',
);

// условия поиска
$where = '';
// поле name удаляется — поиск упрощаем
if (isset($get['search']) && $get['search']!='') {
    $s = mysql_res(mb_strtolower($get['search'],'UTF-8'));
    if (is_numeric($s)) $where.= "\n\tAND shop_wallpaper.id='".intval($s)."'\n"; // поиск только по id
}

// фильтр по категории
if (isset($get['category']) && intval($get['category'])>0) {
    $where .= "\n\tAND shop_wallpaper.category='".intval($get['category'])."'\n";
}

// фильтр вложенных категорий (как в shop_items)
$join = "";

// запрос
$query = "
  SELECT
    shop_wallpaper.*,
    sc.name sc_name
  FROM shop_wallpaper
  LEFT JOIN shop_wallpaper_categores sc ON sc.id = shop_wallpaper.category
    $join
  WHERE 1 $where
";

// форма
$form[] = array('select td4','category',array(
    'value'=>array(true,$categories)
));
$form[] = array('input td2 right','sort');
$form[] = array('file td6','img',array(
    'sizes'=>array(''=>'resize 1000x1000')
));
$form[] = array('checkbox','price');
$form[] = array('checkbox','display');


