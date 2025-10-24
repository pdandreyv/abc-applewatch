<?php

//восстановление дерева по полю parent - 5 уровня вложенности

define('ROOT_DIR', dirname(__FILE__).'/../');
require_once(ROOT_DIR.'_config2.php');//доступ к ДБ
include_once (ROOT_DIR.'functions/mysql_func.php');
include_once (ROOT_DIR.'functions/common_func.php');

$table = 'shop_categories';

$pages = mysql_select("SELECT id,parent FROM ".$table." ORDER BY left_key",'rows_id');
foreach ($pages as $k=>$v) {
	$pages2[$v['parent']][] = $v['id'];
}
//dd($pages);
//dd($pages2);
$i=1;
nested_sets_repary (0,1);
function nested_sets_repary ($key,$level) {
	global $pages2,$pages,$i,$table;
	if (isset($pages2[$key])) foreach ($pages2[$key] as $k=>$v) {
		$pages[$v]['left_key']	= $i++;
		nested_sets_repary ($v,$level+1);
		$pages[$v]['right_key']= $i++;
		$pages[$v]['level']= $level;
		//dd($pages[$v]);
		mysql_fn('update',$table,$pages[$v]);
	}
}

/*
foreach ($pages2[0] as $k1=>$v1) {
	$pages[$v1]['left_key']	= $i++;
	if (isset($pages2[$v1])) foreach ($pages2[$v1] as $k2=>$v2) {
		$pages[$v2]['left_key']	= $i++;
		if (isset($pages2[$v2])) foreach ($pages2[$v2] as $k3=>$v3) {
			$pages[$v3]['left_key']	= $i++;
			if (isset($pages2[$v3])) foreach ($pages2[$v3] as $k4=>$v4) {
				$pages[$v4]['left_key']	= $i++;
				if (isset($pages2[$v3])) foreach ($pages2[$v4] as $k5=>$v5) {
					$pages[$v5]['left_key']	= $i++;
					//тут можно встаить еще один уровень
					$pages[$v5]['right_key']= $i++;
					$pages[$v5]['level']= 5;
					mysql_fn('update',$table,$pages[$v5]);
				}
				$pages[$v4]['right_key']= $i++;
				$pages[$v4]['level']= 4;
				mysql_fn('update',$table,$pages[$v4]);
			}
			$pages[$v3]['right_key']= $i++;
			$pages[$v3]['level']= 3;
			mysql_fn('update',$table,$pages[$v3]);
		}
		$pages[$v2]['right_key']= $i++;
		$pages[$v2]['level']= 2;
		mysql_fn('update',$table,$pages[$v2]);
	}
	$pages[$v1]['right_key']= $i++;
	$pages[$v1]['level']= 1;
	mysql_fn('update',$table,$pages[$v1]);
}
*/
