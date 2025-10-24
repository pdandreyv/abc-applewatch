<?php

$post['special'] = 1;

include(ROOT_DIR.'admin/modules/shop_products.php');

$query = "
	SELECT
		shop_products.*,
		sc.name sc_name
	FROM
		shop_products
	LEFT JOIN shop_categories sc ON shop_products.category = sc.id
	$join
	WHERE shop_products.special=1 AND shop_products.id IS NOT NULL $where
";