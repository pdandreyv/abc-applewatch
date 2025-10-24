<?php

//404 если есть $u[4]
if ($u[4]) {
	$error++;
}
//категория и товар
elseif($u[2]) {
	//ид категории
	$id = intval(explode2('-',$u[2]));
	$category = mysql_select("
		SELECT *
		FROM shop_categories
		WHERE id = '".$id."' AND display=1
		LIMIT 1
	",'row');
	if ($category) {
		//переадреация на корректный урл
		if ($u[2] != $category['id'] . '-' . $category['url' . $lang['i']]) {
			$url = '';
			if ($u[3]) $url .= $u[3] . '/';
			if ($u[4]) $url .= $u[4] . '/';
			die(header('location: ' . get_url('shop_category', $category) . $url, true, 301));
		}
		if ($config['multilingual']) {
			$category['name'] = $category['name' . $lang['i']];
			$category['title'] = $category['title' . $lang['i']];
			$category['description'] = $category['description' . $lang['i']];
			$category['text'] = $category['text' . $lang['i']];
		}

		$abc['page'] = array_merge($abc['page'], $category);

		//вложенный breadcrumb
		$query = "
			SELECT id,name,url
			FROM shop_categories
			WHERE left_key <= " . $category['left_key'] . " AND right_key >= " . $category['right_key'] . "
			ORDER BY left_key ASC
		";
		$breadcrumb = breadcrumb($query, get_url('shop_category', array('id' => '{id}', 'url' => '{url}','url'.$lang['i'] => '{url'.$lang['i'].'}')));
		$abc['breadcrumb'] = array_merge($abc['breadcrumb'],$breadcrumb);

		// ТОВАР *******************************************************************
		if ($u[3]) {
			$id = intval(explode2('-', $u[3]));
			//запрос на товар и на категорию
			$product = mysql_select("
				SELECT *
				FROM shop_products
				WHERE display = 1 AND id = '" . $id . "'
				LIMIT 1
			", 'row');
			if ($product) {
				$product['category_url'] = $category['url'];
				if ($config['multilingual']) {
					$product['name'] = $product['name' . $lang['i']];
					$product['title'] = $product['title' . $lang['i']];
					$product['description'] = $product['description' . $lang['i']];
					$product['text'] = $product['text' . $lang['i']];
				}
				$abc['page'] = array_merge($abc['page'], $product);
				$abc['page']['parameters'] = $category['parameters'];
				//переадреация на корректный урл
				if ($u[2] != $category['id'] . '-' . $category['url' . $lang['i']]) {
					die(header('location: ' . get_url('shop_product', $product), true, 301));
				}
				$abc['layout'] = 'shop_product';
				$abc['breadcrumb'][] = array(
					'name'=>$product['name'],
					'url'=>get_url('shop_product', $product)
				);
				//v.1.2.31 open graph
				if ($abc['page']['img']) $abc['og']['image'] = get_img('shop_products', $product, 'img');
			}
			else $error++;
		}
		//КАТЕГОРИЯ
		else {
			//список подкатегорий
			$abc['category_list']=mysql_select("
				SELECT *
				FROM shop_categories
				WHERE parent = '" . $category['id'] . "' AND display = 1
				ORDER BY left_key
			",'rows');
			//список товаров если нет подкатегорий
			if ($abc['category_list']==false) {
				$abc['layout'] = 'shop_category';
				//загрузка функций для формы
				require_once(ROOT_DIR . 'functions/form_func.php');
				//определение значений формы
				$fields = array(
					'brand' => 'string_int',
					'price' => 'min_max',
				);
				$shop_parameters = false;
				if ($abc['page']['parameters']) {
					$prms = array();
					foreach (unserialize($abc['page']['parameters']) as $k => $v) if (@$v['display'] AND @$v['filter']) $prms[] = $k;
					if ($shop_parameters = mysql_select("
						SELECT * FROM shop_parameters
						WHERE display=1 AND id IN('" . implode("','", $prms) . "')
						ORDER BY `rank` DESC
					", 'rows_id')
					) {
						foreach ($shop_parameters as $k => $v) {
							if ($v['type'] == 1) $fields['p' . $k] = 'string_int';
							elseif ($v['type'] == 2) $fields['p' . $k] = 'min_max';
							elseif ($v['type'] == 3) $fields['p' . $k] = 'boolean';
							elseif ($v['type'] == 4) $fields['p' . $k] = 'string_int';
							//else $fields['p'.$k] = 'int';
						}
					}
				}

				//создание массива $post
				$post = form_smart($fields, stripslashes_smart($_GET)); //print_r($post);
				$post['shop_parameters'] = $shop_parameters;

				$where = '';
				if ($post['brand']) $where .= " AND sp.brand IN (" . $post['brand'] . ")";
				if ($post['price'] AND $price = explode('-', $post['price'])) {
					if ($price[0] > 0) $where .= " AND sp.price>=" . $price[0];
					if ($price[1] > 0) $where .= " AND sp.price<=" . $price[1];
				}
				if ($abc['page']['parameters'] AND $shop_parameters) {
					foreach ($shop_parameters as $k => $v) {
						//селект
						if ('type' == 1) {
							if ($post['p' . $k] != '')
								$where .= " AND sp.p" . $k . " IN (" . $post['p' . $k] . ")";
						} //мультичекбокс v1.2.19
						elseif ($v['type'] == 4) {
							if ($post['p' . $k] != '') {
								$array = explode(',', $post['p' . $k]);
								if (count($array) == 1) {
									$where .= " AND FIND_IN_SET (" . $post['p' . $k] . ",sp.p" . $k . ")";
								} else {
									$where2 = array();
									foreach ($array as $k1 => $v1) {
										$where2[] = "FIND_IN_SET (" . $v1 . ",sp.p" . $k . ")";
									}
									$where .= " AND ( " . implode(' OR ', $where2) . " )";
								}
							}
						} //число от и до
						elseif ($v['type'] == 2) {
							if ($post['p' . $k] != '0-0') {
								$min_max = explode('-', $post['p' . $k]);
								if ($min_max[0] > 0) $where .= " AND sp.p" . $k . ">=" . $min_max[0];
								if ($min_max[1] > 0) $where .= " AND sp.p" . $k . "<=" . $min_max[1];
							}
						} //да/нет
						elseif ($post['p' . $k] != '') {
							$where .= " AND sp.p" . $k . " = '" . $post['p' . $k] . "'";
						}
					}
				}
				//фильтр
				$abc['post'] = $post;
				//список товаров
				$query = "
					SELECT sp.*
					FROM shop_products sp
					WHERE sp.display = 1
						AND sp.category=" . $category['id'] . "
						$where
					GROUP BY sp.id
					ORDER BY sp.price
				"; //echo $query;
				$abc['products'] = mysql_data($query,false,10,@$_GET['n']);
			}
		}
	}
	else {
		$error++;
	}
}
//главная страница модуля
else {
	$abc['category_list'] = mysql_select("
		SELECT *
		FROM shop_categories
		WHERE level = 1 AND display = 1
		ORDER BY left_key
	",'rows');
}