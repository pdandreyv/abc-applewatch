<?php

//редиректим при обращении на главную по url
if ($u[1]) {
	header('HTTP/1.1 301 Moved Permanently');
	die(header('location: /'));
}

//содержание главной полностью в шаблоне /includes/modules/index.php

$abc['breadcrumb'] = array();

//слайдер
$abc['slider'] = mysql_select("SELECT * FROM slider WHERE display=1 ORDER BY `rank` DESC",'rows',60*60);

//важные товары
$abc['products_index'] = mysql_select("
	SELECT sp.*,sc.url category_url, 1 as h2
	FROM shop_products sp, shop_categories sc
	WHERE sc.display=1 AND sp.category=sc.id AND sp.display = 1
	ORDER BY sp.date DESC
	LIMIT 6
",'rows',60*60);


/*
//лендинг
$query = "
	SELECT *
	FROM landing
	WHERE display = 1
	ORDER BY `rank` DESC
";
$html['content'] = html_query ('landing/landing',$query);
 */