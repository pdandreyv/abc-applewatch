<?php

// модуль товаров для магазина
/*
 * v1.2.19 - добавил множественный выбор для параметров
 * v1.2.130 - сделал расширения и добавил чекбоксы для таблицы
 * v1.3.37 - created_at - mysql_fn
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 * v1.4.70 - урл товара в админке
 */

//*** инициализация переменных ***
$a18n['sb_name'] = 'производители';
$a18n['sc_name'] = 'категории';
$module['save_as'] = true;
$brands = mysql_select("SELECT id,name FROM shop_brands ORDER BY name",'array');
$categories = mysql_select("SELECT id,name,level FROM `shop_categories` ORDER BY left_key",'rows_id');

$filter[] = array('search');
$filter[] = array('brand',$brands,'производители');
$filter[] = array('category',$categories,'категории',true);

//пустое значение добавлем только в том случае, если могут быть товары без производителей
$brands = array('0'=>i18n('common|make_selection'))+$brands;

$table = array(
	//'_sorting'	=>	'n',
	'_edit'		=>	true,
	'id'		=>	'date:desc id name price',
	'_view'     =>  'shop_product',
	'img'		=>	'img',
	'name'		=>	'',
	'article'	=>	'',
	'brand:'	=>	$brands,
	'category'	=>	$categories,//'<a href="/admin.php?m=shop_categories&id={category}">{sc_name}</a>',
	'date'		=>	'date',
	'price'		=>	'right',
	'price2'	=>	'right',
	'rating'	=>	'text',
	'special'	=>	'boolean',
	'market'	=>	'boolean',
	'display'	=>	'boolean'
);

$join = (isset($get['category']) && $get['category']>0) ? " RIGHT JOIN shop_categories sc2 ON sc2.id = '".intval($get['category'])."' AND sc.left_key>=sc2.left_key AND sc.right_key<=sc2.right_key" : "";
$where = (isset($get['brand']) && $get['brand']>0) ? " AND shop_products.brand = '".intval($get['brand'])."' " : "";
if (isset($get['search']) && $get['search']!='') $where.= "
	AND (
		LOWER(shop_products.name) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
		OR LOWER(shop_products.article) like '%".mysql_res(mb_strtolower($get['search'],'UTF-8'))."%'
	)
";

$query = "
	SELECT
		shop_products.*,
		sc.name sc_name
	FROM
		shop_products
	LEFT JOIN shop_categories sc ON shop_products.category = sc.id
	$join
	WHERE shop_products.id IS NOT NULL $where
";

//v1.4.16 - $delete удалил confirm
function event_delete_shop_products($q) {
	//удаление отзывов
	mysql_fn('query',"DELETE FROM shop_reviews WHERE product = '" . $q['id'] . "'");
}

$tabs = array(
	1=>'Общее',
	2=>'Картинки',
	//9=>'Подтовары',
);

$form[1][] = array('input td7','name');
$form[1][] = array('checkbox','special');
$form[1][] = array('checkbox','market');
$form[1][] = array('checkbox','display');
$form[1][] = array('select td3','brand',array(
	'value'=>array(true,$brands)
));
$form[1][] = array('select td3','category',array(
	'value'=>array(true,$categories)
));
/*
$form[1][] = array('multicheckbox td3','categories',array(
	'value'=>array(true,'SELECT id,name,level FROM shop_categories ORDER BY left_key'))
);
$form[1][] = array('multiple td12','categories',array(
	'value'=>array(true,'SELECT id,name,level FROM shop_categories ORDER BY left_key'))
);
*/
$form[1][] = array('input td1 right','price');
$form[1][] = array('input td1 right','price2');
$form[1][] = array('input td1 right','rating',@$get['id']>0?array('name'=>'<a target="_blank" href="?m=shop_reviews&product='.@$get['id'].'">оценки</a>'):NULL);
$form[1][] = array('input td1','article');
$form[1][] = array('input td2','date');
$form[1][] = array('input td7','h1');
$form[1][] = array('tinymce td12','text');
$form[1][] = array('seo','seo url title description');

$form[2][] = array('file td6','img',array(
	'sizes'=>array(''=>'resize 1000x1000')
));
$form[2][] = array('file_multi','imgs',array(
	'sizes'=>array(''=>'resize 1000x1000')
));

//$form[9][] = array('file_multi_db','shop_items','Дополнительные картинки',array(''=>'resize 1000x1000','preview'=>'resize 150x150'));

//*** расширения модуля ***

//чекбоксы в таблице
include_once(ROOT_DIR.'admin/modules_extentions/shop_products_check.php');
//динамические параметры
include_once(ROOT_DIR.'admin/modules_extentions/shop_products_parameters.php');
//похожие товары
include_once(ROOT_DIR.'admin/modules_extentions/shop_products_similar.php');