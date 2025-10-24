<?php

//отзывы
/*
 * v1.4.17 - сокращение параметров form
 */

$table = array(
	'id'		=> 'date:desc id email',
	'date'		=> '',
	'product'	=> '[{product}] {product_name}',
	'rating' 	=> '',
	'name'		=> '',
	'email'		=> '',
	//'text'		=> 'strip_tags',
	'display'	=> 'display'
);

$where = @$_GET['product']>0 ? ' AND product='.intval($_GET['product']) : '';

$query = "
	SELECT shop_reviews.*,sp.name product_name
	FROM shop_reviews
	LEFT JOIN shop_products sp ON sp.id=shop_reviews.product
	WHERE 1 $where
";

$form[] = array('input td3','name');
$form[] = array('input td3','email');
$form[] = array('input td2','date');
$form[] = array('input td1','product',array('help'=>'ID товара'));
$form[] = array('input td1','rating');
$form[] = array('checkbox','display');
$form[] = array('tinymce td12','text');