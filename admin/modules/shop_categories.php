<?php

//категории
/*
 * v1.3.37 - created_at - mysql_fn
 * v1.4.7 - шаблон для параметров
 * v1.4.16 - $delete удалил confirm
 * v1.4.17 - сокращение параметров form
 */

if ($get['u']=='edit') {
	$post['parameters'] = (isset($post['parameters']) AND $post['parameters']) ? serialize($post['parameters']) : '';
}

$table = array(
	'_tree'		=>	true,
	'id'		=>	'',
	'_view'      => 'shop_category',
	//'left_key'  =>  '', 'right_key' =>  '',
	'name'		=>	'',
	'h1'        =>  '',
	'url'		=>	'',
	'display'	=>	'display',
);

$tabs = array(
	1=>'Общее',
	2=>'Настройка параметров',
);

//v1.4.16 - $delete удалил confirm
$delete = array(
	'shop_categories'	=>	'parent',
	'shop_products'		=>	'category'
);

$form[1][] = array('input td8','name');
$form[1][] = array('checkbox','display');
$form[1][] = array('input td8','h1');
$form[1][] = 'clear';
$form[1][] = array('parent td4 td4','parent');
$form[1][] = array('tinymce td12','text');
$form[1][] = array('seo','seo url title description');
$form[1][] = array('file td6','img',array(
	'name'=>'Основная картинка',
	'sizes'=>array(''=>'resize 1000x1000')
));

$form[2][] = array('parameters','parameters');